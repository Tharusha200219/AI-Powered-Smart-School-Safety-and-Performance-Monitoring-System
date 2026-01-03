"""
Model Training Module
Trains machine learning models to predict student performance

This module:
1. Loads cleaned data
2. Prepares features and labels
3. Trains Linear Regression model for each subject
4. Evaluates model performance
5. Saves trained models for prediction
"""

import pandas as pd
import numpy as np
import os
import sys
import joblib
from sklearn.model_selection import train_test_split
from sklearn.linear_model import LinearRegression
from sklearn.preprocessing import StandardScaler, LabelEncoder
from sklearn.metrics import mean_absolute_error, mean_squared_error, r2_score

# Add parent directory to path
sys.path.append(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
from config.config import CLEANED_DATA_PATH, MODELS_DIR, MODEL_PATH, SCALER_PATH, RANDOM_STATE, TEST_SIZE


class PerformancePredictor:
    """Train and manage student performance prediction models"""
    
    def __init__(self, data_path=CLEANED_DATA_PATH):
        """
        Initialize the predictor
        
        Args:
            data_path: Path to cleaned dataset
        """
        self.data_path = data_path
        self.df = None
        self.models = {}
        self.scaler = StandardScaler()
        self.label_encoder = LabelEncoder()
        self.feature_columns = ['age', 'grade', 'attendance', 'marks', 'subject_encoded']
        
    def load_data(self):
        """Load cleaned dataset"""
        print(f"Loading cleaned data from: {self.data_path}")
        self.df = pd.read_csv(self.data_path)
        print(f"Loaded {len(self.df)} records")
        print(f"Columns: {list(self.df.columns)}")
        return self
        
    def prepare_features(self):
        """
        Prepare features for training:
        - Encode categorical variables
        - Create feature matrix X and target vector y
        """
        print("\n=== Preparing Features ===")
        
        # Encode subject names to numbers
        self.df['subject_encoded'] = self.label_encoder.fit_transform(self.df['subject'])
        
        # Features: age, grade, attendance, marks, subject
        X = self.df[self.feature_columns].values
        
        # Target: future performance
        y = self.df['future_performance'].values
        
        print(f"Features shape: {X.shape}")
        print(f"Target shape: {y.shape}")
        print(f"Subject encoding: {dict(zip(self.label_encoder.classes_, range(len(self.label_encoder.classes_))))}")
        
        return X, y
        
    def train_model(self, X, y):
        """
        Train Linear Regression model
        
        Args:
            X: Feature matrix
            y: Target vector
            
        Returns:
            Trained model and evaluation metrics
        """
        print("\n=== Training Model ===")
        print("Algorithm: Linear Regression")
        print(f"Train/Test split: {int((1-TEST_SIZE)*100)}% / {int(TEST_SIZE*100)}%")
        
        # Split data into training and testing sets
        X_train, X_test, y_train, y_test = train_test_split(
            X, y, test_size=TEST_SIZE, random_state=RANDOM_STATE
        )
        
        print(f"\nTraining samples: {len(X_train)}")
        print(f"Testing samples: {len(X_test)}")
        
        # Scale features
        X_train_scaled = self.scaler.fit_transform(X_train)
        X_test_scaled = self.scaler.transform(X_test)
        
        # Train Linear Regression model
        model = LinearRegression()
        model.fit(X_train_scaled, y_train)
        
        # Make predictions
        y_train_pred = model.predict(X_train_scaled)
        y_test_pred = model.predict(X_test_scaled)
        
        # Evaluate model
        metrics = self.evaluate_model(y_train, y_train_pred, y_test, y_test_pred)
        
        # Print feature importance (coefficients)
        print("\n=== Feature Importance (Coefficients) ===")
        for feature, coef in zip(self.feature_columns, model.coef_):
            print(f"{feature}: {coef:.4f}")
        print(f"Intercept: {model.intercept_:.4f}")
        
        return model, metrics
        
    def evaluate_model(self, y_train, y_train_pred, y_test, y_test_pred):
        """
        Evaluate model performance
        
        Args:
            y_train, y_train_pred: Training actual and predicted values
            y_test, y_test_pred: Testing actual and predicted values
            
        Returns:
            Dictionary of evaluation metrics
        """
        print("\n=== Model Evaluation ===")
        
        # Training metrics
        train_mae = mean_absolute_error(y_train, y_train_pred)
        train_rmse = np.sqrt(mean_squared_error(y_train, y_train_pred))
        train_r2 = r2_score(y_train, y_train_pred)
        
        # Testing metrics
        test_mae = mean_absolute_error(y_test, y_test_pred)
        test_rmse = np.sqrt(mean_squared_error(y_test, y_test_pred))
        test_r2 = r2_score(y_test, y_test_pred)
        
        print("\nTraining Set Performance:")
        print(f"  Mean Absolute Error (MAE): {train_mae:.4f}")
        print(f"  Root Mean Squared Error (RMSE): {train_rmse:.4f}")
        print(f"  R² Score: {train_r2:.4f}")
        
        print("\nTest Set Performance:")
        print(f"  Mean Absolute Error (MAE): {test_mae:.4f}")
        print(f"  Root Mean Squared Error (RMSE): {test_rmse:.4f}")
        print(f"  R² Score: {test_r2:.4f}")
        
        metrics = {
            'train_mae': train_mae,
            'train_rmse': train_rmse,
            'train_r2': train_r2,
            'test_mae': test_mae,
            'test_rmse': test_rmse,
            'test_r2': test_r2
        }
        
        return metrics
        
    def save_models(self, model):
        """
        Save trained model and scaler
        
        Args:
            model: Trained model to save
        """
        print("\n=== Saving Models ===")
        
        # Create models directory if it doesn't exist
        os.makedirs(MODELS_DIR, exist_ok=True)
        
        # Save model
        joblib.dump(model, MODEL_PATH)
        print(f"✓ Model saved to: {MODEL_PATH}")
        
        # Save scaler
        joblib.dump(self.scaler, SCALER_PATH)
        print(f"✓ Scaler saved to: {SCALER_PATH}")
        
        # Save label encoder
        encoder_path = os.path.join(MODELS_DIR, 'label_encoder.pkl')
        joblib.dump(self.label_encoder, encoder_path)
        print(f"✓ Label encoder saved to: {encoder_path}")
        
        return self
        
    def train_and_save(self):
        """Execute complete training pipeline"""
        # Load and prepare data
        self.load_data()
        X, y = self.prepare_features()
        
        # Train model
        model, metrics = self.train_model(X, y)
        
        # Save model
        self.save_models(model)
        
        return model, metrics


def main():
    """Main execution function"""
    print("=" * 60)
    print("STUDENT PERFORMANCE PREDICTION MODEL TRAINING")
    print("=" * 60)
    
    # Initialize predictor
    predictor = PerformancePredictor()
    
    # Train and save model
    model, metrics = predictor.train_and_save()
    
    print("\n" + "=" * 60)
    print("✓ Model training completed successfully!")
    print(f"✓ Test R² Score: {metrics['test_r2']:.4f}")
    print(f"✓ Test MAE: {metrics['test_mae']:.4f}")
    print("=" * 60)


if __name__ == "__main__":
    main()
