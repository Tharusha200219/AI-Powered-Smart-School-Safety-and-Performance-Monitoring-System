-- ============================================================
-- Laravel Database Schema for Student Performance Prediction
-- ============================================================
-- This schema is designed to work with the ML prediction model
-- Use this to create tables in your Laravel MySQL database
-- ============================================================

-- 1. STUDENTS TABLE (Main table with all required columns)
CREATE TABLE students (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    gender ENUM('Male', 'Female') NOT NULL,
    date_of_birth DATE,
    
    -- Academic Data (Required for ML Model)
    study_hours_per_week DECIMAL(5, 2) DEFAULT 0 COMMENT 'Weekly study hours reported by student',
    attendance_rate DECIMAL(5, 2) DEFAULT 0 COMMENT 'Calculated from RFID attendance records',
    past_exam_scores DECIMAL(5, 2) DEFAULT 0 COMMENT 'Average of all past exam scores',
    
    -- Background Information (Required for ML Model)
    parental_education_level ENUM('High School', 'Bachelors', 'Masters', 'PhD') COMMENT 'Parent highest education',
    internet_access_at_home ENUM('Yes', 'No') DEFAULT 'Yes',
    extracurricular_activities ENUM('Yes', 'No') DEFAULT 'No',
    
    -- ML Prediction Results (Updated by Python API)
    predicted_performance ENUM('Pass', 'Fail') NULL COMMENT 'Latest prediction from ML model',
    prediction_confidence DECIMAL(5, 4) NULL COMMENT 'Confidence score (0-1)',
    prediction_pass_probability DECIMAL(5, 4) NULL COMMENT 'Probability of passing',
    prediction_fail_probability DECIMAL(5, 4) NULL COMMENT 'Probability of failing',
    last_prediction_date TIMESTAMP NULL COMMENT 'When prediction was last updated',
    
    -- Additional Information
    class VARCHAR(50),
    section VARCHAR(10),
    phone_number VARCHAR(20),
    address TEXT,
    guardian_name VARCHAR(255),
    guardian_contact VARCHAR(20),
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_student_id (student_id),
    INDEX idx_predicted_performance (predicted_performance),
    INDEX idx_attendance_rate (attendance_rate)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 2. ATTENDANCE TABLE (RFID Tracking)
CREATE TABLE attendance (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) NOT NULL,
    rfid_tag VARCHAR(50) NOT NULL COMMENT 'RFID card unique identifier',
    check_in_time TIMESTAMP NOT NULL,
    check_out_time TIMESTAMP NULL,
    subject_code VARCHAR(20),
    class_date DATE NOT NULL,
    status ENUM('Present', 'Late', 'Absent') DEFAULT 'Present',
    remarks TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    INDEX idx_student_attendance (student_id, class_date),
    INDEX idx_rfid_tag (rfid_tag),
    INDEX idx_class_date (class_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 3. EXAM SCORES TABLE
CREATE TABLE exam_scores (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) NOT NULL,
    exam_name VARCHAR(255) NOT NULL,
    subject_code VARCHAR(20),
    subject_name VARCHAR(255),
    score DECIMAL(5, 2) NOT NULL COMMENT 'Score obtained by student',
    max_score DECIMAL(5, 2) DEFAULT 100 COMMENT 'Maximum possible score',
    percentage DECIMAL(5, 2) GENERATED ALWAYS AS ((score / max_score) * 100) STORED,
    exam_date DATE NOT NULL,
    exam_type ENUM('Quiz', 'Midterm', 'Final', 'Assignment', 'Project') DEFAULT 'Final',
    grade VARCHAR(5) COMMENT 'A, B, C, D, F',
    remarks TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    INDEX idx_student_exams (student_id, exam_date),
    INDEX idx_exam_date (exam_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 4. STUDY HOURS LOG (Optional - to track study hours)
CREATE TABLE study_hours_log (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) NOT NULL,
    date DATE NOT NULL,
    hours DECIMAL(4, 2) NOT NULL,
    subject_code VARCHAR(20),
    study_type ENUM('Individual', 'Group', 'With Tutor') DEFAULT 'Individual',
    notes TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    INDEX idx_student_study (student_id, date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 5. PREDICTION HISTORY (Track all predictions over time)
CREATE TABLE prediction_history (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) NOT NULL,
    prediction ENUM('Pass', 'Fail') NOT NULL,
    confidence DECIMAL(5, 4) NOT NULL,
    pass_probability DECIMAL(5, 4),
    fail_probability DECIMAL(5, 4),
    
    -- Input features used for prediction
    study_hours_per_week DECIMAL(5, 2),
    attendance_rate DECIMAL(5, 2),
    past_exam_scores DECIMAL(5, 2),
    
    prediction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    INDEX idx_student_history (student_id, prediction_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 6. SUBJECTS TABLE (Optional)
CREATE TABLE subjects (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subject_code VARCHAR(20) UNIQUE NOT NULL,
    subject_name VARCHAR(255) NOT NULL,
    credits INT DEFAULT 3,
    description TEXT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- VIEWS FOR EASY DATA ACCESS
-- ============================================================

-- View: Student Performance Dashboard
CREATE VIEW student_performance_dashboard AS
SELECT 
    s.student_id,
    s.name,
    s.gender,
    s.class,
    s.attendance_rate,
    s.past_exam_scores,
    s.study_hours_per_week,
    s.predicted_performance,
    s.prediction_confidence,
    s.last_prediction_date,
    CASE 
        WHEN s.predicted_performance = 'Fail' AND s.prediction_confidence > 0.7 THEN 'High Risk'
        WHEN s.predicted_performance = 'Fail' AND s.prediction_confidence > 0.5 THEN 'Medium Risk'
        WHEN s.predicted_performance = 'Fail' THEN 'Low Risk'
        ELSE 'No Risk'
    END AS risk_level
FROM students s;


-- View: Attendance Summary
CREATE VIEW attendance_summary AS
SELECT 
    student_id,
    COUNT(*) as total_classes,
    SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present_count,
    SUM(CASE WHEN status = 'Late' THEN 1 ELSE 0 END) as late_count,
    SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) as absent_count,
    ROUND((SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as attendance_percentage
FROM attendance
GROUP BY student_id;


-- ============================================================
-- STORED PROCEDURES
-- ============================================================

DELIMITER //

-- Procedure: Update student attendance rate
CREATE PROCEDURE update_attendance_rate(IN p_student_id VARCHAR(50))
BEGIN
    UPDATE students s
    SET s.attendance_rate = (
        SELECT ROUND((SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2)
        FROM attendance
        WHERE student_id = p_student_id
    )
    WHERE s.student_id = p_student_id;
END //

-- Procedure: Update past exam scores average
CREATE PROCEDURE update_past_exam_scores(IN p_student_id VARCHAR(50))
BEGIN
    UPDATE students s
    SET s.past_exam_scores = (
        SELECT ROUND(AVG(percentage), 2)
        FROM exam_scores
        WHERE student_id = p_student_id
    )
    WHERE s.student_id = p_student_id;
END //

-- Procedure: Update weekly study hours
CREATE PROCEDURE update_study_hours(IN p_student_id VARCHAR(50))
BEGIN
    UPDATE students s
    SET s.study_hours_per_week = (
        SELECT ROUND(AVG(weekly_hours), 2)
        FROM (
            SELECT SUM(hours) as weekly_hours
            FROM study_hours_log
            WHERE student_id = p_student_id
            AND date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY WEEK(date)
        ) as weekly_avg
    )
    WHERE s.student_id = p_student_id;
END //

DELIMITER ;


-- ============================================================
-- TRIGGERS
-- ============================================================

DELIMITER //

-- Trigger: Auto-update attendance rate after new attendance record
CREATE TRIGGER after_attendance_insert
AFTER INSERT ON attendance
FOR EACH ROW
BEGIN
    CALL update_attendance_rate(NEW.student_id);
END //

-- Trigger: Auto-update past exam scores after new exam score
CREATE TRIGGER after_exam_score_insert
AFTER INSERT ON exam_scores
FOR EACH ROW
BEGIN
    CALL update_past_exam_scores(NEW.student_id);
END //

DELIMITER ;


-- ============================================================
-- SAMPLE DATA (Optional - for testing)
-- ============================================================

-- Insert sample students
INSERT INTO students (student_id, name, gender, parental_education_level, internet_access_at_home, extracurricular_activities, study_hours_per_week) VALUES
('S001', 'John Doe', 'Male', 'Bachelors', 'Yes', 'Yes', 20),
('S002', 'Jane Smith', 'Female', 'Masters', 'Yes', 'Yes', 25),
('S003', 'Mike Johnson', 'Male', 'High School', 'No', 'No', 10);

-- Insert sample subjects
INSERT INTO subjects (subject_code, subject_name) VALUES
('MATH101', 'Mathematics'),
('ENG101', 'English'),
('SCI101', 'Science');


-- ============================================================
-- NOTES FOR LARAVEL MIGRATION
-- ============================================================
/*
To use this schema in Laravel:

1. Create Laravel migrations for each table
2. Use Laravel's Schema Builder instead of raw SQL
3. Add relationships in Eloquent models:
   - Student hasMany Attendance
   - Student hasMany ExamScores
   - Student hasMany StudyHoursLog
   - Student hasMany PredictionHistory

4. Create a Laravel Seeder for sample data
5. Use Laravel Events/Observers for triggers
6. Create Laravel API routes for Python integration
*/
