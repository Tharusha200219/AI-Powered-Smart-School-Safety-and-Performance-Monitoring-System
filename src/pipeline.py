"""
Pipeline module for the student performance prediction model.

This module orchestrates the end-to-end machine learning pipeline,
including data preprocessing, model training, and evaluation.
"""

import os
from typing import Dict, Any
from utils.load_data import load_csv_data
from training.train_model import train_and_evaluate_model
from utils.logger import get_logger

logger = get_logger(__name__)


class MLPipeline:
    """
    Machine Learning Pipeline for student performance prediction.
    """
    
    def __init__(
        self,
        data_path: str,
        model_type: str = 'random_forest',
        target_column: str = 'FutureEducationTrack',
        test_size: float = 0.2,
        random_state: int = 42
    ):
        """
        Initialize the ML pipeline.
        
        Args:
            data_path: Path to the dataset CSV file
            model_type: Type of model to train ('random_forest' or 'gradient_boosting')
            target_column: Name of the target column
            test_size: Proportion of test set (0-1)
            random_state: Random seed for reproducibility
        """
        self.data_path = data_path
        self.model_type = model_type
        self.target_column = target_column
        self.test_size = test_size
        self.random_state = random_state
        
        self.model = None
        self.metrics = None
        self.encoders = None
        self.scaler = None
        self.feature_names = None
        self.class_labels = None
    
    def run(
        self,
        model_save_path: str = 'models/education_model.pkl',
        preprocessing_save_dir: str = 'models',
        evaluation_save_dir: str = 'results'
    ) -> Dict[str, Any]:
        """
        Run the complete ML pipeline.
        
        Args:
            model_save_path: Path to save the trained model
            preprocessing_save_dir: Directory to save preprocessing objects
            evaluation_save_dir: Directory to save evaluation results
        
        Returns:
            Dictionary containing pipeline results
        """
        logger.info("Starting ML Pipeline")
        logger.info(f"Data path: {self.data_path}")
        logger.info(f"Model type: {self.model_type}")
        logger.info(f"Target column: {self.target_column}")
        
        # Load data
        df = load_csv_data(self.data_path)
        
        if df is None:
            logger.error("Failed to load data. Pipeline aborted.")
            return None
        
        # Train and evaluate model
        results = train_and_evaluate_model(
            df=df,
            model_type=self.model_type,
            target_column=self.target_column,
            test_size=self.test_size,
            random_state=self.random_state,
            model_save_path=model_save_path,
            preprocessing_save_dir=preprocessing_save_dir,
            evaluation_save_dir=evaluation_save_dir
        )
        
        # Store results
        self.model = results['model']
        self.metrics = results['metrics']
        self.encoders = results['encoders']
        self.scaler = results['scaler']
        self.feature_names = results['feature_names']
        self.class_labels = results['class_labels']
        
        logger.info("ML Pipeline completed successfully")
        
        return results
    
    def get_summary(self) -> Dict[str, Any]:
        """
        Get a summary of the pipeline results.
        
        Returns:
            Dictionary containing summary information
        """
        if self.metrics is None:
            logger.warning("Pipeline has not been run yet")
            return None
        
        summary = {
            'model_type': self.model_type,
            'accuracy': self.metrics['accuracy'],
            'f1_score_weighted': self.metrics['f1_score_weighted'],
            'precision': self.metrics['precision'],
            'recall': self.metrics['recall'],
            'num_features': len(self.feature_names),
            'num_classes': len(self.class_labels),
            'class_labels': list(self.class_labels)
        }
        
        return summary


def run_training_pipeline(
    data_path: str = 'data/dataset.csv',
    model_type: str = 'random_forest',
    target_column: str = 'FutureEducationTrack',
    model_save_path: str = 'models/education_model.pkl',
    preprocessing_save_dir: str = 'models',
    evaluation_save_dir: str = 'results'
) -> Dict[str, Any]:
    """
    Convenience function to run the training pipeline.
    
    Args:
        data_path: Path to the dataset CSV file
        model_type: Type of model to train
        target_column: Name of the target column
        model_save_path: Path to save the trained model
        preprocessing_save_dir: Directory to save preprocessing objects
        evaluation_save_dir: Directory to save evaluation results
    
    Returns:
        Dictionary containing pipeline results
    """
    pipeline = MLPipeline(
        data_path=data_path,
        model_type=model_type,
        target_column=target_column
    )
    
    results = pipeline.run(
        model_save_path=model_save_path,
        preprocessing_save_dir=preprocessing_save_dir,
        evaluation_save_dir=evaluation_save_dir
    )
    
    if results:
        summary = pipeline.get_summary()
        logger.info("\n" + "=" * 80)
        logger.info("PIPELINE SUMMARY")
        logger.info("=" * 80)
        for key, value in summary.items():
            logger.info(f"{key}: {value}")
        logger.info("=" * 80)
    
    return results