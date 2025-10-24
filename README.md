# Student Performance Prediction Model

A machine learning model to predict student academic performance (Pass/Fail) based on attendance, study hours, past exam scores, and other factors. This model can be integrated with your Laravel web application.

## 🎯 Features

- **High Accuracy**: Uses ensemble methods (Random Forest & Gradient Boosting) with hyperparameter tuning
- **Real-time Predictions**: Flask API for easy integration with Laravel
- **Batch Processing**: Predict performance for multiple students at once
- **Feature Engineering**: Advanced features like Study-Attendance Score and Performance Index

## 📊 Model Input Features

The model uses the following features:

1. **Study Hours per Week** (numeric): Hours spent studying weekly
2. **Attendance Rate** (numeric): Percentage of classes attended (0-100)
3. **Past Exam Scores** (numeric): Previous exam performance (0-100)
4. **Gender** (categorical): "Male" or "Female"
5. **Parental Education Level** (categorical): "High School", "Bachelors", "Masters", or "PhD"
6. **Internet Access at Home** (categorical): "Yes" or "No"
7. **Extracurricular Activities** (categorical): "Yes" or "No"

## 🗄️ Required Database Columns for Laravel Application

Your Laravel application should have a `students` table with these columns:

```sql
CREATE TABLE students (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL,
    
    -- Academic data
    study_hours_per_week DECIMAL(5, 2) DEFAULT 0,
    attendance_rate DECIMAL(5, 2) DEFAULT 0,  -- Calculated from RFID attendance
    past_exam_scores DECIMAL(5, 2) DEFAULT 0,  -- Average of past exams
    
    -- Background information
    parental_education_level ENUM('High School', 'Bachelors', 'Masters', 'PhD'),
    internet_access_at_home ENUM('Yes', 'No') DEFAULT 'Yes',
    extracurricular_activities ENUM('Yes', 'No') DEFAULT 'No',
    
    -- Prediction results (updated by API)
    predicted_performance ENUM('Pass', 'Fail') NULL,
    prediction_confidence DECIMAL(5, 4) NULL,
    last_prediction_date TIMESTAMP NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Attendance table (for RFID tracking)
CREATE TABLE attendance (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) NOT NULL,
    rfid_tag VARCHAR(50) NOT NULL,
    check_in_time TIMESTAMP NOT NULL,
    subject_code VARCHAR(20),
    status ENUM('Present', 'Late', 'Absent') DEFAULT 'Present',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id)
);

-- Exam scores table
CREATE TABLE exam_scores (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) NOT NULL,
    exam_name VARCHAR(255) NOT NULL,
    subject_code VARCHAR(20),
    score DECIMAL(5, 2) NOT NULL,
    max_score DECIMAL(5, 2) DEFAULT 100,
    exam_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id)
);
```

## 🚀 Installation & Setup

### 1. Install Python Dependencies

```bash
# Create virtual environment (recommended)
python3 -m venv venv
source venv/bin/activate  # On macOS/Linux
# or
venv\Scripts\activate  # On Windows

# Install required packages
pip install -r requirements.txt
```

### 2. Train the Model

```bash
python train_model.py
```

This will:
- Load and preprocess the dataset
- Train multiple models with hyperparameter tuning
- Select the best performing model
- Save the model and preprocessors as `student_performance_model.pkl`

### 3. Test the Model

```bash
python predict_simple.py
```

This runs example predictions to verify the model works correctly.

### 4. Start the API Server

```bash
python predict_api.py
```

The API will start on `http://localhost:5000`

## 🔌 Laravel Integration

### Method 1: Direct API Calls (Recommended)

Create a service in Laravel to communicate with the Python API:

```php
<?php
// app/Services/StudentPerformancePredictionService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StudentPerformancePredictionService
{
    private $apiUrl = 'http://localhost:5000';
    
    public function predictSingleStudent($studentData)
    {
        try {
            $response = Http::timeout(30)->post($this->apiUrl . '/predict', [
                'study_hours_per_week' => $studentData['study_hours_per_week'],
                'attendance_rate' => $studentData['attendance_rate'],
                'past_exam_scores' => $studentData['past_exam_scores'],
                'gender' => $studentData['gender'],
                'parental_education_level' => $studentData['parental_education_level'],
                'internet_access_at_home' => $studentData['internet_access_at_home'],
                'extracurricular_activities' => $studentData['extracurricular_activities'],
            ]);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            Log::error('Prediction API Error: ' . $response->body());
            return null;
            
        } catch (\Exception $e) {
            Log::error('Prediction Service Error: ' . $e->getMessage());
            return null;
        }
    }
    
    public function predictBatchStudents($studentsData)
    {
        try {
            $response = Http::timeout(60)->post($this->apiUrl . '/predict_batch', [
                'students' => $studentsData
            ]);
            
            if ($response->successful()) {
                return $response->json();
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Batch Prediction Error: ' . $e->getMessage());
            return null;
        }
    }
    
    public function checkApiHealth()
    {
        try {
            $response = Http::timeout(5)->get($this->apiUrl . '/health');
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
```

### Method 2: Laravel Controller Example

```php
<?php
// app/Http/Controllers/StudentPredictionController.php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Services\StudentPerformancePredictionService;
use Illuminate\Http\Request;

class StudentPredictionController extends Controller
{
    protected $predictionService;
    
    public function __construct(StudentPerformancePredictionService $predictionService)
    {
        $this->predictionService = $predictionService;
    }
    
    public function predictStudent($studentId)
    {
        $student = Student::findOrFail($studentId);
        
        // Calculate attendance rate from RFID data
        $attendanceRate = $this->calculateAttendanceRate($student);
        
        // Calculate average past exam scores
        $pastExamScores = $this->calculateAverageExamScores($student);
        
        $studentData = [
            'study_hours_per_week' => $student->study_hours_per_week,
            'attendance_rate' => $attendanceRate,
            'past_exam_scores' => $pastExamScores,
            'gender' => $student->gender,
            'parental_education_level' => $student->parental_education_level,
            'internet_access_at_home' => $student->internet_access_at_home,
            'extracurricular_activities' => $student->extracurricular_activities,
        ];
        
        $result = $this->predictionService->predictSingleStudent($studentData);
        
        if ($result) {
            // Update student record with prediction
            $student->update([
                'predicted_performance' => $result['prediction'],
                'prediction_confidence' => $result['confidence'],
                'last_prediction_date' => now(),
            ]);
            
            return response()->json([
                'success' => true,
                'student_id' => $student->student_id,
                'prediction' => $result['prediction'],
                'confidence' => $result['confidence'],
                'probabilities' => $result['probabilities'],
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Prediction failed'
        ], 500);
    }
    
    private function calculateAttendanceRate($student)
    {
        $totalClasses = $student->attendance()->count();
        $presentClasses = $student->attendance()
            ->where('status', 'Present')
            ->count();
            
        return $totalClasses > 0 ? ($presentClasses / $totalClasses) * 100 : 0;
    }
    
    private function calculateAverageExamScores($student)
    {
        return $student->examScores()
            ->avg('score') ?? 0;
    }
}
```

### Method 3: Scheduled Batch Predictions

```php
<?php
// app/Console/Commands/UpdateStudentPredictions.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Student;
use App\Services\StudentPerformancePredictionService;

class UpdateStudentPredictions extends Command
{
    protected $signature = 'students:update-predictions';
    protected $description = 'Update performance predictions for all students';
    
    public function handle(StudentPerformancePredictionService $service)
    {
        $this->info('Updating student predictions...');
        
        $students = Student::all();
        $studentsData = [];
        
        foreach ($students as $student) {
            $studentsData[] = [
                'student_id' => $student->student_id,
                'study_hours_per_week' => $student->study_hours_per_week,
                'attendance_rate' => $student->attendance_rate,
                'past_exam_scores' => $student->past_exam_scores,
                'gender' => $student->gender,
                'parental_education_level' => $student->parental_education_level,
                'internet_access_at_home' => $student->internet_access_at_home,
                'extracurricular_activities' => $student->extracurricular_activities,
            ];
        }
        
        $result = $service->predictBatchStudents($studentsData);
        
        if ($result) {
            foreach ($result['predictions'] as $prediction) {
                $student = Student::where('student_id', $prediction['student_id'])->first();
                if ($student) {
                    $student->update([
                        'predicted_performance' => $prediction['prediction'],
                        'prediction_confidence' => $prediction['confidence'],
                        'last_prediction_date' => now(),
                    ]);
                }
            }
            
            $this->info("Successfully updated predictions for {$result['total_students']} students");
        } else {
            $this->error('Failed to update predictions');
        }
    }
}

// Add to app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // Run predictions daily at midnight
    $schedule->command('students:update-predictions')->daily();
}
```

## 📡 API Endpoints

### 1. Health Check
```bash
GET http://localhost:5000/health
```

### 2. Single Prediction
```bash
POST http://localhost:5000/predict
Content-Type: application/json

{
    "study_hours_per_week": 25,
    "attendance_rate": 85.5,
    "past_exam_scores": 75,
    "gender": "Male",
    "parental_education_level": "Bachelors",
    "internet_access_at_home": "Yes",
    "extracurricular_activities": "Yes"
}
```

### 3. Batch Prediction
```bash
POST http://localhost:5000/predict_batch
Content-Type: application/json

{
    "students": [
        {
            "student_id": "S001",
            "study_hours_per_week": 25,
            "attendance_rate": 85.5,
            ...
        }
    ]
}
```

### 4. Model Information
```bash
GET http://localhost:5000/model_info
```

## 🔄 Automated Workflow

1. **RFID Attendance**: Laravel records attendance automatically
2. **Exam Entry**: Teachers manually enter exam scores
3. **Scheduled Prediction**: Laravel runs batch predictions daily
4. **Dashboard Display**: Show at-risk students to administrators
5. **Intervention**: Take action for students predicted to fail

## 📈 Model Performance

The model is trained using:
- **Random Forest** and **Gradient Boosting** with GridSearchCV
- **Cross-validation** for robust performance estimation
- **Feature engineering** for improved accuracy
- Expected accuracy: **85-95%** depending on data quality

## 🛠️ Troubleshooting

### API Connection Issues
```bash
# Check if API is running
curl http://localhost:5000/health

# Check Python process
ps aux | grep predict_api
```

### Model Loading Errors
```bash
# Retrain the model
python train_model.py
```

### Missing Dependencies
```bash
pip install -r requirements.txt
```

## 📝 Notes

- Ensure the Python API is always running for real-time predictions
- Use a process manager like `supervisor` or `systemd` for production
- Consider using Docker for easier deployment
- Regularly retrain the model with new data to maintain accuracy

## 🚀 Production Deployment

For production, consider:
1. Using Gunicorn instead of Flask development server
2. Setting up Nginx as reverse proxy
3. Implementing API authentication
4. Adding rate limiting
5. Using Docker containers
6. Setting up monitoring and logging
