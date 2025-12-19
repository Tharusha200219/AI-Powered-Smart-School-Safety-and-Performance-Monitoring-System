"""
Main entry point for the student performance prediction model.

This module handles the overall execution of the prediction system,
including training, inference, and pipeline orchestration.
"""

import os
import sys
import argparse
from typing import Dict, Any

# Add the project root to Python path
project_root = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
sys.path.insert(0, project_root)

from utils.logger import get_logger
from utils.transform_real_data import (
    create_mock_student_data,
    prepare_student_features,
    prepare_batch_student_features
)
from src.inference import StudentPerformancePredictor
from src.pipeline import run_training_pipeline

logger = get_logger(__name__)


def print_banner():
    """Print application banner."""
    banner = """
    ╔═══════════════════════════════════════════════════════════════╗
    ║   STUDENT PERFORMANCE PREDICTION & EDUCATION RECOMMENDATION   ║
    ║                    Machine Learning System                    ║
    ╚═══════════════════════════════════════════════════════════════╝
    """
    print(banner)


def print_prediction_result(student_id: str, result: Dict[str, Any]) -> None:
    """
    Print prediction result in a formatted way.
    
    Args:
        student_id: Student identifier
        result: Prediction result dictionary
    """
    print("\n" + "=" * 80)
    print(f"PREDICTION RESULT FOR STUDENT: {student_id}")
    print("=" * 80)
    print(f"\n📚 Recommended Future Education Track: {result['predicted_track']}")
    print(f"🎯 Confidence Level: {result['confidence']:.2%}")
    
    if 'class_probabilities' in result:
        print("\n📊 All Track Probabilities:")
        print("-" * 80)
        for track, prob in sorted(result['class_probabilities'].items(), key=lambda x: x[1], reverse=True):
            bar_length = int(prob * 50)
            bar = "█" * bar_length + "░" * (50 - bar_length)
            print(f"  {track:30s} | {bar} {prob:.2%}")
    
    print("=" * 80 + "\n")


def run_training_mode(
    data_path: str = 'data/dataset.csv',
    model_type: str = 'random_forest'
) -> None:
    """
    Run the training pipeline.
    
    Args:
        data_path: Path to the training dataset
        model_type: Type of model to train
    """
    logger.info("Starting training mode")
    print("\n🚀 TRAINING MODE - Training model on dataset...\n")
    
    if not os.path.exists(data_path):
        logger.error(f"Dataset not found: {data_path}")
        print(f"❌ Error: Dataset file not found at {data_path}")
        return
    
    # Run training pipeline
    results = run_training_pipeline(
        data_path=data_path,
        model_type=model_type,
        model_save_path='models/education_model.pkl',
        preprocessing_save_dir='models',
        evaluation_save_dir='results'
    )
    
    if results:
        print("\n✅ Training completed successfully!")
        print(f"   Model saved to: models/education_model.pkl")
        print(f"   Accuracy: {results['metrics']['accuracy']:.2%}")
        print(f"   F1 Score: {results['metrics']['f1_score_weighted']:.4f}")
    else:
        print("\n❌ Training failed. Check logs for details.")


def run_inference_mode(
    use_mock_data: bool = True,
    student_data: Dict[str, Any] = None
) -> None:
    """
    Run inference on student data.
    
    Args:
        use_mock_data: Whether to use mock data or provided data
        student_data: Dictionary containing student information
    """
    logger.info("Starting inference mode")
    print("\n🔮 INFERENCE MODE - Making predictions...\n")
    
    # Check if model exists
    if not os.path.exists('models/education_model.pkl'):
        logger.error("Model not found. Please train the model first.")
        print("❌ Error: Model not found. Please run training mode first.")
        return
    
    # Load predictor
    try:
        predictor = StudentPerformancePredictor(
            model_path='models/education_model.pkl',
            encoder_path='models/label_encoder.pkl',
            scaler_path='models/scaler.pkl'
        )
        print("✅ Model loaded successfully\n")
        
        # Display model info
        model_info = predictor.get_model_info()
        print(f"📋 Model Information:")
        print(f"   Type: {model_info['model_type']}")
        print(f"   Number of Features: {model_info['num_features']}")
        print(f"   Number of Classes: {model_info['num_classes']}")
        print(f"   Available Tracks: {', '.join(model_info['class_labels'])}\n")
        
    except Exception as e:
        logger.error(f"Error loading model: {str(e)}")
        print(f"❌ Error loading model: {str(e)}")
        return
    
    # Get student data
    if use_mock_data:
        print("📝 Using mock student data for demonstration...\n")
        mock_data = create_mock_student_data()
        
        # Prepare features
        features = prepare_student_features(
            student=mock_data['student'],
            attendance_records=mock_data['attendance_records'],
            subject_records=mock_data['subject_records'],
            additional_data=mock_data['additional_data']
        )
        
        student_id = mock_data['student']['student_id']
        student_name = f"{mock_data['student']['first_name']} {mock_data['student']['last_name']}"
        
        # Display student information
        print(f"👤 Student Information:")
        print(f"   ID: {student_id}")
        print(f"   Name: {student_name}")
        print(f"   Age: {features['Age']}")
        print(f"   Gender: {features['Gender']}")
        print(f"   Grade Level: {mock_data['student']['grade_level']}")
        print(f"\n📊 Performance Metrics:")
        print(f"   Attendance: {features['Attendance']:.1f}%")
        print(f"   Exam Score: {features['ExamScore']:.1f}")
        print(f"   Final Grade: {features['FinalGrade']:.1f}")
        print(f"   Study Hours/Day: {features['StudyHours']}")
        print(f"   Assignment Completion: {features['AssignmentCompletion']:.1f}%")
        
    else:
        if student_data is None:
            logger.error("No student data provided")
            print("❌ Error: No student data provided")
            return
        
        features = student_data
        student_id = features.get('student_id', 'Unknown')
    
    # Make prediction
    try:
        print("\n🔍 Analyzing student performance and making prediction...\n")
        result = predictor.predict(features)
        
        # Print result
        print_prediction_result(student_id, result)
        
        # Provide recommendation
        print("💡 Recommendation:")
        track = result['predicted_track']
        confidence = result['confidence']
        
        if confidence >= 0.8:
            confidence_level = "high confidence"
        elif confidence >= 0.6:
            confidence_level = "moderate confidence"
        else:
            confidence_level = "low confidence"
        
        print(f"   Based on the student's performance data, with {confidence_level},")
        print(f"   we recommend enrolling in the '{track}' educational track.")
        print(f"\n   This recommendation considers:")
        print(f"   • Academic performance (exam scores, final grades)")
        print(f"   • Engagement metrics (attendance, participation)")
        print(f"   • Learning behaviors (study hours, assignment completion)")
        print(f"   • Personal factors (motivation, stress level)")
        
    except Exception as e:
        logger.error(f"Error during prediction: {str(e)}")
        print(f"❌ Error during prediction: {str(e)}")


def run_demo_mode() -> None:
    """Run a complete demonstration with multiple students."""
    logger.info("Starting demo mode")
    print("\n🎭 DEMO MODE - Complete Demonstration\n")
    
    print("This demo will:")
    print("1. Load the trained model")
    print("2. Create sample student data")
    print("3. Make predictions")
    print("4. Display results\n")
    
    run_inference_mode(use_mock_data=True)


def main():
    """Main application entry point."""
    print_banner()
    
    # Parse command line arguments
    parser = argparse.ArgumentParser(
        description='Student Performance Prediction & Education Recommendation System'
    )
    parser.add_argument(
        '--mode',
        type=str,
        choices=['train', 'inference', 'demo'],
        default='demo',
        help='Operating mode: train, inference, or demo'
    )
    parser.add_argument(
        '--data',
        type=str,
        default='data/dataset.csv',
        help='Path to the training dataset (for train mode)'
    )
    parser.add_argument(
        '--model-type',
        type=str,
        choices=['random_forest', 'gradient_boosting'],
        default='random_forest',
        help='Type of model to train'
    )
    
    args = parser.parse_args()
    
    # Execute based on mode
    if args.mode == 'train':
        run_training_mode(data_path=args.data, model_type=args.model_type)
    elif args.mode == 'inference':
        run_inference_mode(use_mock_data=True)
    elif args.mode == 'demo':
        run_demo_mode()
    
    print("\n✨ Application completed. Thank you!\n")


if __name__ == "__main__":
    try:
        main()
    except KeyboardInterrupt:
        print("\n\n⚠️  Application interrupted by user.")
        sys.exit(0)
    except Exception as e:
        logger.error(f"Application error: {str(e)}")
        print(f"\n❌ Application error: {str(e)}")
        sys.exit(1)