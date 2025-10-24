"""
Simple prediction script - Use this to test the model after training
"""

import joblib
import numpy as np
import pandas as pd

# Load the model
print("Loading model...")
model_artifacts = joblib.load('student_performance_model.pkl')

model = model_artifacts['model']
scaler = model_artifacts['scaler']
label_encoders = model_artifacts['label_encoders']
target_encoder = model_artifacts['target_encoder']

print(f"Model: {model_artifacts['model_name']}")
print(f"Accuracy: {model_artifacts['accuracy']:.4f}\n")

def predict_student_performance(
    study_hours_per_week,
    attendance_rate,
    past_exam_scores,
    gender,
    parental_education_level,
    internet_access_at_home,
    extracurricular_activities
):
    """
    Predict student performance
    
    Parameters:
    - study_hours_per_week: float (e.g., 20)
    - attendance_rate: float (e.g., 85.5, as percentage)
    - past_exam_scores: float (e.g., 75)
    - gender: str ("Male" or "Female")
    - parental_education_level: str ("High School", "Bachelors", "Masters", or "PhD")
    - internet_access_at_home: str ("Yes" or "No")
    - extracurricular_activities: str ("Yes" or "No")
    
    Returns:
    - prediction: "Pass" or "Fail"
    - probabilities: dict with probability for each class
    """
    
    # Calculate engineered features
    study_attendance_score = study_hours_per_week * (attendance_rate / 100)
    performance_index = past_exam_scores * (attendance_rate / 100)
    
    # Encode categorical variables
    gender_encoded = label_encoders['Gender'].transform([gender])[0]
    parental_edu_encoded = label_encoders['Parental_Education_Level'].transform(
        [parental_education_level]
    )[0]
    internet_encoded = label_encoders['Internet_Access_at_Home'].transform(
        [internet_access_at_home]
    )[0]
    extracurricular_encoded = label_encoders['Extracurricular_Activities'].transform(
        [extracurricular_activities]
    )[0]
    
    # Create feature array
    features = np.array([[
        study_hours_per_week,
        attendance_rate,
        past_exam_scores,
        gender_encoded,
        parental_edu_encoded,
        internet_encoded,
        extracurricular_encoded,
        study_attendance_score,
        performance_index
    ]])
    
    # Scale features
    features_scaled = scaler.transform(features)
    
    # Make prediction
    prediction = model.predict(features_scaled)[0]
    prediction_proba = model.predict_proba(features_scaled)[0]
    
    # Get class labels
    predicted_class = target_encoder.inverse_transform([prediction])[0]
    
    # Get probabilities for each class
    probabilities = {
        target_encoder.classes_[i]: prediction_proba[i]
        for i in range(len(target_encoder.classes_))
    }
    
    return predicted_class, probabilities


# Example predictions
if __name__ == "__main__":
    print("="*60)
    print("EXAMPLE PREDICTIONS")
    print("="*60)
    
    # Example 1: Good student
    print("\n1. High-performing student:")
    print("-" * 40)
    prediction, probs = predict_student_performance(
        study_hours_per_week=25,
        attendance_rate=90,
        past_exam_scores=85,
        gender="Female",
        parental_education_level="Masters",
        internet_access_at_home="Yes",
        extracurricular_activities="Yes"
    )
    print(f"Prediction: {prediction}")
    print(f"Confidence: {max(probs.values()):.2%}")
    print(f"Probabilities: {probs}")
    
    # Example 2: At-risk student
    print("\n2. At-risk student:")
    print("-" * 40)
    prediction, probs = predict_student_performance(
        study_hours_per_week=10,
        attendance_rate=65,
        past_exam_scores=55,
        gender="Male",
        parental_education_level="High School",
        internet_access_at_home="No",
        extracurricular_activities="No"
    )
    print(f"Prediction: {prediction}")
    print(f"Confidence: {max(probs.values()):.2%}")
    print(f"Probabilities: {probs}")
    
    # Example 3: Average student
    print("\n3. Average student:")
    print("-" * 40)
    prediction, probs = predict_student_performance(
        study_hours_per_week=18,
        attendance_rate=78,
        past_exam_scores=72,
        gender="Male",
        parental_education_level="Bachelors",
        internet_access_at_home="Yes",
        extracurricular_activities="No"
    )
    print(f"Prediction: {prediction}")
    print(f"Confidence: {max(probs.values()):.2%}")
    print(f"Probabilities: {probs}")
    
    print("\n" + "="*60)
    print("\nYou can modify the parameters above to test different scenarios!")
