"""
Model evaluation module for the student performance prediction system.

This module contains functions to evaluate the trained model's performance,
including metrics calculation, validation, and performance analysis.
"""

import numpy as np
import pandas as pd
from sklearn.metrics import (
    accuracy_score,
    f1_score,
    classification_report,
    confusion_matrix,
    precision_score,
    recall_score
)
from typing import Dict, Any, Tuple
import matplotlib.pyplot as plt
import seaborn as sns
import os
from utils.logger import get_logger

logger = get_logger(__name__)


def evaluate_model(
    y_true: np.ndarray,
    y_pred: np.ndarray,
    class_labels: list = None
) -> Dict[str, Any]:
    """
    Evaluate model performance using multiple metrics.
    
    Args:
        y_true: True labels
        y_pred: Predicted labels
        class_labels: List of class label names
    
    Returns:
        Dictionary containing evaluation metrics
    """
    logger.info("Evaluating model performance")
    
    # Calculate metrics
    accuracy = accuracy_score(y_true, y_pred)
    f1_macro = f1_score(y_true, y_pred, average='macro')
    f1_weighted = f1_score(y_true, y_pred, average='weighted')
    precision = precision_score(y_true, y_pred, average='weighted', zero_division=0)
    recall = recall_score(y_true, y_pred, average='weighted', zero_division=0)
    
    # Generate classification report
    class_report = classification_report(
        y_true, 
        y_pred, 
        target_names=class_labels,
        zero_division=0
    )
    
    # Generate confusion matrix
    conf_matrix = confusion_matrix(y_true, y_pred)
    
    metrics = {
        'accuracy': accuracy,
        'f1_score_macro': f1_macro,
        'f1_score_weighted': f1_weighted,
        'precision': precision,
        'recall': recall,
        'classification_report': class_report,
        'confusion_matrix': conf_matrix
    }
    
    # Log metrics
    logger.info(f"Accuracy: {accuracy:.4f}")
    logger.info(f"F1 Score (Macro): {f1_macro:.4f}")
    logger.info(f"F1 Score (Weighted): {f1_weighted:.4f}")
    logger.info(f"Precision: {precision:.4f}")
    logger.info(f"Recall: {recall:.4f}")
    logger.info(f"\nClassification Report:\n{class_report}")
    
    return metrics


def plot_confusion_matrix(
    conf_matrix: np.ndarray,
    class_labels: list,
    save_path: str = None,
    figsize: Tuple[int, int] = (10, 8)
) -> None:
    """
    Plot confusion matrix as a heatmap.
    
    Args:
        conf_matrix: Confusion matrix array
        class_labels: List of class label names
        save_path: Path to save the plot (optional)
        figsize: Figure size tuple
    """
    logger.info("Plotting confusion matrix")
    
    plt.figure(figsize=figsize)
    sns.heatmap(
        conf_matrix,
        annot=True,
        fmt='d',
        cmap='Blues',
        xticklabels=class_labels,
        yticklabels=class_labels,
        cbar_kws={'label': 'Count'}
    )
    plt.title('Confusion Matrix', fontsize=16, fontweight='bold')
    plt.ylabel('True Label', fontsize=12)
    plt.xlabel('Predicted Label', fontsize=12)
    plt.tight_layout()
    
    if save_path:
        os.makedirs(os.path.dirname(save_path), exist_ok=True)
        plt.savefig(save_path, dpi=300, bbox_inches='tight')
        logger.info(f"Confusion matrix saved to {save_path}")
    
    plt.close()


def plot_feature_importance(
    model: Any,
    feature_names: list,
    save_path: str = None,
    top_n: int = 20,
    figsize: Tuple[int, int] = (10, 8)
) -> None:
    """
    Plot feature importance for tree-based models.
    
    Args:
        model: Trained model with feature_importances_ attribute
        feature_names: List of feature names
        save_path: Path to save the plot (optional)
        top_n: Number of top features to display
        figsize: Figure size tuple
    """
    if not hasattr(model, 'feature_importances_'):
        logger.warning("Model does not have feature_importances_ attribute")
        return
    
    logger.info("Plotting feature importance")
    
    # Get feature importances
    importances = model.feature_importances_
    indices = np.argsort(importances)[::-1][:top_n]
    
    # Create DataFrame for easier plotting
    importance_df = pd.DataFrame({
        'feature': [feature_names[i] for i in indices],
        'importance': importances[indices]
    })
    
    # Plot
    plt.figure(figsize=figsize)
    sns.barplot(data=importance_df, x='importance', y='feature', palette='viridis')
    plt.title(f'Top {top_n} Feature Importances', fontsize=16, fontweight='bold')
    plt.xlabel('Importance', fontsize=12)
    plt.ylabel('Feature', fontsize=12)
    plt.tight_layout()
    
    if save_path:
        os.makedirs(os.path.dirname(save_path), exist_ok=True)
        plt.savefig(save_path, dpi=300, bbox_inches='tight')
        logger.info(f"Feature importance plot saved to {save_path}")
    
    plt.close()


def save_evaluation_report(
    metrics: Dict[str, Any],
    save_path: str
) -> bool:
    """
    Save evaluation metrics to a text file.
    
    Args:
        metrics: Dictionary of evaluation metrics
        save_path: Path to save the report
    
    Returns:
        True if successful, False otherwise
    """
    try:
        os.makedirs(os.path.dirname(save_path), exist_ok=True)
        
        with open(save_path, 'w') as f:
            f.write("=" * 80 + "\n")
            f.write("MODEL EVALUATION REPORT\n")
            f.write("=" * 80 + "\n\n")
            
            f.write(f"Accuracy: {metrics['accuracy']:.4f}\n")
            f.write(f"F1 Score (Macro): {metrics['f1_score_macro']:.4f}\n")
            f.write(f"F1 Score (Weighted): {metrics['f1_score_weighted']:.4f}\n")
            f.write(f"Precision: {metrics['precision']:.4f}\n")
            f.write(f"Recall: {metrics['recall']:.4f}\n\n")
            
            f.write("-" * 80 + "\n")
            f.write("CLASSIFICATION REPORT\n")
            f.write("-" * 80 + "\n\n")
            f.write(metrics['classification_report'])
            f.write("\n\n")
            
            f.write("-" * 80 + "\n")
            f.write("CONFUSION MATRIX\n")
            f.write("-" * 80 + "\n\n")
            f.write(str(metrics['confusion_matrix']))
            f.write("\n")
        
        logger.info(f"Evaluation report saved to {save_path}")
        return True
    
    except Exception as e:
        logger.error(f"Error saving evaluation report: {str(e)}")
        return False


def compare_models(
    models_metrics: Dict[str, Dict[str, float]]
) -> pd.DataFrame:
    """
    Compare multiple models based on their metrics.
    
    Args:
        models_metrics: Dictionary mapping model names to their metrics
    
    Returns:
        DataFrame containing comparison results
    """
    logger.info("Comparing model performances")
    
    comparison_df = pd.DataFrame(models_metrics).T
    comparison_df = comparison_df.sort_values('accuracy', ascending=False)
    
    logger.info(f"\nModel Comparison:\n{comparison_df}")
    
    return comparison_df