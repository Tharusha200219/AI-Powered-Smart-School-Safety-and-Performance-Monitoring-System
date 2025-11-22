"""
Data preprocessing module for model training.

This module handles data cleaning, transformation, and preparation
steps required before training the student performance prediction model.
"""

import pandas as pd
import numpy as np
from sklearn.preprocessing import LabelEncoder, StandardScaler
from sklearn.model_selection import train_test_split
from typing import Tuple, Dict, Any
import pickle
import os
from utils.logger import get_logger

logger = get_logger(__name__)


def handle_missing_values(df: pd.DataFrame) -> pd.DataFrame:
    """
    Handle missing values in the dataset.
    
    Args:
        df: Input DataFrame
    
    Returns:
        DataFrame with missing values handled
    """
    logger.info("Handling missing values")
    
    initial_rows = len(df)
    missing_counts = df.isnull().sum()
    
    if missing_counts.sum() > 0:
        logger.info(f"Missing values found:\n{missing_counts[missing_counts > 0]}")
        
        # For numerical columns, fill with median
        numerical_cols = df.select_dtypes(include=[np.number]).columns
        for col in numerical_cols:
            if df[col].isnull().sum() > 0:
                median_value = df[col].median()
                df[col].fillna(median_value, inplace=True)
                logger.info(f"Filled {col} with median: {median_value}")
        
        # For categorical columns, fill with mode
        categorical_cols = df.select_dtypes(include=['object']).columns
        for col in categorical_cols:
            if df[col].isnull().sum() > 0:
                mode_value = df[col].mode()[0] if not df[col].mode().empty else 'Unknown'
                df[col].fillna(mode_value, inplace=True)
                logger.info(f"Filled {col} with mode: {mode_value}")
    
    final_rows = len(df)
    logger.info(f"Rows after handling missing values: {final_rows} (removed: {initial_rows - final_rows})")
    
    return df


def encode_categorical_features(
    df: pd.DataFrame, 
    categorical_cols: list,
    encoders: Dict[str, LabelEncoder] = None,
    fit: bool = True
) -> Tuple[pd.DataFrame, Dict[str, LabelEncoder]]:
    """
    Encode categorical features using LabelEncoder.
    
    Args:
        df: Input DataFrame
        categorical_cols: List of categorical column names
        encoders: Dictionary of pre-fitted encoders (for inference)
        fit: Whether to fit new encoders or use provided ones
    
    Returns:
        Tuple of (encoded DataFrame, dictionary of encoders)
    """
    logger.info(f"Encoding categorical features: {categorical_cols}")
    
    if encoders is None:
        encoders = {}
    
    df_encoded = df.copy()
    
    for col in categorical_cols:
        if col not in df.columns:
            logger.warning(f"Column {col} not found in DataFrame")
            continue
        
        if fit:
            encoder = LabelEncoder()
            df_encoded[col] = encoder.fit_transform(df[col].astype(str))
            encoders[col] = encoder
            logger.info(f"Encoded {col}: {len(encoder.classes_)} unique values")
        else:
            if col in encoders:
                # Handle unknown categories
                known_values = set(encoders[col].classes_)
                df_encoded[col] = df[col].apply(
                    lambda x: x if x in known_values else encoders[col].classes_[0]
                )
                df_encoded[col] = encoders[col].transform(df_encoded[col].astype(str))
            else:
                logger.warning(f"No encoder found for {col}")
    
    return df_encoded, encoders


def normalize_numerical_features(
    df: pd.DataFrame,
    numerical_cols: list,
    scaler: StandardScaler = None,
    fit: bool = True
) -> Tuple[pd.DataFrame, StandardScaler]:
    """
    Normalize numerical features using StandardScaler.
    
    Args:
        df: Input DataFrame
        numerical_cols: List of numerical column names
        scaler: Pre-fitted scaler (for inference)
        fit: Whether to fit new scaler or use provided one
    
    Returns:
        Tuple of (normalized DataFrame, fitted scaler)
    """
    logger.info(f"Normalizing numerical features: {numerical_cols}")
    
    df_normalized = df.copy()
    
    # Filter out columns that don't exist
    existing_cols = [col for col in numerical_cols if col in df.columns]
    
    if not existing_cols:
        logger.warning("No numerical columns found to normalize")
        return df_normalized, scaler
    
    if fit:
        scaler = StandardScaler()
        df_normalized[existing_cols] = scaler.fit_transform(df[existing_cols])
        logger.info(f"Normalized {len(existing_cols)} numerical features")
    else:
        if scaler is not None:
            df_normalized[existing_cols] = scaler.transform(df[existing_cols])
        else:
            logger.warning("No scaler provided for transformation")
    
    return df_normalized, scaler


def split_data(
    X: pd.DataFrame,
    y: pd.Series,
    test_size: float = 0.2,
    random_state: int = 42
) -> Tuple[pd.DataFrame, pd.DataFrame, pd.Series, pd.Series]:
    """
    Split data into training and testing sets.
    
    Args:
        X: Feature DataFrame
        y: Target Series
        test_size: Proportion of test set (default: 0.2)
        random_state: Random seed for reproducibility
    
    Returns:
        Tuple of (X_train, X_test, y_train, y_test)
    """
    logger.info(f"Splitting data: test_size={test_size}, random_state={random_state}")
    
    X_train, X_test, y_train, y_test = train_test_split(
        X, y, test_size=test_size, random_state=random_state, stratify=y
    )
    
    logger.info(f"Training set: {len(X_train)} samples")
    logger.info(f"Test set: {len(X_test)} samples")
    
    return X_train, X_test, y_train, y_test


def save_preprocessing_objects(
    encoders: Dict[str, LabelEncoder],
    scaler: StandardScaler,
    save_dir: str
) -> bool:
    """
    Save preprocessing objects (encoders and scaler) to disk.
    
    Args:
        encoders: Dictionary of label encoders
        scaler: Fitted scaler
        save_dir: Directory to save objects
    
    Returns:
        True if successful, False otherwise
    """
    try:
        os.makedirs(save_dir, exist_ok=True)
        
        # Save encoders
        encoder_path = os.path.join(save_dir, 'label_encoder.pkl')
        with open(encoder_path, 'wb') as f:
            pickle.dump(encoders, f)
        logger.info(f"Encoders saved to {encoder_path}")
        
        # Save scaler
        scaler_path = os.path.join(save_dir, 'scaler.pkl')
        with open(scaler_path, 'wb') as f:
            pickle.dump(scaler, f)
        logger.info(f"Scaler saved to {scaler_path}")
        
        return True
    
    except Exception as e:
        logger.error(f"Error saving preprocessing objects: {str(e)}")
        return False


def load_preprocessing_objects(load_dir: str) -> Tuple[Dict[str, LabelEncoder], StandardScaler]:
    """
    Load preprocessing objects from disk.
    
    Args:
        load_dir: Directory containing saved objects
    
    Returns:
        Tuple of (encoders dictionary, scaler)
    """
    try:
        # Load encoders
        encoder_path = os.path.join(load_dir, 'label_encoder.pkl')
        with open(encoder_path, 'rb') as f:
            encoders = pickle.load(f)
        logger.info(f"Encoders loaded from {encoder_path}")
        
        # Load scaler
        scaler_path = os.path.join(load_dir, 'scaler.pkl')
        with open(scaler_path, 'rb') as f:
            scaler = pickle.load(f)
        logger.info(f"Scaler loaded from {scaler_path}")
        
        return encoders, scaler
    
    except Exception as e:
        logger.error(f"Error loading preprocessing objects: {str(e)}")
        return None, None