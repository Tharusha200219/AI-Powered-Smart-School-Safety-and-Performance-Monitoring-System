"""
Data Preprocessing Module
Cleans and prepares student performance data for model training

This module:
1. Loads the raw dataset
2. Removes unnecessary columns
3. Handles missing values
4. Creates subject-wise records
5. Saves cleaned data for model training
"""

import pandas as pd
import numpy as np
import os
import sys

# Add parent directory to path
sys.path.append(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
from config.config import DATASET_PATH, CLEANED_DATA_PATH, DATA_DIR


class DataPreprocessor:
    """Clean and prepare student performance data"""
    
    def __init__(self, raw_data_path=DATASET_PATH):
        """
        Initialize preprocessor
        
        Args:
            raw_data_path: Path to raw CSV dataset
        """
        self.raw_data_path = raw_data_path
        self.df = None
        
    def load_data(self):
        """Load raw dataset from CSV"""
        print(f"Loading data from: {self.raw_data_path}")
        self.df = pd.read_csv(self.raw_data_path)
        print(f"Loaded {len(self.df)} records with {len(self.df.columns)} columns")
        return self
        
    def clean_data(self):
        """
        Clean the dataset:
        - Keep only relevant columns
        - Handle missing values
        - Standardize data types
        """
        print("\n=== Cleaning Data ===")
        
        # Select relevant columns for our model
        # We need: StudentID, Age (derived from Gender/other proxies), Attendance, Marks
        relevant_cols = ['StudentID', 'Gender', 'AttendanceRate', 'Attendance (%)', 
                        'PreviousGrade', 'FinalGrade', 'StudyHoursPerWeek']
        
        # Keep only columns that exist in the dataset
        existing_cols = [col for col in relevant_cols if col in self.df.columns]
        self.df = self.df[existing_cols].copy()
        
        # Combine attendance columns (use the one with more data)
        if 'AttendanceRate' in self.df.columns and 'Attendance (%)' in self.df.columns:
            self.df['Attendance'] = self.df['AttendanceRate'].fillna(self.df['Attendance (%)'])
        elif 'AttendanceRate' in self.df.columns:
            self.df['Attendance'] = self.df['AttendanceRate']
        elif 'Attendance (%)' in self.df.columns:
            self.df['Attendance'] = self.df['Attendance (%)']
        else:
            self.df['Attendance'] = 0
            
        # Drop original attendance columns
        self.df.drop(['AttendanceRate', 'Attendance (%)'], axis=1, errors='ignore', inplace=True)
        
        # Handle missing StudentID - generate if missing
        if self.df['StudentID'].isnull().any():
            missing_ids = self.df['StudentID'].isnull()
            self.df.loc[missing_ids, 'StudentID'] = list(range(10000, 10000 + missing_ids.sum()))
        
        # Fill missing attendance with 0
        self.df['Attendance'] = self.df['Attendance'].fillna(0)
        
        # Fill missing grades with 0
        if 'PreviousGrade' in self.df.columns:
            self.df['PreviousGrade'] = self.df['PreviousGrade'].fillna(0)
        if 'FinalGrade' in self.df.columns:
            self.df['FinalGrade'] = self.df['FinalGrade'].fillna(0)
            
        # Fill missing study hours with median
        if 'StudyHoursPerWeek' in self.df.columns:
            self.df['StudyHoursPerWeek'] = self.df['StudyHoursPerWeek'].fillna(
                self.df['StudyHoursPerWeek'].median()
            )
        
        # Create age from a baseline (assume grade 10 = 15 years old as baseline)
        # This is a simplified approach
        self.df['Age'] = 15  # Default age for demonstration
        
        # Create grade level (assume grade 10 as default)
        self.df['Grade'] = 10
        
        print(f"Cleaned data shape: {self.df.shape}")
        print(f"Missing values:\n{self.df.isnull().sum()}")
        
        return self
        
    def create_subject_records(self):
        """
        Create individual records for each subject
        In the real system, students have multiple subjects
        For training, we'll simulate multiple subjects per student with realistic attendance
        """
        print("\n=== Creating Subject-wise Records ===")

        # Common subjects in schools
        subjects = ['Mathematics', 'Science', 'English', 'History', 'Geography']

        records = []

        for _, row in self.df.iterrows():
            student_id = int(row['StudentID'])
            base_marks = row.get('PreviousGrade', 0)
            age = row['Age']
            grade = row['Grade']

            # Generate realistic attendance distribution (0% to 100% range)
            # Based on real school data: wide range from poor to excellent attendance
            base_attendance = np.random.uniform(0, 100)  # Uniform distribution from 0-100%
            # Add slight variation to make it more realistic
            base_attendance = max(0, min(100, base_attendance + np.random.uniform(-5, 5)))

            # Create records for each subject with slight variations
            for subject in subjects:
                # Add random variation to make subjects different
                attendance = base_attendance + np.random.uniform(-8, 8)
                attendance = max(0, min(100, attendance))  # Clamp between 0-100%

                marks = base_marks + np.random.uniform(-15, 15)
                marks = max(0, min(100, marks))  # Clamp between 0-100%

                # Target: predict future performance (use FinalGrade as proxy)
                future_performance = row.get('FinalGrade', marks)
                if pd.isna(future_performance) or future_performance == 0:
                    # More realistic formula: attendance has moderate correlation with performance
                    attendance_factor = (attendance - 75) * 0.15  # Reduced correlation
                    future_performance = marks + attendance_factor + np.random.uniform(-5, 5)

                future_performance = max(0, min(100, future_performance))

                records.append({
                    'student_id': student_id,
                    'age': age,
                    'grade': grade,
                    'subject': subject,
                    'attendance': round(attendance, 2),
                    'marks': round(marks, 2),
                    'future_performance': round(future_performance, 2)
                })

        self.df_cleaned = pd.DataFrame(records)
        print(f"Created {len(self.df_cleaned)} subject-wise records")
        print(f"Attendance distribution: Mean={self.df_cleaned['attendance'].mean():.1f}%, Std={self.df_cleaned['attendance'].std():.1f}%")
        print(f"Attendance range: {self.df_cleaned['attendance'].min():.1f}% - {self.df_cleaned['attendance'].max():.1f}%")

        return self
        
    def save_cleaned_data(self, output_path=CLEANED_DATA_PATH):
        """Save cleaned data to CSV"""
        # Create data directory if it doesn't exist
        os.makedirs(DATA_DIR, exist_ok=True)
        
        self.df_cleaned.to_csv(output_path, index=False)
        print(f"\n✓ Cleaned data saved to: {output_path}")
        print(f"Total records: {len(self.df_cleaned)}")
        
        return self
        
    def get_statistics(self):
        """Print dataset statistics"""
        print("\n=== Dataset Statistics ===")
        print(f"\nNumerical columns summary:")
        print(self.df_cleaned[['age', 'grade', 'attendance', 'marks', 'future_performance']].describe())
        
        print(f"\nSubjects distribution:")
        print(self.df_cleaned['subject'].value_counts())
        
        return self


def main():
    """Main execution function"""
    print("=" * 60)
    print("STUDENT PERFORMANCE DATA PREPROCESSING")
    print("=" * 60)
    
    # Initialize preprocessor
    preprocessor = DataPreprocessor()
    
    # Execute preprocessing pipeline
    preprocessor.load_data() \
                .clean_data() \
                .create_subject_records() \
                .save_cleaned_data() \
                .get_statistics()
    
    print("\n" + "=" * 60)
    print("✓ Data preprocessing completed successfully!")
    print("=" * 60)


if __name__ == "__main__":
    main()
