"""
Prediction API for Student Performance Model
This Flask API can be integrated with your Laravel application
"""

from flask import Flask, request, jsonify
from flask_cors import CORS
import joblib
import numpy as np
import pandas as pd

app = Flask(__name__)
CORS(app)  # Enable CORS for Laravel integration

# Load model and preprocessors
print("Loading model...")
model_artifacts = joblib.load('student_performance_model.pkl')

model = model_artifacts['model']
scaler = model_artifacts['scaler']
label_encoders = model_artifacts['label_encoders']
target_encoder = model_artifacts['target_encoder']
feature_columns = model_artifacts['feature_columns']

print(f"Model loaded: {model_artifacts['model_name']}")
print(f"Accuracy: {model_artifacts['accuracy']:.4f}")

@app.route('/health', methods=['GET'])
def health():
    """Health check endpoint"""
    return jsonify({
        'status': 'healthy',
        'model': model_artifacts['model_name'],
        'accuracy': float(model_artifacts['accuracy'])
    })

@app.route('/predict', methods=['POST'])
def predict():
    """
    Predict student performance
    
    Expected JSON input:
    {
        "study_hours_per_week": 25,
        "attendance_rate": 85.5,
        "past_exam_scores": 75,
        "gender": "Male",
        "parental_education_level": "Bachelors",
        "internet_access_at_home": "Yes",
        "extracurricular_activities": "Yes"
    }
    """
    try:
        data = request.get_json()
        
        # Validate required fields
        required_fields = [
            'study_hours_per_week',
            'attendance_rate',
            'past_exam_scores',
            'gender',
            'parental_education_level',
            'internet_access_at_home',
            'extracurricular_activities'
        ]
        
        missing_fields = [field for field in required_fields if field not in data]
        if missing_fields:
            return jsonify({
                'error': 'Missing required fields',
                'missing_fields': missing_fields
            }), 400
        
        # Prepare features
        study_hours = float(data['study_hours_per_week'])
        attendance_rate = float(data['attendance_rate'])
        past_exam_scores = float(data['past_exam_scores'])
        
        # Calculate engineered features
        study_attendance_score = study_hours * (attendance_rate / 100)
        performance_index = past_exam_scores * (attendance_rate / 100)
        
        # Encode categorical variables
        gender_encoded = label_encoders['Gender'].transform([data['gender']])[0]
        parental_edu_encoded = label_encoders['Parental_Education_Level'].transform(
            [data['parental_education_level']]
        )[0]
        internet_encoded = label_encoders['Internet_Access_at_Home'].transform(
            [data['internet_access_at_home']]
        )[0]
        extracurricular_encoded = label_encoders['Extracurricular_Activities'].transform(
            [data['extracurricular_activities']]
        )[0]
        
        # Create feature array
        features = np.array([[
            study_hours,
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
            target_encoder.classes_[i]: float(prediction_proba[i])
            for i in range(len(target_encoder.classes_))
        }
        
        return jsonify({
            'prediction': predicted_class,
            'probabilities': probabilities,
            'confidence': float(max(prediction_proba)),
            'input_data': data
        })
        
    except ValueError as e:
        return jsonify({
            'error': 'Invalid input values',
            'message': str(e)
        }), 400
    except Exception as e:
        return jsonify({
            'error': 'Prediction failed',
            'message': str(e)
        }), 500

@app.route('/predict_batch', methods=['POST'])
def predict_batch():
    """
    Predict for multiple students
    
    Expected JSON input:
    {
        "students": [
            {
                "student_id": "S001",
                "study_hours_per_week": 25,
                "attendance_rate": 85.5,
                ...
            },
            ...
        ]
    }
    """
    try:
        data = request.get_json()
        
        if 'students' not in data:
            return jsonify({
                'error': 'Missing students array'
            }), 400
        
        results = []
        
        for student_data in data['students']:
            # Make prediction for each student
            student_id = student_data.get('student_id', 'Unknown')
            
            # Prepare features (same as single prediction)
            study_hours = float(student_data['study_hours_per_week'])
            attendance_rate = float(student_data['attendance_rate'])
            past_exam_scores = float(student_data['past_exam_scores'])
            
            study_attendance_score = study_hours * (attendance_rate / 100)
            performance_index = past_exam_scores * (attendance_rate / 100)
            
            gender_encoded = label_encoders['Gender'].transform([student_data['gender']])[0]
            parental_edu_encoded = label_encoders['Parental_Education_Level'].transform(
                [student_data['parental_education_level']]
            )[0]
            internet_encoded = label_encoders['Internet_Access_at_Home'].transform(
                [student_data['internet_access_at_home']]
            )[0]
            extracurricular_encoded = label_encoders['Extracurricular_Activities'].transform(
                [student_data['extracurricular_activities']]
            )[0]
            
            features = np.array([[
                study_hours,
                attendance_rate,
                past_exam_scores,
                gender_encoded,
                parental_edu_encoded,
                internet_encoded,
                extracurricular_encoded,
                study_attendance_score,
                performance_index
            ]])
            
            features_scaled = scaler.transform(features)
            prediction = model.predict(features_scaled)[0]
            prediction_proba = model.predict_proba(features_scaled)[0]
            predicted_class = target_encoder.inverse_transform([prediction])[0]
            
            results.append({
                'student_id': student_id,
                'prediction': predicted_class,
                'confidence': float(max(prediction_proba))
            })
        
        return jsonify({
            'predictions': results,
            'total_students': len(results)
        })
        
    except Exception as e:
        return jsonify({
            'error': 'Batch prediction failed',
            'message': str(e)
        }), 500

@app.route('/model_info', methods=['GET'])
def model_info():
    """Get information about the model"""
    return jsonify({
        'model_name': model_artifacts['model_name'],
        'accuracy': float(model_artifacts['accuracy']),
        'features': feature_columns,
        'classes': list(target_encoder.classes_),
        'categorical_encodings': {
            'gender': list(label_encoders['Gender'].classes_),
            'parental_education_level': list(label_encoders['Parental_Education_Level'].classes_),
            'internet_access_at_home': list(label_encoders['Internet_Access_at_Home'].classes_),
            'extracurricular_activities': list(label_encoders['Extracurricular_Activities'].classes_)
        }
    })

if __name__ == '__main__':
    print("\n" + "="*60)
    print("Student Performance Prediction API")
    print("="*60)
    print(f"Model: {model_artifacts['model_name']}")
    print(f"Accuracy: {model_artifacts['accuracy']:.4f}")
    print("\nEndpoints:")
    print("  GET  /health        - Health check")
    print("  POST /predict       - Single prediction")
    print("  POST /predict_batch - Batch predictions")
    print("  GET  /model_info    - Model information")
    print("\nStarting server on http://localhost:5000")
    print("="*60 + "\n")
    
    app.run(host='0.0.0.0', port=5000, debug=True)
