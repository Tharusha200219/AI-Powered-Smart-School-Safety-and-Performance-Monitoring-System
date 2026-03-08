"""
Quick API Test - Example Request/Response
Demonstrates how the Laravel frontend should call the API
"""

import requests
import json

# API endpoint (make sure API is running: python api/app.py)
API_URL = "http://localhost:5002/predict"

# Example: Student with mixed performance across different subjects
test_student = {
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
        },
        {
            "subject_name": "Science",
            "attendance": 60,
            "term1_marks": 45,
            "term2_marks": 50,
            "term3_marks": 55
        },
        {
            "subject_name": "English",
            "attendance": 80,
            "term1_marks": 70,
            "term2_marks": 72,
            "term3_marks": 75
        }
    ]
}

print("=" * 80)
print("STUDENT PERFORMANCE PREDICTION - API TEST")
print("=" * 80)
print("\n📤 REQUEST:")
print(json.dumps(test_student, indent=2))

try:
    response = requests.post(API_URL, json=test_student, timeout=10)
    
    if response.status_code == 200:
        result = response.json()
        print("\n✅ SUCCESS!")
        print("\n📥 RESPONSE:")
        print(json.dumps(result, indent=2))
        
        print("\n" + "=" * 80)
        print("UI DISPLAY FORMAT:")
        print("=" * 80)
        
        for pred in result['predictions']:
            print(f"\n📚 {pred['subject']}")
            print(f"   {'─' * 60}")
            print(f"   📊 Attendance:        {pred['attendance']}%")
            print(f"   📝 Term 1 Marks:      {pred['term1_marks']}")
            print(f"   📝 Term 2 Marks:      {pred['term2_marks']}")
            print(f"   📝 Term 3 Marks:      {pred['term3_marks']}")
            print(f"   {'─' * 60}")
            print(f"   🎯 Predicted Score:   {pred['predicted_performance']}")
            print(f"   📈 Trend:             {pred['prediction_trend']}")
            print(f"   ⭐ Category:          {pred['performance_category']}")
            print(f"   📊 95% CI:            [{pred['confidence_interval']['lower_bound']}, {pred['confidence_interval']['upper_bound']}]")
            print(f"   💡 Recommendation:    {pred['recommendation'][:60]}...")
        
    else:
        print(f"\n❌ ERROR: {response.status_code}")
        print(response.text)
        
except requests.exceptions.ConnectionError:
    print("\n❌ ERROR: Cannot connect to API")
    print("   Make sure the API is running:")
    print("   cd student-performance-prediction-model")
    print("   source venv/bin/activate")
    print("   python api/app.py")
    
except Exception as e:
    print(f"\n❌ ERROR: {str(e)}")

print("\n" + "=" * 80)
