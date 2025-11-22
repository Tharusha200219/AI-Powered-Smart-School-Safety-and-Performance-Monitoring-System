"""
Real data transformation utilities.

This module handles the transformation of real-world student data
into the format expected by the prediction model, including
data normalization and feature mapping.
"""

import pandas as pd
import numpy as np
from datetime import datetime, timedelta
from typing import Dict, List, Any, Optional
from utils.logger import get_logger

logger = get_logger(__name__)


def convert_attendance_to_percentage(attendance_records: List[Dict[str, Any]]) -> float:
    """
    Convert attendance records to attendance percentage.
    
    Args:
        attendance_records: List of attendance record dictionaries with 'status' field
    
    Returns:
        Attendance percentage (0-100)
    """
    if not attendance_records:
        logger.warning("No attendance records provided")
        return 0.0
    
    total_days = len(attendance_records)
    present_days = sum(1 for record in attendance_records if record.get('status', '').lower() in ['present', 'p', '1', 'true'])
    
    percentage = (present_days / total_days) * 100 if total_days > 0 else 0.0
    
    logger.info(f"Attendance: {present_days}/{total_days} days = {percentage:.2f}%")
    
    return round(percentage, 2)


def calculate_exam_score(student_subject_table: List[Dict[str, Any]]) -> float:
    """
    Calculate average exam score across all subjects.
    
    Args:
        student_subject_table: List of student-subject records with 'grade' field
    
    Returns:
        Average grade/score across subjects (0-100)
    """
    if not student_subject_table:
        logger.warning("No subject records provided")
        return 0.0
    
    grades = []
    for record in student_subject_table:
        grade = record.get('grade')
        if grade is not None:
            try:
                # Handle both numeric grades and letter grades
                if isinstance(grade, str):
                    # Convert letter grades to numeric (A=90, B=80, etc.)
                    grade_map = {'A': 90, 'B': 80, 'C': 70, 'D': 60, 'F': 50}
                    grade = grade_map.get(grade.upper(), 0)
                grades.append(float(grade))
            except (ValueError, TypeError):
                logger.warning(f"Invalid grade value: {grade}")
                continue
    
    avg_score = sum(grades) / len(grades) if grades else 0.0
    
    logger.info(f"Exam Score: Average of {len(grades)} subjects = {avg_score:.2f}")
    
    return round(avg_score, 2)


def calculate_age_from_dob(date_of_birth: str) -> int:
    """
    Calculate age from date of birth.
    
    Args:
        date_of_birth: Date of birth as string (YYYY-MM-DD)
    
    Returns:
        Age in years
    """
    try:
        dob = pd.to_datetime(date_of_birth)
        today = datetime.now()
        age = today.year - dob.year - ((today.month, today.day) < (dob.month, dob.day))
        return age
    except Exception as e:
        logger.warning(f"Error calculating age: {str(e)}")
        return 15  # Default age


def map_gender_to_model_format(gender: str) -> str:
    """
    Map gender values to model-expected format.
    
    Args:
        gender: Gender value from database
    
    Returns:
        Standardized gender value
    """
    gender_lower = str(gender).lower()
    
    if gender_lower in ['m', 'male', '1']:
        return 'Male'
    elif gender_lower in ['f', 'female', '0']:
        return 'Female'
    else:
        return 'Other'


def prepare_student_features(
    student: Dict[str, Any],
    attendance_records: List[Dict[str, Any]],
    subject_records: List[Dict[str, Any]],
    additional_data: Optional[Dict[str, Any]] = None
) -> Dict[str, Any]:
    """
    Prepare student features from raw database tables to match model input format.
    
    Expected model columns:
    StudyHours, Attendance, Resources, Extracurricular, Motivation,
    Internet, Gender, Age, LearningStyle, OnlineCourses, Discussions,
    AssignmentCompletion, ExamScore, EduTech, StressLevel, FinalGrade
    
    Args:
        student: Student record from students table
        attendance_records: List of attendance records
        subject_records: List of student-subject records
        additional_data: Optional dictionary with additional student data
    
    Returns:
        Dictionary with features matching model input format
    """
    logger.info(f"Preparing features for student: {student.get('student_id', 'Unknown')}")
    
    # Initialize additional_data if None
    if additional_data is None:
        additional_data = {}
    
    # Calculate derived features
    attendance_percentage = convert_attendance_to_percentage(attendance_records)
    exam_score = calculate_exam_score(subject_records)
    age = calculate_age_from_dob(student.get('date_of_birth', '2010-01-01'))
    gender = map_gender_to_model_format(student.get('gender', 'Other'))
    
    # Calculate final grade (weighted average of exam score and other factors)
    # If final grade not provided, estimate from exam score
    final_grade = additional_data.get('FinalGrade', exam_score * 0.95)
    
    # Prepare feature dictionary matching model input
    features = {
        # Direct mappings from additional_data or defaults
        'StudyHours': additional_data.get('StudyHours', 5.0),
        'Attendance': attendance_percentage,
        'Resources': additional_data.get('Resources', 3),  # 1-5 scale
        'Extracurricular': additional_data.get('Extracurricular', 2),  # 0-5 scale
        'Motivation': additional_data.get('Motivation', 3),  # 1-5 scale
        'Internet': additional_data.get('Internet', 1),  # 0 or 1
        'Gender': gender,
        'Age': age,
        'LearningStyle': additional_data.get('LearningStyle', 'Visual'),  # Visual, Auditory, Kinesthetic
        'OnlineCourses': additional_data.get('OnlineCourses', 1),  # 0-10 scale
        'Discussions': additional_data.get('Discussions', 3),  # 0-10 scale
        'AssignmentCompletion': additional_data.get('AssignmentCompletion', 80.0),  # 0-100 percentage
        'ExamScore': exam_score,
        'EduTech': additional_data.get('EduTech', 1),  # 0 or 1
        'StressLevel': additional_data.get('StressLevel', 3),  # 1-5 scale
        'FinalGrade': final_grade
    }
    
    logger.info(f"Features prepared successfully for student {student.get('student_id')}")
    logger.debug(f"Feature values: {features}")
    
    return features


def prepare_batch_student_features(
    students: List[Dict[str, Any]],
    attendance_data: Dict[str, List[Dict[str, Any]]],
    subject_data: Dict[str, List[Dict[str, Any]]],
    additional_data: Optional[Dict[str, Dict[str, Any]]] = None
) -> pd.DataFrame:
    """
    Prepare features for multiple students.
    
    Args:
        students: List of student records
        attendance_data: Dictionary mapping student_id to attendance records
        subject_data: Dictionary mapping student_id to subject records
        additional_data: Optional dictionary mapping student_id to additional data
    
    Returns:
        DataFrame with features for all students
    """
    logger.info(f"Preparing features for {len(students)} students")
    
    all_features = []
    
    for student in students:
        student_id = student.get('student_id')
        
        # Get student-specific data
        student_attendance = attendance_data.get(student_id, [])
        student_subjects = subject_data.get(student_id, [])
        student_additional = additional_data.get(student_id, {}) if additional_data else {}
        
        # Prepare features
        features = prepare_student_features(
            student,
            student_attendance,
            student_subjects,
            student_additional
        )
        
        # Add student_id for reference
        features['student_id'] = student_id
        all_features.append(features)
    
    # Convert to DataFrame
    df = pd.DataFrame(all_features)
    
    logger.info(f"Prepared features DataFrame with shape: {df.shape}")
    
    return df


def validate_feature_dict(features: Dict[str, Any]) -> bool:
    """
    Validate that all required features are present.
    
    Args:
        features: Feature dictionary to validate
    
    Returns:
        True if valid, False otherwise
    """
    required_features = [
        'StudyHours', 'Attendance', 'Resources', 'Extracurricular', 'Motivation',
        'Internet', 'Gender', 'Age', 'LearningStyle', 'OnlineCourses', 'Discussions',
        'AssignmentCompletion', 'ExamScore', 'EduTech', 'StressLevel', 'FinalGrade'
    ]
    
    missing_features = [f for f in required_features if f not in features]
    
    if missing_features:
        logger.error(f"Missing required features: {missing_features}")
        return False
    
    logger.info("All required features present")
    return True


def create_mock_student_data() -> Dict[str, Any]:
    """
    Create mock student data for testing.
    
    Returns:
        Dictionary containing mock student, attendance, and subject data
    """
    logger.info("Creating mock student data")
    
    mock_data = {
        'student': {
            'student_id': 'STU001',
            'user_id': 'USER001',
            'student_code': 'SC2024001',
            'first_name': 'John',
            'middle_name': 'Michael',
            'last_name': 'Doe',
            'date_of_birth': '2010-05-15',
            'gender': 'Male',
            'nationality': 'American',
            'religion': 'Christian',
            'home_language': 'English',
            'enrollment_date': '2020-09-01',
            'grade_level': '9',
            'class_id': 'CLS001',
            'section': 'A',
            'is_active': True,
            'email': 'john.doe@school.com'
        },
        'attendance_records': [
            {'attendance_id': i, 'student_id': 'STU001', 'status': 'Present' if i % 10 != 0 else 'Absent'}
            for i in range(1, 101)  # 100 days, 90% attendance
        ],
        'subject_records': [
            {'id': 1, 'student_id': 'STU001', 'subject_id': 'MATH', 'grade': 85},
            {'id': 2, 'student_id': 'STU001', 'subject_id': 'SCI', 'grade': 88},
            {'id': 3, 'student_id': 'STU001', 'subject_id': 'ENG', 'grade': 82},
            {'id': 4, 'student_id': 'STU001', 'subject_id': 'HIST', 'grade': 90},
            {'id': 5, 'student_id': 'STU001', 'subject_id': 'CS', 'grade': 92}
        ],
        'additional_data': {
            'StudyHours': 6.5,
            'Resources': 4,
            'Extracurricular': 3,
            'Motivation': 4,
            'Internet': 1,
            'LearningStyle': 'Visual',
            'OnlineCourses': 5,
            'Discussions': 7,
            'AssignmentCompletion': 92.0,
            'EduTech': 1,
            'StressLevel': 2,
            'FinalGrade': 87.4
        }
    }
    
    logger.info("Mock student data created successfully")
    
    return mock_data