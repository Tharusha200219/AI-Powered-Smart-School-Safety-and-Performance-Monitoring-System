"""
Model training module for the student performance prediction system.

This module contains functions and classes for training machine learning
models to predict student future education recommendations based on
historical performance data.
"""

import pandas as pd
import numpy as np
from sklearn.ensemble import RandomForestClassifier, GradientBoostingClassifier
from sklearn.model_selection import cross_val_score
import pickle
import os
from typing import Any, Dict, Tuple
from utils.logger import get_logger
from training.preprocess import (
    handle_missing_values,
    encode_categorical_features,
    normalize_numerical_features,
    split_data,
    save_preprocessing_objects
)
from training.evaluate import evaluate_model, plot_confusion_matrix, plot_feature_importance, save_evaluation_report
from utils.feature_engineering import create_all_features

logger = get_logger(__name__)


def prepare_data(
    df: pd.DataFrame,
    target_column: str = 'FutureEducationTrack'
) -> Tuple[pd.DataFrame, pd.Series, Dict[str, Any], Dict[str, Any]]:
    """
    Prepare data for model training.
    
    Args:
        df: Input DataFrame
        target_column: Name of the target column
    
    Returns:
        Tuple of (X, y, encoders, scaler)
    """
    logger.info("Starting data preparation")
    
    # Check if target column exists
    if target_column not in df.columns:
        raise ValueError(f"Target column '{target_column}' not found in DataFrame")
    
    # Create engineered features
    df = create_all_features(df)
    
    # Handle missing values
    df = handle_missing_values(df)
    
    # Separate features and target
    X = df.drop(columns=[target_column])
    y = df[target_column]
    
    # Identify categorical and numerical columns
    categorical_cols = X.select_dtypes(include=['object']).columns.tolist()
    numerical_cols = X.select_dtypes(include=[np.number]).columns.tolist()
    
    logger.info(f"Categorical columns: {categorical_cols}")
    logger.info(f"Numerical columns: {numerical_cols}")
    
    # Encode categorical features
    X, encoders = encode_categorical_features(X, categorical_cols, fit=True)
    
    # Encode target variable
    target_encoder = {}
    X_encoded, target_encoder = encode_categorical_features(
        pd.DataFrame({target_column: y}),
        [target_column],
        fit=True
    )
    y_encoded = X_encoded[target_column]
    encoders['target'] = target_encoder[target_column]
    
    # Normalize numerical features
    X, scaler = normalize_numerical_features(X, numerical_cols, fit=True)
    
    logger.info("Data preparation completed")
    
    return X, y_encoded, encoders, scaler


def train_random_forest(
    X_train: pd.DataFrame,
    y_train: pd.Series,
    n_estimators: int = 100,
    max_depth: int = None,
    random_state: int = 42,
    **kwargs
) -> RandomForestClassifier:
    """
    Train a Random Forest classifier.
    
    Args:
        X_train: Training features
        y_train: Training labels
        n_estimators: Number of trees
        max_depth: Maximum tree depth
        random_state: Random seed
        **kwargs: Additional parameters for RandomForestClassifier
    
    Returns:
        Trained RandomForestClassifier
    """
    logger.info("Training Random Forest Classifier")
    logger.info(f"Parameters: n_estimators={n_estimators}, max_depth={max_depth}")
    
    model = RandomForestClassifier(
        n_estimators=n_estimators,
        max_depth=max_depth,
        random_state=random_state,
        n_jobs=-1,
        **kwargs
    )
    
    model.fit(X_train, y_train)
    
    # Cross-validation score
    cv_scores = cross_val_score(model, X_train, y_train, cv=5, scoring='accuracy')
    logger.info(f"Cross-validation scores: {cv_scores}")
    logger.info(f"Mean CV accuracy: {cv_scores.mean():.4f} (+/- {cv_scores.std() * 2:.4f})")
    
    return model


def train_gradient_boosting(
    X_train: pd.DataFrame,
    y_train: pd.Series,
    n_estimators: int = 100,
    learning_rate: float = 0.1,
    max_depth: int = 3,
    random_state: int = 42,
    **kwargs
) -> GradientBoostingClassifier:
    """
    Train a Gradient Boosting classifier.
    
    Args:
        X_train: Training features
        y_train: Training labels
        n_estimators: Number of boosting stages
        learning_rate: Learning rate
        max_depth: Maximum tree depth
        random_state: Random seed
        **kwargs: Additional parameters for GradientBoostingClassifier
    
    Returns:
        Trained GradientBoostingClassifier
    """
    logger.info("Training Gradient Boosting Classifier")
    logger.info(f"Parameters: n_estimators={n_estimators}, learning_rate={learning_rate}, max_depth={max_depth}")
    
    model = GradientBoostingClassifier(
        n_estimators=n_estimators,
        learning_rate=learning_rate,
        max_depth=max_depth,
        random_state=random_state,
        **kwargs
    )
    
    model.fit(X_train, y_train)
    
    # Cross-validation score
    cv_scores = cross_val_score(model, X_train, y_train, cv=5, scoring='accuracy')
    logger.info(f"Cross-validation scores: {cv_scores}")
    logger.info(f"Mean CV accuracy: {cv_scores.mean():.4f} (+/- {cv_scores.std() * 2:.4f})")
    
    return model


def save_model(model: Any, model_path: str) -> bool:
    """
    Save trained model to disk.
    
    Args:
        model: Trained model
        model_path: Path to save the model
    
    Returns:
        True if successful, False otherwise
    """
    try:
        os.makedirs(os.path.dirname(model_path), exist_ok=True)
        
        with open(model_path, 'wb') as f:
            pickle.dump(model, f)
        
        logger.info(f"Model saved to {model_path}")
        return True
    
    except Exception as e:
        logger.error(f"Error saving model: {str(e)}")
        return False


def load_model(model_path: str) -> Any:
    """
    Load trained model from disk.
    
    Args:
        model_path: Path to the saved model
    
    Returns:
        Loaded model
    """
    try:
        with open(model_path, 'rb') as f:
            model = pickle.load(f)
        
        logger.info(f"Model loaded from {model_path}")
        return model
    
    except Exception as e:
        logger.error(f"Error loading model: {str(e)}")
        return None


def train_and_evaluate_model(
    df: pd.DataFrame,
    model_type: str = 'random_forest',
    target_column: str = 'FutureEducationTrack',
    test_size: float = 0.2,
    random_state: int = 42,
    model_save_path: str = 'models/education_model.pkl',
    preprocessing_save_dir: str = 'models',
    evaluation_save_dir: str = 'results'
) -> Dict[str, Any]:
    """
    Complete training and evaluation pipeline.
    
    Args:
        df: Input DataFrame
        model_type: Type of model ('random_forest' or 'gradient_boosting')
        target_column: Name of the target column
        test_size: Proportion of test set
        random_state: Random seed
        model_save_path: Path to save the trained model
        preprocessing_save_dir: Directory to save preprocessing objects
        evaluation_save_dir: Directory to save evaluation results
    
    Returns:
        Dictionary containing model, metrics, and other information
    """
    logger.info("=" * 80)
    logger.info("STARTING MODEL TRAINING PIPELINE")
    logger.info("=" * 80)
    
    # Prepare data
    X, y, encoders, scaler = prepare_data(df, target_column)
    
    # Split data
    X_train, X_test, y_train, y_test = split_data(X, y, test_size=test_size, random_state=random_state)
    
    # Train model
    if model_type.lower() == 'random_forest':
        model = train_random_forest(X_train, y_train, random_state=random_state)
    elif model_type.lower() == 'gradient_boosting':
        model = train_gradient_boosting(X_train, y_train, random_state=random_state)
    else:
        raise ValueError(f"Unknown model type: {model_type}")
    
    # Make predictions
    y_pred = model.predict(X_test)
    
    # Get class labels
    target_encoder = encoders['target']
    class_labels = target_encoder.classes_
    
    # Evaluate model
    metrics = evaluate_model(y_test, y_pred, class_labels=class_labels)
    
    # Save model
    save_model(model, model_save_path)
    
    # Save preprocessing objects
    save_preprocessing_objects(encoders, scaler, preprocessing_save_dir)
    
    # Save evaluation results
    os.makedirs(evaluation_save_dir, exist_ok=True)
    
    # Save evaluation report
    report_path = os.path.join(evaluation_save_dir, 'evaluation_report.txt')
    save_evaluation_report(metrics, report_path)
    
    # Plot confusion matrix
    cm_path = os.path.join(evaluation_save_dir, 'confusion_matrix.png')
    plot_confusion_matrix(metrics['confusion_matrix'], class_labels, save_path=cm_path)
    
    # Plot feature importance
    fi_path = os.path.join(evaluation_save_dir, 'feature_importance.png')
    plot_feature_importance(model, X.columns.tolist(), save_path=fi_path)
    
    logger.info("=" * 80)
    logger.info("TRAINING PIPELINE COMPLETED SUCCESSFULLY")
    logger.info("=" * 80)
    
    return {
        'model': model,
        'metrics': metrics,
        'encoders': encoders,
        'scaler': scaler,
        'feature_names': X.columns.tolist(),
        'class_labels': class_labels
    }