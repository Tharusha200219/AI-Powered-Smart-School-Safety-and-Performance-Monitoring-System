"""
Training Script for 6000-Record Dataset
Trains the student performance prediction model with the full 6000-record dataset

Dataset Structure:
- Attendance: Attendance percentage (0-100)
- Term1: First term marks (0-100)
- Term2: Second term marks (0-100)
- Term3: Third term marks (0-100)
- PredictedScore: Target variable (future performance)

This script:
1. Loads the 6000-record dataset
2. Expands it to create subject-wise records (Mathematics, Science, English, etc.)
3. Engineers features (momentum, volatility, attendance interactions)
4. Trains XGBoost model with cross-validation
5. Saves trained model with metrics
"""

import pandas as pd
import numpy as np
import os
import sys
import joblib
from sklearn.model_selection import train_test_split, cross_val_score
from sklearn.preprocessing import StandardScaler, OneHotEncoder
from sklearn.metrics import mean_absolute_error, mean_squared_error, r2_score
from xgboost import XGBRegressor
import json

# Configuration
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
DATASET_PATH = os.path.join(BASE_DIR, 'dataset', 'student_performance_6000_with_prediction.csv')
MODELS_DIR = os.path.join(BASE_DIR, 'models')
DATA_DIR = os.path.join(BASE_DIR, 'data')

# Create directories if they don't exist
os.makedirs(MODELS_DIR, exist_ok=True)
os.makedirs(DATA_DIR, exist_ok=True)

# Model configuration
RANDOM_STATE = 42
TEST_SIZE = 0.2
CV_FOLDS = 5

# XGBoost parameters
XGB_PARAMS = {
    'n_estimators': 500,
    'learning_rate': 0.05,
    'max_depth': 8,
    'subsample': 0.8,
    'colsample_bytree': 0.8,
    'random_state': RANDOM_STATE,
    'n_jobs': -1
}


class PerformanceModelTrainer:
    """Train student performance prediction model"""
    
    def __init__(self):
        self.model = None
        self.scaler = StandardScaler()
        self.subject_encoder = OneHotEncoder(sparse_output=False, handle_unknown='ignore')
        self.feature_order = None
        
        # Feature definitions
        self.numerical_features = ['age', 'grade', 'attendance', 'term1_marks', 'term2_marks', 'term3_marks']
        self.engineered_features = [
            'attendance_score', 'grade_marks_ratio', 'marks_avg', 
            'marks_delta', 'marks_slope', 'marks_volatility', 
            'is_crashing', 'performance_momentum', 'attendance_marks_interaction'
        ]
    
    def load_and_expand_data(self):
        """
        Load the 6000-record dataset and expand it with subject-wise records
        
        Returns:
            DataFrame with expanded records including subjects
        """
        print("=" * 60)
        print("LOADING 6000-RECORD DATASET")
        print("=" * 60)
        
        # Load dataset
        df = pd.read_csv(DATASET_PATH)
        print(f"✓ Loaded {len(df)} records")
        print(f"  Columns: {list(df.columns)}")
        print(f"\nSample data:")
        print(df.head())
        
        # Define subjects (common school subjects)
        subjects = ['Mathematics', 'Science', 'English', 'History', 'Geography']
        
        # Expand each record to multiple subjects
        # Each student will have records for different subjects with slight variations
        print(f"\n{'=' * 60}")
        print(f"EXPANDING TO SUBJECT-WISE RECORDS")
        print(f"{'=' * 60}")
        
        expanded_records = []
        
        for idx, row in df.iterrows():
            student_id = idx + 1  # Create student ID
            attendance = row['Attendance']
            term1 = row['Term1']
            term2 = row['Term2']
            term3 = row['Term3']
            predicted_score = row['PredictedScore']
            
            # Derive age and grade (simulate realistic distributions)
            # Grades 8-12 (ages 13-17)
            grade = np.random.randint(8, 13)
            age = grade + 5  # Approximate age
            
            # Create a record for each subject with slight variations
            for subject in subjects:
                # Add small random variations to make subjects different
                # This simulates that students perform differently in different subjects
                variation = np.random.uniform(-3, 3)
                
                subject_t1 = max(0, min(100, term1 + variation))
                subject_t2 = max(0, min(100, term2 + variation))
                subject_t3 = max(0, min(100, term3 + variation))
                subject_attendance = max(0, min(100, attendance + np.random.uniform(-2, 2)))
                
                # Target (future performance) with slight variation
                subject_predicted = max(0, min(100, predicted_score + variation))
                
                expanded_records.append({
                    'student_id': student_id,
                    'age': age,
                    'grade': grade,
                    'subject': subject,
                    'attendance': subject_attendance,
                    'term1_marks': subject_t1,
                    'term2_marks': subject_t2,
                    'term3_marks': subject_t3,
                    'future_performance': subject_predicted
                })
        
        expanded_df = pd.DataFrame(expanded_records)
        print(f"✓ Expanded to {len(expanded_df)} subject-wise records")
        print(f"  Students: {expanded_df['student_id'].nunique()}")
        print(f"  Subjects: {expanded_df['subject'].nunique()}")
        
        return expanded_df
    
    def engineer_features(self, df):
        """
        Create engineered features
        
        Args:
            df: DataFrame with base features
            
        Returns:
            DataFrame with engineered features added
        """
        print(f"\n{'=' * 60}")
        print("ENGINEERING FEATURES")
        print(f"{'=' * 60}")
        
        # Attendance score (normalized)
        df['attendance_score'] = df['attendance'] / 100.0
        
        # Grade-marks ratio
        df['grade_marks_ratio'] = df['term3_marks'] / np.maximum(df['grade'], 1)
        
        # Marks average across terms
        df['marks_avg'] = (df['term1_marks'] + df['term2_marks'] + df['term3_marks']) / 3.0
        
        # Marks delta (recent change)
        df['marks_delta'] = df['term3_marks'] - df['term2_marks']
        
        # Marks slope (overall trend)
        df['marks_slope'] = (df['term3_marks'] - df['term1_marks']) / 2.0
        
        # Marks volatility (standard deviation)
        df['marks_volatility'] = df.apply(
            lambda row: np.std([row['term1_marks'], row['term2_marks'], row['term3_marks']]),
            axis=1
        )
        
        # Is crashing (sudden drop)
        df['is_crashing'] = ((df['term2_marks'] - df['term3_marks']) > 30).astype(int)
        
        # Performance momentum
        df['performance_momentum'] = (df['term3_marks'] * df['attendance_score']) / 100.0
        
        # Attendance-marks interaction
        df['attendance_marks_interaction'] = df['attendance_score'] * df['term3_marks']
        
        print(f"✓ Created {len(self.engineered_features)} engineered features")
        print(f"  Features: {self.engineered_features}")
        
        return df
    
    def prepare_features(self, df):
        """
        Prepare features for training
        
        Args:
            df: DataFrame with all features
            
        Returns:
            X: Feature matrix
            y: Target vector
        """
        print(f"\n{'=' * 60}")
        print("PREPARING FEATURES")
        print(f"{'=' * 60}")
        
        # Get numerical features
        numerical_data = df[self.numerical_features + self.engineered_features].values
        
        # One-Hot encode subjects
        subjects = df['subject'].values.reshape(-1, 1)
        subject_encoded = self.subject_encoder.fit_transform(subjects)
        subject_names = self.subject_encoder.get_feature_names_out(['subject'])
        
        # Combine features
        X = np.hstack([numerical_data, subject_encoded])
        
        # Store feature order
        self.feature_order = self.numerical_features + self.engineered_features + list(subject_names)
        
        # Target
        y = df['future_performance'].values
        
        print(f"✓ Feature matrix shape: {X.shape}")
        print(f"✓ Target shape: {y.shape}")
        print(f"✓ Total features: {len(self.feature_order)}")
        
        return X, y
    
    def train_model(self, X, y):
        """
        Train XGBoost model with cross-validation
        
        Args:
            X: Feature matrix
            y: Target vector
        """
        print(f"\n{'=' * 60}")
        print("TRAINING XGBOOST MODEL")
        print(f"{'=' * 60}")
        
        # Split data
        X_train, X_test, y_train, y_test = train_test_split(
            X, y, test_size=TEST_SIZE, random_state=RANDOM_STATE
        )
        
        print(f"Training set: {X_train.shape[0]} samples")
        print(f"Test set: {X_test.shape[0]} samples")
        
        # Scale features
        X_train_scaled = self.scaler.fit_transform(X_train)
        X_test_scaled = self.scaler.transform(X_test)
        
        # Initialize and train model
        print(f"\nTraining with parameters:")
        for key, value in XGB_PARAMS.items():
            print(f"  {key}: {value}")
        
        self.model = XGBRegressor(**XGB_PARAMS)
        self.model.fit(X_train_scaled, y_train)
        
        print(f"\n✓ Model training completed!")
        
        # Evaluate
        print(f"\n{'=' * 60}")
        print("MODEL EVALUATION")
        print(f"{'=' * 60}")
        
        # Training predictions
        y_train_pred = self.model.predict(X_train_scaled)
        train_mae = mean_absolute_error(y_train, y_train_pred)
        train_rmse = np.sqrt(mean_squared_error(y_train, y_train_pred))
        train_r2 = r2_score(y_train, y_train_pred)
        
        # Test predictions
        y_test_pred = self.model.predict(X_test_scaled)
        test_mae = mean_absolute_error(y_test, y_test_pred)
        test_rmse = np.sqrt(mean_squared_error(y_test, y_test_pred))
        test_r2 = r2_score(y_test, y_test_pred)
        
        # Cross-validation
        cv_scores = cross_val_score(
            self.model, X_train_scaled, y_train,
            cv=CV_FOLDS, scoring='neg_mean_absolute_error', n_jobs=-1
        )
        cv_mae = -cv_scores.mean()
        cv_std = cv_scores.std()
        
        print(f"\n📊 TRAINING METRICS:")
        print(f"   MAE:  {train_mae:.2f}")
        print(f"   RMSE: {train_rmse:.2f}")
        print(f"   R²:   {train_r2:.4f}")
        
        print(f"\n📊 TEST METRICS:")
        print(f"   MAE:  {test_mae:.2f}")
        print(f"   RMSE: {test_rmse:.2f}")
        print(f"   R²:   {test_r2:.4f}")
        
        print(f"\n📊 CROSS-VALIDATION ({CV_FOLDS}-FOLD):")
        print(f"   MAE:  {cv_mae:.2f} (±{cv_std:.2f})")
        
        # Save metrics
        metrics = {
            'training': {
                'mae': float(train_mae),
                'rmse': float(train_rmse),
                'r2': float(train_r2)
            },
            'test': {
                'mae': float(test_mae),
                'rmse': float(test_rmse),
                'r2': float(test_r2)
            },
            'cross_validation': {
                'mae': float(cv_mae),
                'std': float(cv_std),
                'folds': CV_FOLDS
            },
            'dataset_size': {
                'total': len(X),
                'training': len(X_train),
                'test': len(X_test)
            }
        }
        
        metrics_path = os.path.join(BASE_DIR, 'model_accuracy_results.json')
        with open(metrics_path, 'w') as f:
            json.dump(metrics, f, indent=2)
        print(f"\n✓ Metrics saved to: {metrics_path}")
        
        return metrics
    
    def save_models(self):
        """Save trained model and preprocessors"""
        print(f"\n{'=' * 60}")
        print("SAVING MODELS")
        print(f"{'=' * 60}")
        
        # Save model
        model_path = os.path.join(MODELS_DIR, 'performance_predictor.pkl')
        joblib.dump(self.model, model_path)
        print(f"✓ Model saved: {model_path}")
        
        # Save scaler
        scaler_path = os.path.join(MODELS_DIR, 'scaler.pkl')
        joblib.dump(self.scaler, scaler_path)
        print(f"✓ Scaler saved: {scaler_path}")
        
        # Save encoder
        encoder_path = os.path.join(MODELS_DIR, 'subject_encoder.pkl')
        joblib.dump(self.subject_encoder, encoder_path)
        print(f"✓ Encoder saved: {encoder_path}")
        
        # Save feature order
        feature_order_path = os.path.join(MODELS_DIR, 'feature_order.pkl')
        joblib.dump(self.feature_order, feature_order_path)
        print(f"✓ Feature order saved: {feature_order_path}")


def main():
    """Main training pipeline"""
    print("\n" + "=" * 60)
    print("STUDENT PERFORMANCE PREDICTION MODEL TRAINING")
    print("6000-Record Dataset")
    print("=" * 60 + "\n")
    
    # Initialize trainer
    trainer = PerformanceModelTrainer()
    
    # Step 1: Load and expand data
    df = trainer.load_and_expand_data()
    
    # Step 2: Engineer features
    df = trainer.engineer_features(df)
    
    # Step 3: Prepare features
    X, y = trainer.prepare_features(df)
    
    # Step 4: Train model
    metrics = trainer.train_model(X, y)
    
    # Step 5: Save models
    trainer.save_models()
    
    # Summary
    print(f"\n{'=' * 60}")
    print("🎉 TRAINING COMPLETED SUCCESSFULLY!")
    print(f"{'=' * 60}")
    print(f"\n✓ Dataset: 6000 original records")
    print(f"✓ Expanded: {len(df)} subject-wise records")
    print(f"✓ Test MAE: {metrics['test']['mae']:.2f}")
    print(f"✓ Test R²: {metrics['test']['r2']:.4f}")
    print(f"\nModel ready for predictions! 🚀")
    print("Start the API with: ./start_api.sh")
    print("=" * 60 + "\n")


if __name__ == '__main__':
    main()
