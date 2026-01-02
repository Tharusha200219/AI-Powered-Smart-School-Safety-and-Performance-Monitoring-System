"""
Configuration settings for the Student Performance Prediction System
"""

import os

# Base directory
BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))

# Data paths
DATA_DIR = os.path.join(BASE_DIR, 'data')
DATASET_PATH = os.path.join(BASE_DIR, 'dataset', 'student_performance_updated_1000 (1).csv')
CLEANED_DATA_PATH = os.path.join(DATA_DIR, 'cleaned_data.csv')

# Model paths
MODELS_DIR = os.path.join(BASE_DIR, 'models')
MODEL_PATH = os.path.join(MODELS_DIR, 'performance_predictor.pkl')
SCALER_PATH = os.path.join(MODELS_DIR, 'scaler.pkl')

# API Configuration
API_HOST = '0.0.0.0'
API_PORT = 5001  # Changed from 5000 due to macOS Control Center conflict
API_DEBUG = True

# Model Configuration
RANDOM_STATE = 42
TEST_SIZE = 0.2

# Prediction thresholds
ATTENDANCE_MIN = 0
ATTENDANCE_MAX = 100
MARKS_MIN = 0
MARKS_MAX = 100

# Performance categories
PERFORMANCE_EXCELLENT = 85
PERFORMANCE_GOOD = 70
PERFORMANCE_AVERAGE = 55
PERFORMANCE_POOR = 40
