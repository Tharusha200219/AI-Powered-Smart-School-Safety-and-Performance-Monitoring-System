"""
Prediction Engine Module
Makes predictions for student performance using trained models

This module:
1. Loads trained models
2. Prepares input data
3. Makes predictions for each subject
4. Returns formatted predictions with trends
"""

import numpy as np
import joblib
import os
import sys

# Add parent directory to path
sys.path.append(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
from config.config import MODEL_PATH, SCALER_PATH, MODELS_DIR


class StudentPerformancePredictor:
    """Make predictions for student performance"""
    
    def __init__(self):
        """Initialize predictor with trained models"""
        self.model = None
        self.scaler = None
        self.label_encoder = None
        self.load_models()
        
    def load_models(self):
        """Load trained model, scaler, and label encoder"""
        try:
            self.model = joblib.load(MODEL_PATH)
            self.scaler = joblib.load(SCALER_PATH)
            encoder_path = os.path.join(MODELS_DIR, 'label_encoder.pkl')
            self.label_encoder = joblib.load(encoder_path)
            print("✓ Models loaded successfully")
        except Exception as e:
            print(f"Error loading models: {e}")
            raise
            
    def prepare_input(self, student_data):
        """
        Prepare student data for prediction
        
        Args:
            student_data: Dictionary containing:
                - age: Student age
                - grade: Grade level
                - subjects: List of dictionaries with subject_name, attendance, marks
                
        Returns:
            Prepared feature matrix
        """
        age = student_data.get('age', 15)
        grade = student_data.get('grade', 10)
        subjects = student_data.get('subjects', [])
        
        features = []
        subject_names = []
        
        for subject in subjects:
            subject_name = subject.get('subject_name', 'Unknown')
            attendance = subject.get('attendance', 0)
            marks = subject.get('marks', 0)
            
            # Encode subject name
            try:
                subject_encoded = self.label_encoder.transform([subject_name])[0]
            except:
                # If subject not in training data, use first subject as default
                subject_encoded = 0
            
            # Create feature vector: [age, grade, attendance, marks, subject_encoded]
            feature_vector = [age, grade, attendance, marks, subject_encoded]
            features.append(feature_vector)
            subject_names.append(subject_name)
            
        return np.array(features), subject_names
        
    def predict(self, student_data):
        """
        Predict performance for all subjects
        
        Args:
            student_data: Dictionary with student information
            
        Returns:
            List of predictions for each subject
        """
        # Prepare input features
        X, subject_names = self.prepare_input(student_data)
        
        if len(X) == 0:
            return []
        
        # Scale features
        X_scaled = self.scaler.transform(X)
        
        # Make predictions
        predictions = self.model.predict(X_scaled)
        
        # Format results
        results = []
        subjects = student_data.get('subjects', [])
        
        for i, (subject_name, predicted_performance) in enumerate(zip(subject_names, predictions)):
            current_marks = subjects[i].get('marks', 0)
            attendance = subjects[i].get('attendance', 0)
            
            # Determine trend based on current performance quality
            if current_marks >= 80:
                trend = "improving"
            elif current_marks >= 60:
                trend = "stable"
            else:
                trend = "declining"
            
            # Calculate confidence based on attendance
            confidence = min(0.95, 0.5 + (attendance / 200))
            
            # Determine performance category
            if predicted_performance >= 85:
                category = "Excellent"
            elif predicted_performance >= 70:
                category = "Good"
            elif predicted_performance >= 55:
                category = "Average"
            else:
                category = "Needs Improvement"
            
            result = {
                'subject': subject_name,
                'current_performance': round(float(current_marks), 2),
                'current_attendance': round(float(attendance), 2),
                'predicted_performance': round(float(predicted_performance), 2),
                'prediction_trend': trend,
                'performance_category': category,
                'confidence': round(float(confidence), 2),
                'recommendation': self.generate_recommendation(attendance, current_marks, predicted_performance)
            }
            
            results.append(result)
            
        return results
        
    def generate_recommendation(self, attendance, current_marks, predicted_performance):
        """
        Generate personalized recommendation
        
        Args:
            attendance: Current attendance percentage
            current_marks: Current marks
            predicted_performance: Predicted future performance
            
        Returns:
            Recommendation string
        """
        recommendations = []
        
        if attendance < 75:
            recommendations.append("Improve attendance to at least 75%")
        
        if current_marks < 60:
            recommendations.append("Focus on fundamental concepts and seek additional help")
        elif current_marks < 75:
            recommendations.append("Regular practice and revision recommended")
        
        if predicted_performance < current_marks:
            recommendations.append("Extra attention needed to maintain current performance")
        elif predicted_performance > current_marks + 10:
            recommendations.append("Great potential! Keep up the good work")
        
        if not recommendations:
            recommendations.append("Continue with current study approach")
            
        return " | ".join(recommendations)


def test_predictor():
    """Test the predictor with sample data"""
    print("=" * 60)
    print("TESTING STUDENT PERFORMANCE PREDICTOR")
    print("=" * 60)
    
    predictor = StudentPerformancePredictor()
    
    # Sample student data
    sample_data = {
        'student_id': 123,
        'age': 15,
        'grade': 10,
        'subjects': [
            {
                'subject_name': 'Mathematics',
                'attendance': 85.5,
                'marks': 78.0
            },
            {
                'subject_name': 'Science',
                'attendance': 90.0,
                'marks': 82.0
            },
            {
                'subject_name': 'English',
                'attendance': 70.0,
                'marks': 65.0
            }
        ]
    }
    
    # Make predictions
    predictions = predictor.predict(sample_data)
    
    print(f"\nPredictions for Student {sample_data['student_id']}:")
    print("-" * 60)
    
    for pred in predictions:
        print(f"\nSubject: {pred['subject']}")
        print(f"  Current Performance: {pred['current_performance']}")
        print(f"  Current Attendance: {pred['current_attendance']}%")
        print(f"  Predicted Performance: {pred['predicted_performance']}")
        print(f"  Trend: {pred['prediction_trend']}")
        print(f"  Category: {pred['performance_category']}")
        print(f"  Confidence: {pred['confidence']}")
        print(f"  Recommendation: {pred['recommendation']}")
    
    print("\n" + "=" * 60)
    print("✓ Test completed successfully!")
    print("=" * 60)


if __name__ == "__main__":
    test_predictor()
