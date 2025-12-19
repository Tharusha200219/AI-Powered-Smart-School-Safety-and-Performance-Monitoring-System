"""
Flask API server for Student Performance Prediction Model.

This module provides REST API endpoints for making real-time predictions
based on student attendance and marks data from the school management system.
"""

from flask import Flask, request, jsonify
from flask_cors import CORS
import os
import sys
from datetime import datetime
from typing import Dict, Any, Optional
import traceback

# Add the project root to Python path
sys.path.append(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

from src.inference import StudentPerformancePredictor
from utils.logger import get_logger

logger = get_logger(__name__)

app = Flask(__name__)
CORS(app)  # Enable CORS for all routes

# Initialize the predictor
predictor = None

def initialize_predictor():
    """Initialize the prediction model."""
    global predictor
    try:
        model_path = os.path.join(os.path.dirname(__file__), '..', 'models', 'education_model.pkl')
        predictor = StudentPerformancePredictor(model_path=model_path)
        logger.info("Prediction model initialized successfully")
        return True
    except Exception as e:
        logger.error(f"Failed to initialize prediction model: {str(e)}")
        return False

# Initialize predictor on module import
initialize_predictor()

def calculate_age(date_of_birth: str) -> int:
    """Calculate age from date of birth."""
    try:
        dob = datetime.strptime(date_of_birth, '%Y-%m-%d')
        today = datetime.now()
        age = today.year - dob.year - ((today.month, today.day) < (dob.month, dob.day))
        return age
    except:
        return 16  # Default age

def calculate_attendance_percentage(student_id: int, school_system_data: Dict) -> float:
    """Calculate attendance percentage from school system data."""
    try:
        # This would be replaced with actual API call to school system
        # For now, return a mock value or use provided data
        attendance_records = school_system_data.get('attendance_records', [])
        if attendance_records:
            total_days = len(attendance_records)
            present_days = sum(1 for record in attendance_records if record.get('status') == 'present')
            return (present_days / total_days) * 100 if total_days > 0 else 75.0
        return school_system_data.get('attendance_percentage', 75.0)
    except:
        return 75.0  # Default attendance

def calculate_average_marks(student_id: int, school_system_data: Dict) -> float:
    """Calculate average marks from school system data."""
    try:
        marks_records = school_system_data.get('marks_records', [])
        if marks_records:
            percentages = [record.get('percentage', 0) for record in marks_records if record.get('percentage')]
            return sum(percentages) / len(percentages) if percentages else 70.0
        return school_system_data.get('average_marks', 70.0)
    except:
        return 70.0  # Default marks

def prepare_features_from_school_data(student_data: Dict, school_system_data: Dict) -> Dict[str, Any]:
    """
    Prepare features for prediction from school management system data.

    Args:
        student_data: Student profile data
        school_system_data: Attendance and marks data

    Returns:
        Dictionary of features for prediction
    """
    # Calculate derived features
    age = calculate_age(student_data.get('date_of_birth', '2008-01-01'))
    attendance_percentage = calculate_attendance_percentage(
        student_data.get('student_id'),
        school_system_data
    )
    average_marks = calculate_average_marks(
        student_data.get('student_id'),
        school_system_data
    )

    # Map gender
    gender_map = {'male': 'Male', 'female': 'Female', 'm': 'Male', 'f': 'Female'}
    gender = gender_map.get(student_data.get('gender', '').lower(), 'Male')

    # Prepare features with defaults for missing data
    features = {
        'StudyHours': school_system_data.get('study_hours', 3),  # Default 3 hours
        'Attendance': attendance_percentage,
        'Resources': school_system_data.get('resources_access', 1),  # 1=Yes, 0=No
        'Extracurricular': school_system_data.get('extracurricular_activities', 1),  # 1=Yes, 0=No
        'Motivation': school_system_data.get('motivation_level', 3),  # Scale 1-5
        'Internet': school_system_data.get('internet_access', 1),  # 1=Yes, 0=No
        'Gender': gender,
        'Age': age,
        'LearningStyle': school_system_data.get('learning_style', 'Visual'),  # Visual/Auditory/Reading/Kinesthetic
        'OnlineCourses': school_system_data.get('online_courses_completed', 0),  # Number
        'Discussions': school_system_data.get('class_discussions_participation', 2),  # Scale 1-5
        'AssignmentCompletion': school_system_data.get('assignment_completion_rate', 80.0),  # Percentage
        'ExamScore': average_marks,
        'EduTech': school_system_data.get('edutech_usage', 1),  # 1=Yes, 0=No
        'StressLevel': school_system_data.get('stress_level', 2),  # Scale 1-5
        'FinalGrade': average_marks  # Using average marks as final grade
    }

    return features

@app.route('/health', methods=['GET'])
def health_check():
    """Health check endpoint."""
    return jsonify({
        'status': 'healthy',
        'timestamp': datetime.now().isoformat(),
        'model_loaded': predictor is not None
    })

@app.route('/predict', methods=['POST'])
def predict():
    """
    Make prediction for student future education track.

    Expected JSON payload:
    {
        "student_data": {
            "student_id": 123,
            "first_name": "John",
            "last_name": "Doe",
            "date_of_birth": "2008-05-15",
            "gender": "male"
        },
        "school_data": {
            "attendance_records": [...],
            "marks_records": [...],
            "attendance_percentage": 85.5,
            "average_marks": 78.3,
            "study_hours": 4,
            "resources_access": 1,
            "extracurricular_activities": 1,
            "motivation_level": 4,
            "internet_access": 1,
            "learning_style": "Visual",
            "online_courses_completed": 2,
            "class_discussions_participation": 3,
            "assignment_completion_rate": 85.0,
            "edutech_usage": 1,
            "stress_level": 2
        }
    }
    """
    try:
        if predictor is None:
            return jsonify({
                'error': 'Prediction model not loaded',
                'status': 'error'
            }), 500

        data = request.get_json()

        if not data:
            return jsonify({
                'error': 'No data provided',
                'status': 'error'
            }), 400

        student_data = data.get('student_data', {})
        school_data = data.get('school_data', {})

        if not student_data:
            return jsonify({
                'error': 'Student data is required',
                'status': 'error'
            }), 400

        # Prepare features
        features = prepare_features_from_school_data(student_data, school_data)

        # Make prediction
        result = predictor.predict(features, return_probabilities=True)

        # Add student info to response
        response = {
            'student_id': student_data.get('student_id'),
            'student_name': f"{student_data.get('first_name', '')} {student_data.get('last_name', '')}".strip(),
            'prediction': result,
            'features_used': features,
            'timestamp': datetime.now().isoformat(),
            'status': 'success'
        }

        logger.info(f"Prediction made for student {student_data.get('student_id')}: {result['predicted_track']}")

        return jsonify(response)

    except Exception as e:
        logger.error(f"Prediction error: {str(e)}")
        logger.error(traceback.format_exc())
        return jsonify({
            'error': str(e),
            'status': 'error',
            'timestamp': datetime.now().isoformat()
        }), 500

@app.route('/predict/batch', methods=['POST'])
def predict_batch():
    """
    Make batch predictions for multiple students.

    Expected JSON payload:
    {
        "students": [
            {
                "student_data": {...},
                "school_data": {...}
            },
            ...
        ]
    }
    """
    try:
        if predictor is None:
            return jsonify({
                'error': 'Prediction model not loaded',
                'status': 'error'
            }), 500

        data = request.get_json()

        if not data or 'students' not in data:
            return jsonify({
                'error': 'Students data is required',
                'status': 'error'
            }), 400

        results = []
        errors = []

        for i, student_info in enumerate(data['students']):
            try:
                student_data = student_info.get('student_data', {})
                school_data = student_info.get('school_data', {})

                features = prepare_features_from_school_data(student_data, school_data)
                result = predictor.predict(features, return_probabilities=True)

                results.append({
                    'student_id': student_data.get('student_id'),
                    'student_name': f"{student_data.get('first_name', '')} {student_data.get('last_name', '')}".strip(),
                    'prediction': result,
                    'features_used': features
                })

            except Exception as e:
                errors.append({
                    'index': i,
                    'student_id': student_info.get('student_data', {}).get('student_id'),
                    'error': str(e)
                })

        response = {
            'results': results,
            'errors': errors,
            'total_processed': len(results),
            'total_errors': len(errors),
            'timestamp': datetime.now().isoformat(),
            'status': 'success'
        }

        return jsonify(response)

    except Exception as e:
        logger.error(f"Batch prediction error: {str(e)}")
        return jsonify({
            'error': str(e),
            'status': 'error'
        }), 500

@app.errorhandler(404)
def not_found(error):
    return jsonify({
        'error': 'Endpoint not found',
        'status': 'error'
    }), 404

@app.errorhandler(500)
def internal_error(error):
    return jsonify({
        'error': 'Internal server error',
        'status': 'error'
    }), 500

if __name__ == '__main__':
    if initialize_predictor():
        port = int(os.environ.get('PORT', 5000))
        app.run(host='0.0.0.0', port=port, debug=False)
    else:
        logger.error("Failed to start server due to model loading error")
        sys.exit(1)