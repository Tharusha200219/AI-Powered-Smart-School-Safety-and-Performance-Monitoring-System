# 📊 LARAVEL DATABASE COLUMNS - COMPLETE REFERENCE

## ✅ YOUR LARAVEL APPLICATION NEEDS THESE EXACT COLUMNS

---

## 🎯 PRIMARY TABLE: `students`

### **Columns You MUST Have:**

```sql
CREATE TABLE students (
    -- Primary Keys
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) UNIQUE NOT NULL,
    
    -- Basic Information
    name VARCHAR(255) NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL,
    
    -- ⭐ ML MODEL INPUT FEATURES (CRITICAL) ⭐
    study_hours_per_week DECIMAL(5,2) DEFAULT 0,
    attendance_rate DECIMAL(5,2) DEFAULT 0,
    past_exam_scores DECIMAL(5,2) DEFAULT 0,
    parental_education_level ENUM('High School', 'Bachelors', 'Masters', 'PhD'),
    internet_access_at_home ENUM('Yes', 'No') DEFAULT 'Yes',
    extracurricular_activities ENUM('Yes', 'No') DEFAULT 'No',
    
    -- ⭐ ML MODEL OUTPUT (AUTO-UPDATED BY API) ⭐
    predicted_performance ENUM('Pass', 'Fail') NULL,
    prediction_confidence DECIMAL(5,4) NULL,
    last_prediction_date TIMESTAMP NULL,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

## 📋 DETAILED COLUMN SPECIFICATIONS

### 1️⃣ **student_id** (VARCHAR 50) - REQUIRED
- **Purpose**: Unique identifier for each student
- **Example**: "S001", "STU-2024-001", "2024CS001"
- **Laravel**: Used as foreign key in all related tables
- **Note**: Must be unique across all students

### 2️⃣ **name** (VARCHAR 255) - REQUIRED
- **Purpose**: Student's full name
- **Example**: "John Doe", "Sarah Smith"
- **Laravel**: Display in dashboards and reports

### 3️⃣ **gender** (ENUM: 'Male', 'Female') - REQUIRED
- **Purpose**: ML model feature for prediction
- **Allowed Values**: ONLY "Male" or "Female" (exact spelling)
- **Laravel**: Dropdown selection during registration
- **⚠️ CRITICAL**: Must match exactly - case sensitive!

### 4️⃣ **study_hours_per_week** (DECIMAL 5,2) - REQUIRED
- **Purpose**: Hours student studies per week
- **Range**: 0.00 to 999.99
- **Example**: 20.50, 15.00, 30.75
- **How to Populate**:
  - Student self-reports via form
  - Calculate from study_hours_log table
  - Manual entry by teachers
- **Update Frequency**: Weekly or monthly

### 5️⃣ **attendance_rate** (DECIMAL 5,2) - REQUIRED ⭐ MOST IMPORTANT
- **Purpose**: Percentage of classes attended
- **Range**: 0.00 to 100.00
- **Example**: 85.50, 92.00, 67.25
- **How to Populate**: **AUTOMATIC FROM RFID**
  ```php
  $totalClasses = Attendance::where('student_id', $studentId)->count();
  $present = Attendance::where('student_id', $studentId)
              ->where('status', 'Present')->count();
  $attendanceRate = ($present / $totalClasses) * 100;
  ```
- **Update Frequency**: Real-time after each class

### 6️⃣ **past_exam_scores** (DECIMAL 5,2) - REQUIRED ⭐ VERY IMPORTANT
- **Purpose**: Average of all past exam scores
- **Range**: 0.00 to 100.00
- **Example**: 75.50, 88.00, 65.75
- **How to Populate**: **AUTO-CALCULATE FROM EXAMS**
  ```php
  $average = ExamScore::where('student_id', $studentId)
              ->avg('percentage');
  $student->past_exam_scores = $average;
  ```
- **Update Frequency**: After each exam entry

### 7️⃣ **parental_education_level** (ENUM) - REQUIRED
- **Purpose**: Highest education level of parents
- **Allowed Values**: ONLY these 4 options:
  - "High School"
  - "Bachelors"
  - "Masters"
  - "PhD"
- **⚠️ CRITICAL**: Must match exactly - case sensitive!
- **How to Populate**: 
  - Dropdown during student registration
  - One-time entry
- **Laravel Example**:
  ```php
  <select name="parental_education_level">
      <option value="High School">High School</option>
      <option value="Bachelors">Bachelor's Degree</option>
      <option value="Masters">Master's Degree</option>
      <option value="PhD">PhD/Doctorate</option>
  </select>
  ```

### 8️⃣ **internet_access_at_home** (ENUM: 'Yes', 'No') - REQUIRED
- **Purpose**: Whether student has internet at home
- **Allowed Values**: ONLY "Yes" or "No"
- **⚠️ CRITICAL**: Must match exactly - case sensitive!
- **How to Populate**: 
  - Checkbox during registration
  - One-time entry
- **Laravel Example**:
  ```php
  <select name="internet_access_at_home">
      <option value="Yes">Yes</option>
      <option value="No">No</option>
  </select>
  ```

### 9️⃣ **extracurricular_activities** (ENUM: 'Yes', 'No') - REQUIRED
- **Purpose**: Whether student participates in activities
- **Allowed Values**: ONLY "Yes" or "No"
- **⚠️ CRITICAL**: Must match exactly - case sensitive!
- **How to Populate**: 
  - Based on enrollment in clubs/sports
  - Checkbox during registration
  - Auto-update when student joins activity
- **Laravel Example**:
  ```php
  // Check if student has any activity enrollments
  $hasActivities = $student->activities()->exists() ? 'Yes' : 'No';
  $student->extracurricular_activities = $hasActivities;
  ```

### 🔟 **predicted_performance** (ENUM: 'Pass', 'Fail') - AUTO-UPDATED
- **Purpose**: ML model's prediction result
- **Values**: "Pass" or "Fail"
- **How to Populate**: **AUTOMATIC FROM API**
- **Laravel**: Updated by Python API response
- **Never manually edit this field**

### 1️⃣1️⃣ **prediction_confidence** (DECIMAL 5,4) - AUTO-UPDATED
- **Purpose**: How confident the model is (0-1)
- **Range**: 0.0000 to 1.0000
- **Example**: 0.8850 = 88.50% confident
- **How to Populate**: **AUTOMATIC FROM API**
- **Use for**: Filtering high-confidence predictions

### 1️⃣2️⃣ **last_prediction_date** (TIMESTAMP) - AUTO-UPDATED
- **Purpose**: When prediction was last made
- **How to Populate**: **AUTOMATIC FROM API**
- **Use for**: Knowing if prediction is stale

---

## 🗄️ SUPPORTING TABLES

### **TABLE: `attendance`** (for RFID tracking)
```sql
CREATE TABLE attendance (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) NOT NULL,
    rfid_tag VARCHAR(50) NOT NULL,        -- RFID card number
    check_in_time TIMESTAMP NOT NULL,     -- When student scanned
    class_date DATE NOT NULL,
    status ENUM('Present', 'Late', 'Absent') DEFAULT 'Present',
    FOREIGN KEY (student_id) REFERENCES students(student_id)
);
```

### **TABLE: `exam_scores`** (for manual exam entry)
```sql
CREATE TABLE exam_scores (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) NOT NULL,
    exam_name VARCHAR(255) NOT NULL,
    score DECIMAL(5,2) NOT NULL,          -- Actual score
    max_score DECIMAL(5,2) DEFAULT 100,   -- Maximum possible
    percentage DECIMAL(5,2),              -- Auto-calculated
    exam_date DATE NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(student_id)
);
```

---

## ⚠️ CRITICAL VALIDATION RULES

### **Gender Field**
```php
// In Laravel Model or Request
'gender' => 'required|in:Male,Female'  // Case sensitive!
```

### **Parental Education Level**
```php
'parental_education_level' => 'required|in:High School,Bachelors,Masters,PhD'
```

### **Internet Access & Activities**
```php
'internet_access_at_home' => 'required|in:Yes,No'
'extracurricular_activities' => 'required|in:Yes,No'
```

### **Numeric Ranges**
```php
'study_hours_per_week' => 'required|numeric|min:0|max:168',  // Max = hours in week
'attendance_rate' => 'required|numeric|min:0|max:100',
'past_exam_scores' => 'required|numeric|min:0|max:100',
```

---

## 📊 HOW TO POPULATE EACH FIELD

| Column | Source | Method | Update Frequency |
|--------|--------|--------|------------------|
| `student_id` | Manual/Auto | Registration | Once |
| `name` | Manual | Registration | Once |
| `gender` | Manual | Registration form | Once |
| `study_hours_per_week` | Student/System | Self-report or calculated | Weekly |
| `attendance_rate` | **RFID System** | **Auto from attendance table** | **Real-time** |
| `past_exam_scores` | **Exam Scores** | **Auto from exam_scores table** | **After each exam** |
| `parental_education_level` | Manual | Registration form | Once |
| `internet_access_at_home` | Manual | Registration form | Once |
| `extracurricular_activities` | System/Manual | Auto from enrollments | Dynamic |
| `predicted_performance` | **Python API** | **Auto from ML model** | **Daily** |
| `prediction_confidence` | **Python API** | **Auto from ML model** | **Daily** |
| `last_prediction_date` | **Python API** | **Auto from ML model** | **Daily** |

---

## 🔄 AUTOMATED CALCULATIONS

### **1. Update Attendance Rate (Automatic)**
```php
// After RFID scan, update attendance_rate
public function updateAttendanceRate()
{
    $total = $this->attendance()->count();
    $present = $this->attendance()->where('status', 'Present')->count();
    
    $this->attendance_rate = $total > 0 ? ($present / $total) * 100 : 0;
    $this->save();
}
```

### **2. Update Past Exam Scores (Automatic)**
```php
// After teacher enters exam score
public function updatePastExamScores()
{
    $average = $this->examScores()->avg('percentage');
    
    $this->past_exam_scores = $average ?? 0;
    $this->save();
}
```

### **3. Update Extracurricular Activities (Automatic)**
```php
// When student joins/leaves an activity
public function updateExtracurricularStatus()
{
    $hasActivities = $this->activities()->exists();
    
    $this->extracurricular_activities = $hasActivities ? 'Yes' : 'No';
    $this->save();
}
```

---

## 🎯 LARAVEL MODEL EXAMPLE

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'student_id',
        'name',
        'gender',
        'study_hours_per_week',
        'attendance_rate',
        'past_exam_scores',
        'parental_education_level',
        'internet_access_at_home',
        'extracurricular_activities',
        'predicted_performance',
        'prediction_confidence',
        'last_prediction_date',
    ];
    
    protected $casts = [
        'study_hours_per_week' => 'decimal:2',
        'attendance_rate' => 'decimal:2',
        'past_exam_scores' => 'decimal:2',
        'prediction_confidence' => 'decimal:4',
        'last_prediction_date' => 'datetime',
    ];
    
    // Relationships
    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'student_id', 'student_id');
    }
    
    public function examScores()
    {
        return $this->hasMany(ExamScore::class, 'student_id', 'student_id');
    }
    
    // Auto-calculate methods
    public function updateAttendanceRate()
    {
        $total = $this->attendance()->count();
        $present = $this->attendance()->where('status', 'Present')->count();
        $this->attendance_rate = $total > 0 ? ($present / $total) * 100 : 0;
        $this->save();
    }
    
    public function updatePastExamScores()
    {
        $this->past_exam_scores = $this->examScores()->avg('percentage') ?? 0;
        $this->save();
    }
}
```

---

## ✅ CHECKLIST BEFORE RUNNING PREDICTIONS

- [ ] All 7 input columns exist in database
- [ ] Gender values are exactly "Male" or "Female"
- [ ] Parental education values match: "High School", "Bachelors", "Masters", "PhD"
- [ ] Yes/No fields use exactly "Yes" or "No"
- [ ] Attendance rate is 0-100 (percentage)
- [ ] Past exam scores is 0-100 (average)
- [ ] Study hours is >= 0
- [ ] RFID attendance system is recording data
- [ ] Exam scores are being entered after exams
- [ ] Python API is running

---

## 🚨 COMMON ERRORS & FIXES

| Error | Cause | Fix |
|-------|-------|-----|
| "Gender not found" | Gender is "M" or "F" instead of "Male"/"Female" | Update to full words |
| "Invalid parental education" | Using "Bachelor" instead of "Bachelors" | Match exact spelling |
| "Attendance rate > 100" | Calculation error | Ensure formula divides by total |
| "Past exam scores NULL" | No exams entered yet | Set default to 0 |
| API returns 400 | Missing required field | Check all 7 fields are sent |
| Low accuracy | Incorrect data types | Verify all numeric fields are numbers |

---

## 📱 SUMMARY FOR YOUR TEAM

**Tell your Laravel developers:**

1. **Add these 12 columns to the students table**
2. **Use EXACT spelling for ENUM values** (case-sensitive!)
3. **Auto-calculate attendance_rate from RFID data**
4. **Auto-calculate past_exam_scores from exam entries**
5. **Let Python API update predicted_performance automatically**
6. **Run batch predictions daily via cron job**

**That's it! 🎉**
