"""
Inference module for the student performance prediction model.

This module handles loading the trained model and making predictions
on new student data to recommend future education paths.
"""

import pandas as pd
import numpy as np
import pickle
import os
from typing import Dict, Any, List, Tuple, Optional
from utils.logger import get_logger
from training.preprocess import encode_categorical_features, normalize_numerical_features
from utils.feature_engineering import create_all_features

logger = get_logger(__name__)


class StudentPerformancePredictor:
    """
    Predictor class for student future education track recommendation.
    """
    
    def __init__(
        self,
        model_path: str = 'models/education_model.pkl',
        encoder_path: str = 'models/label_encoder.pkl',
        scaler_path: str = 'models/scaler.pkl'
    ):
        """
        Initialize the predictor.
        
        Args:
            model_path: Path to the trained model
            encoder_path: Path to the label encoders
            scaler_path: Path to the feature scaler
        """
        self.model_path = model_path
        self.encoder_path = encoder_path
        self.scaler_path = scaler_path
        
        self.model = None
        self.encoders = None
        self.scaler = None
        self.feature_names = None
        
        self._load_model_artifacts()
    
    def _load_model_artifacts(self) -> None:
        """
        Load model, encoders, and scaler from disk.
        """
        try:
            # Load model
            logger.info(f"Loading model from {self.model_path}")
            with open(self.model_path, 'rb') as f:
                self.model = pickle.load(f)
            logger.info("Model loaded successfully")
            
            # Load encoders
            logger.info(f"Loading encoders from {self.encoder_path}")
            with open(self.encoder_path, 'rb') as f:
                self.encoders = pickle.load(f)
            logger.info(f"Loaded {len(self.encoders)} encoders")
            
            # Load scaler
            logger.info(f"Loading scaler from {self.scaler_path}")
            with open(self.scaler_path, 'rb') as f:
                self.scaler = pickle.load(f)
            logger.info("Scaler loaded successfully")
            
            # Store feature names (if available)
            if hasattr(self.model, 'feature_names_in_'):
                self.feature_names = list(self.model.feature_names_in_)
            
        except FileNotFoundError as e:
            logger.error(f"Model artifact not found: {str(e)}")
            raise
        except Exception as e:
            logger.error(f"Error loading model artifacts: {str(e)}")
            raise
    
    def validate_input(self, features: Dict[str, Any]) -> bool:
        """
        Validate input features.
        
        Args:
            features: Dictionary of input features
        
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
        
        logger.info("Input features validated successfully")
        return True
    
    def preprocess_input(self, features: Dict[str, Any]) -> pd.DataFrame:
        """
        Preprocess input features using the same pipeline as training.
        
        Args:
            features: Dictionary of input features
        
        Returns:
            Preprocessed DataFrame ready for prediction
        """
        logger.info("Preprocessing input features")
        
        # Convert to DataFrame
        df = pd.DataFrame([features])
        
        # Create engineered features
        df = create_all_features(df)
        
        # Identify categorical and numerical columns
        categorical_cols = df.select_dtypes(include=['object']).columns.tolist()
        numerical_cols = df.select_dtypes(include=[np.number]).columns.tolist()
        
        # Encode categorical features using pre-fitted encoders
        # Filter encoders to exclude 'target' encoder
        feature_encoders = {k: v for k, v in self.encoders.items() if k != 'target'}
        df, _ = encode_categorical_features(df, categorical_cols, encoders=feature_encoders, fit=False)
        
        # Normalize numerical features using pre-fitted scaler
        df, _ = normalize_numerical_features(df, numerical_cols, scaler=self.scaler, fit=False)
        
        logger.info("Input preprocessing completed")
        
        return df
    
    def predict(
        self,
        features: Dict[str, Any],
        return_probabilities: bool = True
    ) -> Dict[str, Any]:
        """
        Make prediction for a single student.
        
        Args:
            features: Dictionary of student features
            return_probabilities: Whether to return prediction probabilities
        
        Returns:
            Dictionary containing prediction results
        """
        logger.info("Making prediction")
        
        # Validate input
        if not self.validate_input(features):
            raise ValueError("Invalid input features")
        
        # Preprocess input
        X = self.preprocess_input(features)
        
        # Make prediction
        prediction = self.model.predict(X)[0]
        
        # Get prediction probabilities
        if return_probabilities and hasattr(self.model, 'predict_proba'):
            probabilities = self.model.predict_proba(X)[0]
            confidence = float(np.max(probabilities))
        else:
            probabilities = None
            confidence = 1.0
        
        # Decode prediction
        target_encoder = self.encoders.get('target')
        if target_encoder:
            predicted_track = target_encoder.inverse_transform([prediction])[0]
        else:
            predicted_track = str(prediction)
        
        result = {
            'predicted_track': predicted_track,
            'confidence': round(confidence, 4),
            'prediction_raw': int(prediction)
        }
        
        # Add class probabilities if available
        if probabilities is not None and target_encoder:
            class_probabilities = {
                target_encoder.classes_[i]: round(float(probabilities[i]), 4)
                for i in range(len(target_encoder.classes_))
            }
            result['class_probabilities'] = class_probabilities
        
        logger.info(f"Prediction: {predicted_track} (confidence: {confidence:.2%})")
        
        return result
    
    def predict_batch(
        self,
        features_list: List[Dict[str, Any]]
    ) -> List[Dict[str, Any]]:
        """
        Make predictions for multiple students.
        
        Args:
            features_list: List of feature dictionaries
        
        Returns:
            List of prediction result dictionaries
        """
        logger.info(f"Making batch predictions for {len(features_list)} students")
        
        results = []
        for i, features in enumerate(features_list):
            try:
                result = self.predict(features)
                result['student_index'] = i
                results.append(result)
            except Exception as e:
                logger.error(f"Error predicting for student {i}: {str(e)}")
                results.append({
                    'student_index': i,
                    'error': str(e),
                    'predicted_track': None,
                    'confidence': 0.0
                })
        
        logger.info(f"Batch prediction completed: {len(results)} results")
        
        return results
    
    def get_model_info(self) -> Dict[str, Any]:
        """
        Get information about the loaded model.
        
        Returns:
            Dictionary containing model information
        """
        info = {
            'model_type': type(self.model).__name__,
            'num_features': len(self.feature_names) if self.feature_names else 'Unknown',
            'feature_names': self.feature_names,
            'num_classes': len(self.encoders['target'].classes_) if 'target' in self.encoders else 'Unknown',
            'class_labels': list(self.encoders['target'].classes_) if 'target' in self.encoders else []
        }
        
        return info


def load_predictor(
    model_path: str = 'models/education_model.pkl',
    encoder_path: str = 'models/label_encoder.pkl',
    scaler_path: str = 'models/scaler.pkl'
) -> StudentPerformancePredictor:
    """
    Convenience function to load and initialize the predictor.
    
    Args:
        model_path: Path to the trained model
        encoder_path: Path to the label encoders
        scaler_path: Path to the feature scaler
    
    Returns:
        Initialized StudentPerformancePredictor instance
    """
    return StudentPerformancePredictor(model_path, encoder_path, scaler_path)


def predict_single_student(
    features: Dict[str, Any],
    model_path: str = 'models/education_model.pkl',
    encoder_path: str = 'models/label_encoder.pkl',
    scaler_path: str = 'models/scaler.pkl'
) -> Dict[str, Any]:
    """
    Convenience function to make a single prediction.
    
    Args:
        features: Dictionary of student features
        model_path: Path to the trained model
        encoder_path: Path to the label encoders
        scaler_path: Path to the feature scaler
    
    Returns:
        Prediction result dictionary
    """
    predictor = load_predictor(model_path, encoder_path, scaler_path)
    return predictor.predict(features)