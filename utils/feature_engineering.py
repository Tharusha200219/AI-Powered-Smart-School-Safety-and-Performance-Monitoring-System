"""
Feature engineering utilities for the student performance model.

This module contains functions for creating, transforming, and selecting
features from raw student data to improve model performance.
"""

import pandas as pd
import numpy as np
from utils.logger import get_logger

logger = get_logger(__name__)


def create_performance_index(df: pd.DataFrame) -> pd.DataFrame:
    """
    Create a performance index based on exam scores and final grades.
    
    PerformanceIndex = (ExamScore * 0.6) + (FinalGrade * 0.4)
    
    Args:
        df: DataFrame with ExamScore and FinalGrade columns
    
    Returns:
        DataFrame with added PerformanceIndex column
    """
    try:
        logger.info("Creating PerformanceIndex feature")
        
        if 'ExamScore' not in df.columns or 'FinalGrade' not in df.columns:
            logger.warning("Required columns (ExamScore, FinalGrade) not found")
            return df
        
        df['PerformanceIndex'] = (df['ExamScore'] * 0.6) + (df['FinalGrade'] * 0.4)
        logger.info("PerformanceIndex feature created successfully")
        
        return df
    
    except Exception as e:
        logger.error(f"Error creating PerformanceIndex: {str(e)}")
        return df


def create_engagement_score(df: pd.DataFrame) -> pd.DataFrame:
    """
    Create an engagement score based on attendance, extracurricular activities, and discussions.
    
    EngagementScore = Attendance + Extracurricular + Discussions
    
    Args:
        df: DataFrame with Attendance, Extracurricular, and Discussions columns
    
    Returns:
        DataFrame with added EngagementScore column
    """
    try:
        logger.info("Creating EngagementScore feature")
        
        required_cols = ['Attendance', 'Extracurricular', 'Discussions']
        missing_cols = [col for col in required_cols if col not in df.columns]
        
        if missing_cols:
            logger.warning(f"Required columns missing: {missing_cols}")
            return df
        
        df['EngagementScore'] = (
            df['Attendance'] + 
            df['Extracurricular'] + 
            df['Discussions']
        )
        logger.info("EngagementScore feature created successfully")
        
        return df
    
    except Exception as e:
        logger.error(f"Error creating EngagementScore: {str(e)}")
        return df


def create_all_features(df: pd.DataFrame) -> pd.DataFrame:
    """
    Create all engineered features for the dataset.
    
    Args:
        df: Input DataFrame
    
    Returns:
        DataFrame with all engineered features
    """
    logger.info("Starting feature engineering process")
    
    df = create_performance_index(df)
    df = create_engagement_score(df)
    
    logger.info("Feature engineering completed")
    
    return df


def validate_features(df: pd.DataFrame, required_features: list) -> bool:
    """
    Validate that all required features are present in the DataFrame.
    
    Args:
        df: DataFrame to validate
        required_features: List of required feature names
    
    Returns:
        True if all features present, False otherwise
    """
    missing_features = [f for f in required_features if f not in df.columns]
    
    if missing_features:
        logger.error(f"Missing required features: {missing_features}")
        return False
    
    logger.info("All required features present")
    return True