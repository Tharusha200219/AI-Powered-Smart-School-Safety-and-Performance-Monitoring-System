# Student Performance Prediction & Education Recommendation System

A production-ready machine learning system that predicts a student's future education track based on performance data.

## 🎯 Project Overview

This system analyzes student performance data including:

- Academic metrics (exam scores, final grades, attendance)
- Engagement metrics (study hours, discussions, assignment completion)
- Personal factors (motivation, stress level, learning style)

And recommends appropriate future education tracks such as:

- Advanced Level Stream
- Technology Stream
- Commerce Stream
- Average Progress
- Needs Extra Support

## 📁 Project Structure

```
student-performance-prediction-model/
├── data/
│   ├── raw/              # Raw data files
│   ├── processed/        # Processed data files
│   ├── dataset.csv       # Training dataset
│   └── merged_dataset.csv
├── models/
│   ├── education_model.pkl    # Trained model
│   ├── label_encoder.pkl      # Feature encoders
│   └── scaler.pkl            # Feature scaler
├── src/
│   ├── main.py           # Main application entry point
│   ├── inference.py      # Inference/prediction module
│   └── pipeline.py       # ML pipeline orchestration
├── training/
│   ├── train_model.py    # Model training
│   ├── preprocess.py     # Data preprocessing
│   └── evaluate.py       # Model evaluation
├── utils/
│   ├── load_data.py          # Data loading utilities
│   ├── transform_real_data.py # Real data transformation
│   ├── feature_engineering.py # Feature engineering
│   └── logger.py             # Logging utilities
├── results/              # Evaluation results (auto-generated)
├── logs/                 # Application logs (auto-generated)
└── requirements.txt      # Python dependencies
```

## 🚀 Quick Start

### 1. Install Dependencies

```bash
pip install -r requirements.txt
```

### 2. Run Demo Mode (Recommended First)

```bash
python src/main.py --mode demo
```

This will:

- Load the trained model (or prompt you to train first)
- Create mock student data
- Make predictions
- Display results with confidence scores

### 3. Train the Model

```bash
python src/main.py --mode train --data data/dataset.csv
```

Options:

- `--model-type random_forest` (default) or `--model-type gradient_boosting`

### 4. Run Inference

```bash
python src/main.py --mode inference
```

## 💻 Usage Examples

### Training Pipeline

```python
from src.pipeline import run_training_pipeline

results = run_training_pipeline(
    data_path='data/dataset.csv',
    model_type='random_forest',
    model_save_path='models/education_model.pkl',
    preprocessing_save_dir='models',
    evaluation_save_dir='results'
)

print(f"Accuracy: {results['metrics']['accuracy']:.2%}")
```

### Making Predictions

```python
from src.inference import StudentPerformancePredictor

# Initialize predictor
predictor = StudentPerformancePredictor(
    model_path='models/education_model.pkl',
    encoder_path='models/label_encoder.pkl',
    scaler_path='models/scaler.pkl'
)

# Prepare student features
features = {
    'StudyHours': 6.5,
    'Attendance': 90.0,
    'Resources': 4,
    'Extracurricular': 3,
    'Motivation': 4,
    'Internet': 1,
    'Gender': 'Male',
    'Age': 15,
    'LearningStyle': 'Visual',
    'OnlineCourses': 5,
    'Discussions': 7,
    'AssignmentCompletion': 92.0,
    'ExamScore': 87.4,
    'EduTech': 1,
    'StressLevel': 2,
    'FinalGrade': 87.4
}

# Make prediction
result = predictor.predict(features)

print(f"Predicted Track: {result['predicted_track']}")
print(f"Confidence: {result['confidence']:.2%}")
```

### Converting Real School Data

```python
from utils.transform_real_data import prepare_student_features

# Real school database records
student = {
    'student_id': 'STU001',
    'first_name': 'John',
    'last_name': 'Doe',
    'date_of_birth': '2010-05-15',
    'gender': 'Male',
    'grade_level': '9'
}

attendance_records = [
    {'attendance_id': 1, 'student_id': 'STU001', 'status': 'Present'},
    {'attendance_id': 2, 'student_id': 'STU001', 'status': 'Present'},
    # ... more records
]

subject_records = [
    {'id': 1, 'student_id': 'STU001', 'subject_id': 'MATH', 'grade': 85},
    {'id': 2, 'student_id': 'STU001', 'subject_id': 'SCI', 'grade': 88},
    # ... more subjects
]

additional_data = {
    'StudyHours': 6.5,
    'Resources': 4,
    'Extracurricular': 3,
    # ... other metrics
}

# Convert to model features
features = prepare_student_features(
    student=student,
    attendance_records=attendance_records,
    subject_records=subject_records,
    additional_data=additional_data
)

# Make prediction
result = predictor.predict(features)
```

## 📊 Model Features

The model uses the following features:

| Feature              | Description                   | Type        | Range/Values                |
| -------------------- | ----------------------------- | ----------- | --------------------------- |
| StudyHours           | Average study hours per day   | Numerical   | 0-24                        |
| Attendance           | Attendance percentage         | Numerical   | 0-100                       |
| Resources            | Access to learning resources  | Numerical   | 1-5                         |
| Extracurricular      | Participation in activities   | Numerical   | 0-5                         |
| Motivation           | Student motivation level      | Numerical   | 1-5                         |
| Internet             | Internet access               | Categorical | 0 or 1                      |
| Gender               | Student gender                | Categorical | Male/Female/Other           |
| Age                  | Student age                   | Numerical   | 5-25                        |
| LearningStyle        | Preferred learning style      | Categorical | Visual/Auditory/Kinesthetic |
| OnlineCourses        | Number of online courses      | Numerical   | 0-10                        |
| Discussions          | Participation in discussions  | Numerical   | 0-10                        |
| AssignmentCompletion | Assignment completion rate    | Numerical   | 0-100                       |
| ExamScore            | Average exam score            | Numerical   | 0-100                       |
| EduTech              | Use of educational technology | Categorical | 0 or 1                      |
| StressLevel          | Student stress level          | Numerical   | 1-5                         |
| FinalGrade           | Final grade                   | Numerical   | 0-100                       |

### Engineered Features (Auto-created)

- **PerformanceIndex**: `(ExamScore * 0.6) + (FinalGrade * 0.4)`
- **EngagementScore**: `Attendance + Extracurricular + Discussions`

## 📈 Model Performance

The model is evaluated using:

- **Accuracy**: Overall prediction accuracy
- **F1 Score**: Weighted and macro averaged F1 scores
- **Precision & Recall**: Per-class precision and recall
- **Confusion Matrix**: Visualization of predictions vs actual
- **Feature Importance**: Most influential features

Results are saved in the `results/` directory:

- `evaluation_report.txt`: Detailed metrics
- `confusion_matrix.png`: Confusion matrix heatmap
- `feature_importance.png`: Feature importance chart

## 🔧 Configuration

### Command Line Arguments

```bash
python src/main.py [OPTIONS]

Options:
  --mode {train,inference,demo}
                        Operating mode (default: demo)
  --data DATA           Path to training dataset (default: data/dataset.csv)
  --model-type {random_forest,gradient_boosting}
                        Model type to train (default: random_forest)
```

## 📝 Real School Database Integration

The system includes utilities to convert real school database tables:

### Attendance Table

- Converts attendance records to attendance percentage
- Handles various status formats (Present/Absent, P/A, 1/0)

### Students Table

- Extracts demographic information (age, gender)
- Maps gender values to standard format

### Student Subject Table

- Calculates average exam scores across subjects
- Handles both numeric and letter grades

See `utils/transform_real_data.py` for implementation details.

## 🧪 Testing

Use mock data for testing:

```python
from utils.transform_real_data import create_mock_student_data

mock_data = create_mock_student_data()
# Returns complete mock student, attendance, and subject data
```

## 📋 Logs

Application logs are stored in `logs/` directory:

- Separate log file for each module
- Daily rotation with timestamp
- Both INFO and ERROR level logging

## 🤝 Support

For issues or questions:

1. Check the logs in `logs/` directory
2. Review error messages in console output
3. Verify all dependencies are installed
4. Ensure model files exist in `models/` directory

## 📄 License

This is an educational project for student performance prediction.

---

**Built with**: Python, scikit-learn, pandas, numpy, matplotlib, seaborn
