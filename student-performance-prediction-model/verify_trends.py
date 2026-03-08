import sys
import os

# Add src to path
sys.path.append(os.path.join(os.getcwd(), 'src'))
from predictor import StudentPerformancePredictor

def test_trends():
    predictor = StudentPerformancePredictor()
    
    # Test Case 1: Sharp Decline (The 94 -> 13 case)
    print("\nTESTING CASE 1: SHARP DECLINE (94 -> 13)")
    student_crash = {
        'student_id': 101,
        'age': 16,
        'grade': 11,
        'subjects': [
            {
                'subject_name': 'Mathematics',
                'term1_marks': 85,
                'term2_marks': 94,
                'term3_marks': 13,
                'attendance': 90
            }
        ]
    }
    
    results = predictor.predict(student_crash)
    for res in results:
        print(f"Subject: {res['subject']}")
        print(f"Term Marks: [85, 94, 13]")
        print(f"Predicted Performance: {res['predicted_performance']}")
        print(f"Trend: {res['prediction_trend']}")
        print(f"Category: {res['performance_category']}")
        
    # Test Case 2: Consistent Improvement (20 -> 50 -> 80)
    print("\nTESTING CASE 2: CONSISTENT IMPROVEMENT (20 -> 50 -> 80)")
    student_improve = {
        'student_id': 102,
        'age': 15,
        'grade': 10,
        'subjects': [
            {
                'subject_name': 'Science',
                'term1_marks': 20,
                'term2_marks': 50,
                'term3_marks': 80,
                'attendance': 95
            }
        ]
    }
    
    # Test Case 3: Fluctuating (40 -> 80 -> 50)
    print("\nTESTING CASE 3: FLUCTUATING (40 -> 80 -> 50)")
    student_fluctuate = {
        'student_id': 103,
        'age': 15,
        'grade': 10,
        'subjects': [
            {
                'subject_name': 'History',
                'term1_marks': 40,
                'term2_marks': 80,
                'term3_marks': 50,
                'attendance': 85
            }
        ]
    }
    
    results = predictor.predict(student_fluctuate)
    for res in results:
        print(f"Subject: {res['subject']}")
        print(f"Term Marks: [40, 80, 50]")
        print(f"Predicted Performance: {res['predicted_performance']}")
        print(f"Trend: {res['prediction_trend']}")

if __name__ == "__main__":
    test_trends()
