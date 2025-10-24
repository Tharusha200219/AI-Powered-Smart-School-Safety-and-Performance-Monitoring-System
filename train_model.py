"""
Student Performance Prediction Model
This model predicts student future academic performance based on:
- Attendance Rate
- Study Hours
- Past Exam Scores
- Other demographic and behavioral factors
"""

import pandas as pd
import numpy as np
from sklearn.model_selection import train_test_split, cross_val_score, GridSearchCV
from sklearn.preprocessing import LabelEncoder, StandardScaler
from sklearn.ensemble import RandomForestClassifier, GradientBoostingClassifier
from sklearn.metrics import classification_report, confusion_matrix, accuracy_score
import joblib
import warnings
warnings.filterwarnings('ignore')

# Load the primary dataset
print("Loading dataset...")
df = pd.read_csv('data/student_performance_dataset 2.csv')

print(f"Dataset shape: {df.shape}")
print("\nFirst few rows:")
print(df.head())
print("\nDataset info:")
print(df.info())
print("\nMissing values:")
print(df.isnull().sum())

# Data Preprocessing
print("\n" + "="*50)
print("PREPROCESSING DATA")
print("="*50)

# Handle missing values if any
df = df.dropna()

# Feature Engineering
# Create additional features that might be useful
df['Study_Attendance_Score'] = df['Study_Hours_per_Week'] * (df['Attendance_Rate'] / 100)
df['Performance_Index'] = df['Past_Exam_Scores'] * (df['Attendance_Rate'] / 100)

# Encode categorical variables
label_encoders = {}
categorical_columns = ['Gender', 'Parental_Education_Level', 'Internet_Access_at_Home', 
                       'Extracurricular_Activities']

for col in categorical_columns:
    le = LabelEncoder()
    df[col + '_encoded'] = le.fit_transform(df[col])
    label_encoders[col] = le

# Define features for the model
feature_columns = [
    'Study_Hours_per_Week',
    'Attendance_Rate',
    'Past_Exam_Scores',
    'Gender_encoded',
    'Parental_Education_Level_encoded',
    'Internet_Access_at_Home_encoded',
    'Extracurricular_Activities_encoded',
    'Study_Attendance_Score',
    'Performance_Index'
]

X = df[feature_columns]
y = df['Pass_Fail']

# Encode target variable
le_target = LabelEncoder()
y_encoded = le_target.fit_transform(y)

print("\nFeature columns:")
for col in feature_columns:
    print(f"  - {col}")

print(f"\nTarget classes: {le_target.classes_}")
print(f"Class distribution:\n{pd.Series(y).value_counts()}")

# Split the data
X_train, X_test, y_train, y_test = train_test_split(
    X, y_encoded, test_size=0.2, random_state=42, stratify=y_encoded
)

# Feature Scaling
scaler = StandardScaler()
X_train_scaled = scaler.fit_transform(X_train)
X_test_scaled = scaler.transform(X_test)

print(f"\nTraining set size: {X_train.shape}")
print(f"Testing set size: {X_test.shape}")

# Model Training
print("\n" + "="*50)
print("TRAINING MODELS")
print("="*50)

# Model 1: Random Forest with GridSearch
print("\n1. Training Random Forest Classifier...")
rf_params = {
    'n_estimators': [100, 200, 300],
    'max_depth': [10, 20, 30, None],
    'min_samples_split': [2, 5, 10],
    'min_samples_leaf': [1, 2, 4]
}

rf = RandomForestClassifier(random_state=42)
rf_grid = GridSearchCV(rf, rf_params, cv=5, scoring='accuracy', n_jobs=-1, verbose=1)
rf_grid.fit(X_train_scaled, y_train)

print(f"Best Random Forest parameters: {rf_grid.best_params_}")
print(f"Best CV score: {rf_grid.best_score_:.4f}")

# Model 2: Gradient Boosting
print("\n2. Training Gradient Boosting Classifier...")
gb_params = {
    'n_estimators': [100, 200],
    'learning_rate': [0.01, 0.1, 0.2],
    'max_depth': [3, 5, 7],
    'min_samples_split': [2, 5]
}

gb = GradientBoostingClassifier(random_state=42)
gb_grid = GridSearchCV(gb, gb_params, cv=5, scoring='accuracy', n_jobs=-1, verbose=1)
gb_grid.fit(X_train_scaled, y_train)

print(f"Best Gradient Boosting parameters: {gb_grid.best_params_}")
print(f"Best CV score: {gb_grid.best_score_:.4f}")

# Compare models
print("\n" + "="*50)
print("MODEL EVALUATION")
print("="*50)

models = {
    'Random Forest': rf_grid.best_estimator_,
    'Gradient Boosting': gb_grid.best_estimator_
}

best_model = None
best_accuracy = 0
best_model_name = ""

for name, model in models.items():
    y_pred = model.predict(X_test_scaled)
    accuracy = accuracy_score(y_test, y_pred)
    
    print(f"\n{name}:")
    print(f"Accuracy: {accuracy:.4f}")
    print("\nClassification Report:")
    print(classification_report(y_test, y_pred, target_names=le_target.classes_))
    print("\nConfusion Matrix:")
    print(confusion_matrix(y_test, y_pred))
    
    if accuracy > best_accuracy:
        best_accuracy = accuracy
        best_model = model
        best_model_name = name

print("\n" + "="*50)
print(f"BEST MODEL: {best_model_name}")
print(f"ACCURACY: {best_accuracy:.4f}")
print("="*50)

# Feature Importance
if hasattr(best_model, 'feature_importances_'):
    feature_importance = pd.DataFrame({
        'feature': feature_columns,
        'importance': best_model.feature_importances_
    }).sort_values('importance', ascending=False)
    
    print("\nFeature Importance:")
    print(feature_importance)

# Save the model and preprocessing objects
print("\n" + "="*50)
print("SAVING MODEL AND PREPROCESSORS")
print("="*50)

# Save everything needed for prediction
model_artifacts = {
    'model': best_model,
    'scaler': scaler,
    'label_encoders': label_encoders,
    'target_encoder': le_target,
    'feature_columns': feature_columns,
    'model_name': best_model_name,
    'accuracy': best_accuracy
}

joblib.dump(model_artifacts, 'student_performance_model.pkl')
print("✓ Model saved as 'student_performance_model.pkl'")

# Save a lightweight version with just the essentials
joblib.dump(best_model, 'model_only.pkl')
joblib.dump(scaler, 'scaler.pkl')
joblib.dump(label_encoders, 'label_encoders.pkl')
joblib.dump(le_target, 'target_encoder.pkl')

print("✓ Individual components saved")
print(f"\nModel training completed successfully!")
print(f"Final Model Accuracy: {best_accuracy:.4f} ({best_accuracy*100:.2f}%)")
