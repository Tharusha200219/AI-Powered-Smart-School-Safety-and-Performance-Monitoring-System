"""
Data loading utilities for the student performance prediction model.

This module provides functions to load and read data from various sources,
including CSV files, databases, and external APIs.
"""

import pandas as pd
import os
from typing import Optional
from utils.logger import get_logger

logger = get_logger(__name__)


def load_csv_data(file_path: str, encoding: str = 'utf-8') -> Optional[pd.DataFrame]:
    """
    Load data from a CSV file.
    
    Args:
        file_path: Path to the CSV file
        encoding: File encoding (default: utf-8)
    
    Returns:
        DataFrame containing the loaded data, or None if loading fails
    """
    try:
        if not os.path.exists(file_path):
            logger.error(f"File not found: {file_path}")
            return None
        
        logger.info(f"Loading data from {file_path}")
        df = pd.read_csv(file_path, encoding=encoding)
        logger.info(f"Successfully loaded {len(df)} rows and {len(df.columns)} columns")
        
        return df
    
    except Exception as e:
        logger.error(f"Error loading CSV file: {str(e)}")
        return None


def save_csv_data(df: pd.DataFrame, file_path: str, index: bool = False) -> bool:
    """
    Save DataFrame to a CSV file.
    
    Args:
        df: DataFrame to save
        file_path: Destination file path
        index: Whether to include index in the saved file
    
    Returns:
        True if successful, False otherwise
    """
    try:
        # Create directory if it doesn't exist
        os.makedirs(os.path.dirname(file_path), exist_ok=True)
        
        logger.info(f"Saving data to {file_path}")
        df.to_csv(file_path, index=index)
        logger.info(f"Successfully saved {len(df)} rows to {file_path}")
        
        return True
    
    except Exception as e:
        logger.error(f"Error saving CSV file: {str(e)}")
        return False


def get_data_info(df: pd.DataFrame) -> dict:
    """
    Get basic information about the DataFrame.
    
    Args:
        df: DataFrame to analyze
    
    Returns:
        Dictionary containing data information
    """
    info = {
        'rows': len(df),
        'columns': len(df.columns),
        'column_names': list(df.columns),
        'missing_values': df.isnull().sum().to_dict(),
        'data_types': df.dtypes.to_dict()
    }
    
    return info