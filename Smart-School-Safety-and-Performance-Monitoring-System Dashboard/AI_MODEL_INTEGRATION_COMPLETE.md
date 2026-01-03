# AI Model Integration - Implementation Complete

## Overview

Successfully integrated two AI models into the Laravel dashboard:

1. **Student Performance Prediction Model** - Predicts student performance for each subject
2. **Student Seating Arrangement Model** - Generates optimal seating arrangements for classes

## What Was Implemented

### 1. Database Structure

#### New Tables Created:

-   **student_performance_predictions** - Stores AI-generated performance predictions

    -   Links to students and subjects
    -   Tracks current vs predicted performance
    -   Includes trend analysis (improving/stable/declining)
    -   Stores confidence scores and recommendations

-   **seating_arrangements** - Stores generated seating arrangements

    -   Links to grades, sections, and classes
    -   Stores complete arrangement data as JSON
    -   Tracks who generated it and when
    -   Has active/inactive status

-   **student_seat_assignments** - Individual seat assignments
    -   Links students to specific seats in arrangements
    -   Stores row and seat numbers
    -   Formatted seat positions

#### Enhanced Existing Tables:

-   **student_subject** - Links students to their grade-appropriate subjects
-   **marks** - Enhanced with comprehensive seeding
-   **attendance** - Enhanced with random realistic data

### 2. Models and Relationships

#### New Eloquent Models:

-   `StudentPerformancePrediction` - With trend analysis and color-coded badges
-   `SeatingArrangement` - With arrangement data management
-   `StudentSeatAssignment` - With formatted seat display

#### Updated Models:

-   `Student` - Added relationships:
    -   `performancePredictions()`
    -   `seatAssignment()`
    -   `attendance()`

### 3. Services (API Integration)

#### PerformancePredictionService

Location: `app/Services/PerformancePredictionService.php`

-   Communicates with Python prediction API (default: http://localhost:5000)
-   Prepares student data (marks, attendance, age, grade)
-   Stores predictions in database
-   Retrieves predictions for display
-   Health check for API availability

#### SeatingArrangementService

Location: `app/Services/SeatingArrangementService.php`

-   Communicates with Python seating API (default: http://localhost:5001)
-   Generates arrangements based on student performance
-   Stores arrangements and seat assignments
-   Retrieves student seat information
-   Health check for API availability

### 4. Controllers

#### Admin Controllers:

-   **SeatingArrangementController** - Full CRUD for seating arrangements

    -   Index: List all arrangements
    -   Create: Form to generate new arrangements
    -   Generate: Call API and store results
    -   Show: Display specific arrangement
    -   Toggle: Activate/deactivate arrangements

-   **StudentController** (Enhanced)
    -   Added `generatePredictions()` method
    -   Enhanced `show()` to display predictions and seat assignments

#### Student Portal Controller:

-   **StudentDashboardController**
    -   Dashboard with overview
    -   Performance page with predictions
    -   Seat assignment page

### 5. Routes

#### Admin Routes:

```php
// In student management
POST /admin/management/students/{id}/generate-predictions

// Seating arrangement management
GET  /admin/seating-arrangement
GET  /admin/seating-arrangement/create
POST /admin/seating-arrangement/generate
GET  /admin/seating-arrangement/{id}
DELETE /admin/seating-arrangement/{id}
POST /admin/seating-arrangement/{id}/toggle-active
```

#### Student Portal Routes:

```php
GET /student/dashboard
GET /student/performance
GET /student/seat-assignment
```

### 6. Sidebar Navigation

#### Admin Sidebar (config/sidebar.php):

-   Added "Seat Arrangement" under Management section

#### Student Sidebar (config/student-sidebar.php):

-   Dashboard
-   Performance (with predictions)
-   Seat Assignment

### 7. Data Seeding

#### New Seeders:

-   **StudentSubjectSeeder** - Assigns grade-appropriate subjects to all students
-   **MarkSeederEnhanced** - Generates realistic marks for 3 terms with normal distribution
-   **AttendanceSeeder** - Generates 65 days of attendance with 85% present rate

All seeders are integrated into `DatabaseSeeder` and run automatically with:

```bash
php artisan migrate:fresh --seed
```

### 8. Configuration

#### Service Configuration (config/services.php):

```php
'performance_prediction' => [
    'url' => env('PERFORMANCE_PREDICTION_API_URL', 'http://localhost:5000'),
],
'seating_arrangement' => [
    'url' => env('SEATING_ARRANGEMENT_API_URL', 'http://localhost:5001'),
],
```

## Setup Instructions

### 1. Environment Configuration

Add to your `.env` file:

```env
PERFORMANCE_PREDICTION_API_URL=http://localhost:5002
SEATING_ARRANGEMENT_API_URL=http://localhost:5001
```

**Note**: Port 5002 is used instead of 5000 because macOS uses port 5000 for AirPlay Receiver.

### 2. Start Python APIs

**Quick Start - Use the automated script:**

```bash
cd /Users/tharusha_rashmika/Documents/projects/aleph/reserch
./start_both_apis.sh
```

This will start both APIs in the background. Check status with:

```bash
curl http://localhost:5002/health  # Performance Prediction
curl http://localhost:5001/health  # Seating Arrangement
```

**Manual Start (if needed):**

#### Performance Prediction API:

```bash
cd /path/to/student-performance-prediction-model
source venv/bin/activate
python api/app.py
# Runs on port 5002
```

#### Seating Arrangement API:

```bash
cd /path/to/student-seating-arrangement-model
source venv/bin/activate
python api/app.py
# Runs on port 5001
```

### 3. Database Migration

Already completed! But to re-run:

```bash
php artisan migrate:fresh --seed
```

## Usage Guide

### For Administrators:

#### Viewing Student Performance Predictions:

1. Navigate to Students → View Student
2. Predictions are displayed if available
3. Click "Generate Predictions" button to create/update predictions
4. Requires:
    - Student has enrolled subjects
    - Student has marks for current term
    - Student has attendance records
    - Python API is running

#### Generating Seating Arrangements:

1. Navigate to Seat Arrangement (in Management sidebar)
2. Click "Create New Arrangement"
3. Select:
    - Grade Level
    - Section (optional)
    - Class
    - Academic Year
    - Term
    - Seats per row (default: 5)
    - Total rows (default: 6)
4. Click "Generate"
5. View the generated arrangement with student placements

#### Viewing Student Information:

When viewing a student, you'll see:

-   **Top Section**: Seat assignment (if generated)
-   **Performance Section**: Predictions for each subject with:
    -   Current marks
    -   Predicted performance
    -   Trend indicator (↑ improving, → stable, ↓ declining)
    -   Confidence score
    -   Recommendations

### For Students:

#### Dashboard:

-   Overview of academic status
-   Current seat assignment
-   Performance predictions
-   Attendance summary

#### Performance Page:

-   Detailed predictions by subject
-   Trend analysis
-   Term-by-term comparison

#### Seat Assignment Page:

-   Current seat location
-   Row and seat number
-   Class information

## Data Flow

### Performance Prediction:

1. Admin clicks "Generate Predictions" for a student
2. Laravel collects: student age, grade, subjects with marks and attendance
3. Sends data to Python API (http://localhost:5000/predict)
4. API returns predictions with trends and confidence
5. Laravel stores predictions in database
6. Predictions displayed to admin and student

### Seating Arrangement:

1. Admin fills out seating form
2. Laravel collects students for selected grade/section
3. Calculates average marks for each student
4. Sends data to Python API (http://localhost:5001/generate-seating)
5. API returns optimal seating arrangement
6. Laravel stores arrangement and individ2/health`

-   Predict: `POST http://localhost:5002ats

## Database Statistics

After seeding:

-   **52 Students** across grades 1-13
-   **Each student has**:
    -   5-8 subjects (grade-appropriate)
    -   3 terms of marks
    -   65 days of attendance records
-   **All data ready** for AI model integration

## API Endpoints

### Performance Prediction API:

-   Health Check: `GET http://localhost:5000/health`
-   Predict: `POST http://localhost:5000/predict`

### Seating Arrangement API:

-   Health Check: `GET http://localhost:5001/health`
-   Generate: `POST http://localhost:5001/generate-seating`

## Testing

### Test Student Login:

Use any student from the database. Format:

-   Email: grade{X}-student{Y}@school.com (e.g., grade1-student1@school.com)
-   Password: student123

### Test Admin Functions:

1. Login as admin
2. Go to Students → View any student
3. Click "Generate Predictions" (requires Python API running)
4. Go to Seat Arrangement → Create New
5. Generate arrangement for a grade

## Troubleshooting

### "Service Unavailable" Error:

-   Check if Python APIs are running
-   Verify ports 5000 and 5001 are not in use by other services
-   Check `.env` configuration

### No Predictions Generated:

-   Ensure student has marks for current term
-   Ensure student has attendance records
-   Check Python API logs

### No Students in Seating Form:

-   Verify students exist for selected grade/section
-   Check if students are marked as active

## File Locations

### Backend:

-   Models: `app/Models/`
-   Controllers: `app/Http/Controllers/Admin/` and `app/Http/Controllers/Student/`
-   Services: `app/Services/`
-   Migrations: `database/migrations/`
-   Seeders: `database/seeders/`
-   Routes: `routes/web.php`
-   Config: `config/sidebar.php`, `config/student-sidebar.php`, `config/services.php`

### Python APIs:

-   Performance Prediction: `../student-performance-prediction-model/api/app.py`
-   Seating Arrangement: `../student-seating-arrangement-model/api/app.py`

## Next Steps

1. **Create Views** - Need to create Blade templates for:

    - Admin seating arrangement pages
    - Student dashboard and performance pages
    - Enhanced student view page with predictions

2. **Add Frontend Interactivity** - Add JavaScript for:

    - Real-time prediction generation
    - Interactive seating chart visualization
    - Charts for performance trends

3. **Testing** - Thoroughly test:

    - All CRUD operations
    - API integrations
    - Student portal access
    - Permission checks

4. **Production Deployment** - Configure:
    - Python API hosting
    - Environment variables
    - Database optimization

## Success Indicators

✅ Database migrations completed
✅ All seeders run successfully
✅ 52 students with complete data
✅ Subjects assigned based on grade
✅ Random marks and attendance generated
✅ Models and relationships created
✅ Services for API integration created
✅ Controllers and routes implemented
✅ Sidebar navigation updated

## What's Ready to Use

-   All backend logic
-   Database structure
-   API integration services
-   Admin controllers
-   Student portal controllers
-   Routing
-   Data seeding

## What Needs Views

You'll need to create Blade templates for visualization. The backend is complete and ready to serve data to your frontend.

---

**Implementation Date**: January 2, 2026
**Total Database Tables**: 30+
**Total Students**: 52
**Subjects per Student**: 5-8 (grade-based)
**Attendance Records**: 3,380+ (65 days × 52 students)
**Mark Records**: 780+ (3 terms × 5 avg subjects × 52 students)
