# Student Performance Prediction - Data Flow

## 📊 Complete Data Flow from Dataset to UI

### 1. Dataset Structure (6000 Records)

**File:** `dataset/student_performance_6000_with_prediction.csv`

```csv
Attendance,Term1,Term2,Term3,PredictedScore
51,47,61,50,53.98
20,97,85,81,76.23
61,45,41,37,41.07
...
```

**Fields:**

- `Attendance` - Attendance percentage (0-100)
- `Term1` - First term marks (0-100)
- `Term2` - Second term marks (0-100)
- `Term3` - Third term marks (0-100)
- `PredictedScore` - Target variable for training

### 2. Training Process

**Script:** `train_with_6000.py`

```
6000 Original Records
        ↓
Expand to 5 subjects each
        ↓
30,000 Subject-wise Records
        ↓
Feature Engineering
(attendance_score, marks_avg, marks_slope, etc.)
        ↓
XGBoost Training
        ↓
Trained Model (MAE: 0.30, R²: 0.9997)
```

### 3. API Request Format

**Endpoint:** `POST http://localhost:5002/predict`

```json
{
  "student_id": 12345,
  "age": 15,
  "grade": 10,
  "subjects": [
    {
      "subject_name": "Mathematics",
      "attendance": 75,
      "term1_marks": 85,
      "term2_marks": 87,
      "term3_marks": 90
    }
  ]
}
```

### 4. API Response Format

```json
{
  "student_id": 12345,
  "age": 15,
  "grade": 10,
  "predictions": [
    {
      "subject": "Mathematics",

      "attendance": 75.0,
      "term1_marks": 85.0,
      "term2_marks": 87.0,
      "term3_marks": 90.0,

      "predicted_performance": 88.1,
      "prediction_trend": "Improving",
      "performance_category": "Excellent",
      "confidence": 0.85,

      "confidence_interval": {
        "lower_bound": 76.0,
        "upper_bound": 100.0,
        "confidence_level": 0.95
      },

      "recommendation": "Keep up the excellent work..."
    }
  ]
}
```

### 5. Laravel UI Display

**Blade Template Example:**

```blade
<div class="student-performance-card">
    <h3>{{ $student->name }} - Grade {{ $prediction['grade'] }}</h3>

    @foreach($prediction['predictions'] as $subject)
    <div class="subject-card">
        <h4>📚 {{ $subject['subject'] }}</h4>

        <div class="metrics">
            <!-- Attendance -->
            <div class="metric">
                <label>📊 Attendance</label>
                <div class="value">{{ $subject['attendance'] }}%</div>
            </div>

            <!-- Term Marks -->
            <div class="term-marks">
                <h5>Term Marks</h5>
                <div class="marks-row">
                    <div class="mark">
                        <span>Term 1</span>
                        <strong>{{ $subject['term1_marks'] }}</strong>
                    </div>
                    <div class="mark">
                        <span>Term 2</span>
                        <strong>{{ $subject['term2_marks'] }}</strong>
                    </div>
                    <div class="mark">
                        <span>Term 3</span>
                        <strong>{{ $subject['term3_marks'] }}</strong>
                    </div>
                </div>
            </div>

            <!-- Prediction -->
            <div class="prediction">
                <label>🎯 Predicted Performance</label>
                <div class="predicted-score">
                    {{ $subject['predicted_performance'] }}
                </div>
                <div class="trend {{ strtolower($subject['prediction_trend']) }}">
                    @if($subject['prediction_trend'] == 'Improving')
                        📈 Improving
                    @elseif($subject['prediction_trend'] == 'Declining')
                        📉 Declining
                    @else
                        ➡️ Stable
                    @endif
                </div>
            </div>

            <!-- Category Badge -->
            <div class="category-badge {{ strtolower($subject['performance_category']) }}">
                {{ $subject['performance_category'] }}
            </div>

            <!-- Confidence Interval -->
            <div class="confidence-interval">
                <label>95% Confidence Interval</label>
                <div class="range">
                    [{{ $subject['confidence_interval']['lower_bound'] }},
                     {{ $subject['confidence_interval']['upper_bound'] }}]
                </div>
            </div>

            <!-- Recommendation -->
            <div class="recommendation">
                <label>💡 Recommendation</label>
                <p>{{ $subject['recommendation'] }}</p>
            </div>
        </div>
    </div>
    @endforeach
</div>
```

### 6. Performance Categories & Trends

**The model identifies these patterns:**

| ID  | Attendance | Term1 | Term2 | Term3 | Prediction | Trend     | Category                  |
| --- | ---------- | ----- | ----- | ----- | ---------- | --------- | ------------------------- |
| 1   | 10%        | 85    | 88    | 90    | ~82        | Risk      | High Marks/Low Attendance |
| 2   | 18%        | 30    | 28    | 25    | ~25        | Declining | Low Marks/Low Attendance  |
| 3   | 50%        | 48    | 52    | 50    | ~51        | Stable    | Average                   |
| 4   | 65%        | 60    | 68    | 75    | ~72        | Improving | Good Performance          |
| 5   | 95%        | 88    | 90    | 92    | ~92        | Excellent | High Performance          |
| 6   | 90%        | 40    | 38    | 35    | ~42        | Concern   | High Attendance/Low Marks |

### 7. Integration Steps

#### Step 1: Start the API

```bash
cd student-performance-prediction-model
source venv/bin/activate
python api/app.py
```

#### Step 2: Laravel Controller

```php
public function predictPerformance($studentId) {
    $student = Student::with('subjects')->find($studentId);

    $subjects = [];
    foreach ($student->subjects as $subject) {
        $subjects[] = [
            'subject_name' => $subject->name,
            'attendance' => $subject->attendance_percentage,
            'term1_marks' => $subject->term1_marks,
            'term2_marks' => $subject->term2_marks,
            'term3_marks' => $subject->term3_marks,
        ];
    }

    $response = Http::post('http://localhost:5002/predict', [
        'student_id' => $student->id,
        'age' => $student->age,
        'grade' => $student->grade,
        'subjects' => $subjects
    ]);

    return view('performance.show', [
        'student' => $student,
        'prediction' => $response->json()
    ]);
}
```

#### Step 3: Display in Blade

Use the template above to display all fields including:

- ✅ Attendance
- ✅ Term 1, 2, 3 Marks
- ✅ Predicted Performance
- ✅ Trend Analysis
- ✅ Confidence Intervals
- ✅ Recommendations

### 8. Testing

**Test the API:**

```bash
cd student-performance-prediction-model
source venv/bin/activate
python api_test_example.py
```

**Test predictions:**

```bash
python test_predictions.py
```

### 9. Retraining

**To retrain with new data:**

```bash
# Update dataset: dataset/student_performance_6000_with_prediction.csv
# Then run:
python train_with_6000.py
```

---

## ✅ Summary

✓ **Dataset:** 6000 records with Attendance + 3 Term Marks  
✓ **Training:** Expanded to 30,000 records, trained with XGBoost  
✓ **Accuracy:** MAE = 0.30, R² = 0.9997 (excellent)  
✓ **API Output:** Includes all required fields for UI  
✓ **UI Display:** Shows Attendance + Term1 + Term2 + Term3 + Prediction  
✓ **All Scenarios:** Working for all performance categories

**Ready for production! 🚀**
