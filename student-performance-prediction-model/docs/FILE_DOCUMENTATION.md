# Student Performance Prediction Model - File Documentation

## 📁 Project Structure Overview

This module predicts student performance scores using machine learning. It takes student data (age, grades, attendance, marks) and predicts their future performance with confidence intervals.

---

## 📄 Root Level Files

### `requirements.txt`

- **What it does**: Lists all Python packages needed to run this module
- **Key packages**:
  - Flask (web server)
  - scikit-learn (machine learning)
  - xgboost (advanced prediction model)
  - pandas (data handling)
- **How to use**: Run `pip install -r requirements.txt`

### `setup.sh` & `setup_prediction.sh`

- **What it does**: Automated setup scripts to install dependencies and prepare the environment
- **How to use**: Run `bash setup.sh`

### `start_api.sh`

- **What it does**: Starts the Flask API server so other applications can request predictions
- **How to use**: Run `bash start_api.sh`

### `test_*.py` files

- **test_api_errors.sh**: Tests if the API handles errors correctly
- **test_predictions.py**: Tests if predictions work with sample data
- **test_system.py**: Tests the entire system
- **api_test_example.py**: Shows example API calls

### `evaluate_model.py`

- **What it does**: Checks how accurate the trained model is
- **Outputs**: Accuracy metrics to see if model performs well

### `train_with_6000.py`

- **What it does**: Trains a new model using 6000 student records
- **When to use**: When you have new data and want to retrain

### `verify_trends.py`

- **What it does**: Verifies that predictions follow logical trends (e.g., more attendance = higher score)

### Notebook Files (`.ipynb`)

- **Model_Accuracy_Analysis.ipynb**: Jupyter notebook showing accuracy analysis with charts
- **executed_notebook.ipynb**: Already-run notebook with results
- **updated_notebook.ipynb**: Latest version of analysis

### `model_accuracy_results.json`

- **What it does**: Stores accuracy metrics and performance statistics in JSON format
- **Contains**: R² score, MAE, MSE values

---

## 🗂️ `/api` Folder - REST API

### `app.py`

- **What it does**: The main Flask web server
- **Key features**:
  - `/predict` endpoint: Accepts student data and returns performance prediction
  - `/health` endpoint: Checks if API is running
  - Handles CORS (cross-origin requests from Laravel frontend)
- **How to use**:
  ```python
  # POST request to /predict with student data
  # Returns prediction with 95% confidence interval
  ```

### `requirements.txt`

- **What it does**: Dependencies specific to the API (same as root, kept here for clarity)

---

## ⚙️ `/config` Folder - Settings

### `config.py`

- **What it does**: Stores all configuration settings in one place
- **Key settings**:
  - `API_HOST`, `API_PORT`: Where API runs (0.0.0.0:5002)
  - `MODELS_DIR`: Where trained models are saved
  - `DATASET_PATH`: Where training data is stored
  - `RF_N_ESTIMATORS`, `RF_MAX_DEPTH`: ML model settings
  - `CV_FOLDS`: Number of cross-validation folds (5)
- **Why centralized?**: Easy to change settings without editing multiple files

---

## 🧠 `/src` Folder - Core Logic

### `data_preprocessing.py`

- **What it does**: Cleans raw student data before training
- **Steps**:
  1. Load CSV file
  2. Remove unnecessary columns
  3. Handle missing values
  4. Create subject-wise records
  5. Add engineered features (attendance_score, grade_marks_ratio, risk_index)
  6. Save cleaned data
- **Why important?**: Clean data = better model accuracy

### `model_trainer.py`

- **What it does**: Trains the machine learning model
- **Process**:
  1. Load cleaned data
  2. Prepare features and labels
  3. Use One-Hot Encoding for subjects (converts text to numbers)
  4. Train RandomForestRegressor (predicts numbers, not categories)
  5. Use 5-Fold Cross-Validation for reliability
  6. Save trained model to disk
- **Model choice**: RandomForest instead of linear regression because student performance has non-linear patterns
- **Output**: Saves `performance_predictor.pkl` and `scaler.pkl` files

### `predictor.py`

- **What it does**: Makes predictions using the trained model
- **Process**:
  1. Load trained model and scaler
  2. Prepare input data (same preprocessing as training)
  3. Make prediction
  4. Calculate 95% confidence intervals
  5. Clamp results to [0, 100] range
- **Output**: Returns prediction score + confidence bounds

---

## 📊 `/data` Folder

- **What it contains**: Processed/cleaned data files
- **cleaned_data.csv**: The output after preprocessing raw data

---

## 📈 `/dataset` Folder

- **What it contains**: Raw training data
- **Example file**: `student_performance_updated_1000 (1).csv` - 1000 student records

---

## 🤖 `/models` Folder

- **What it contains**: Trained ML models
- **Files saved here**:
  - `performance_predictor.pkl`: The trained RandomForest model
  - `scaler.pkl`: Data normalizer (converts values to 0-1 range)
- **Size**: Typically 10-50MB

---

## 🧪 `/tests` Folder

- **What it contains**: Automated tests to verify everything works
- **Purpose**: Catch bugs early

---

## 📚 How Files Work Together

```
Raw Data (student_performance_updated_1000.csv)
    ↓
data_preprocessing.py (cleans & engineers features)
    ↓
cleaned_data.csv
    ↓
model_trainer.py (trains model)
    ↓
Saved Models (performance_predictor.pkl, scaler.pkl)
    ↓
predictor.py (loads models + makes predictions)
    ↓
app.py (Flask API serves predictions)
    ↓
Laravel Frontend (displays predictions to users)
```

---

## ⚡ Quick Start

1. **First time setup**: `bash setup.sh`
2. **Train model**: `python train_with_6000.py`
3. **Start API**: `bash start_api.sh`
4. **Test predictions**: `python test_predictions.py`
5. **Check accuracy**: `python evaluate_model.py`

---

## 📝 Configuration Priority

When the system runs, it reads settings in this order:

1. `config/config.py` (main settings)
2. Environment variables (override config.py if set)
3. API parameters (override everything for single request)
