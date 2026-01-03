# Student Performance Prediction Model - Setup Guide

Step-by-step guide to run the Performance Prediction API from scratch or daily use.

---

## 📋 Table of Contents

1. [Run from Scratch (New PC)](#run-from-scratch-new-pc)
2. [Run After Already Setup](#run-after-already-setup)
3. [Testing the Model](#testing-the-model)
4. [Demo Without Laravel](#demo-without-laravel)
5. [Troubleshooting](#troubleshooting)

---

## 🆕 Run from Scratch (New PC)

Complete setup guide for a fresh installation.

### Prerequisites

**Check Python Version:**

```bash
python3 --version
# Should be Python 3.8 or higher
```

**Check pip:**

```bash
pip3 --version
```

**If Python not installed:**

- macOS: `brew install python3`
- Ubuntu: `sudo apt install python3 python3-pip`
- Windows: Download from python.org

### Step 1: Navigate to Model Directory

```bash
cd student-performance-prediction-model
```

### Step 2: Run Automated Setup

```bash
# Make setup script executable
chmod +x setup.sh

# Run setup (this will take 2-5 minutes)
./setup.sh
```

**What Happens During Setup:**

1. ✅ Creates virtual environment (`venv/`)
2. ✅ Installs all dependencies
3. ✅ Preprocesses the dataset
4. ✅ Trains the Linear Regression model
5. ✅ Saves model files to `models/` directory
6. ✅ Runs system tests
7. ✅ Verifies everything works

**Expected Output:**

```
============================================================
Student Performance Prediction Model - Setup
============================================================

Step 1: Creating virtual environment...
✓ Virtual environment created

Step 2: Installing dependencies...
✓ Dependencies installed successfully

Step 3: Preprocessing data...
✓ Data preprocessing complete
  - Loaded 1000 student records
  - Cleaned and processed data
  - Saved to: data/preprocessed_data.csv

Step 4: Training model...
✓ Model training complete
  - Model: Linear Regression
  - R² Score: 0.85
  - MSE: 35.2
  - Saved model files to: models/

Step 5: Running tests...
✓ All tests passed

============================================================
✅ Setup Complete!
============================================================

Next steps:
  1. Start API: cd api && python app.py
  2. Test API: python test_system.py
```

### Step 3: Verify Installation

```bash
# Check that models were created
ls -la models/

# You should see:
# - performance_predictor.pkl
# - scaler.pkl
# - label_encoder.pkl
```

### Step 4: Start the API

```bash
# Activate virtual environment
source venv/bin/activate

# Start API server
cd api
python app.py
```

**Expected Output:**

```
 * Serving Flask app 'app'
 * Debug mode: off
WARNING: This is a development server.
 * Running on http://0.0.0.0:5002
Press CTRL+C to quit
```

### Step 5: Test API (In New Terminal)

```bash
# Test health endpoint
curl http://localhost:5002/health

# Expected response:
# {
#   "service": "Student Performance Prediction API",
#   "status": "healthy",
#   "version": "1.0.0"
# }
```

**✅ Setup Complete!** API is running on port 5002.

---

## 🔄 Run After Already Setup

If you ran the setup yesterday and just want to start working again.

### Quick Start (API Only)

```bash
# Navigate to model directory
cd student-performance-prediction-model

# Activate virtual environment
source venv/bin/activate

# Start API
cd api
python app.py
```

**That's it!** API runs on http://localhost:5002

### Using the Convenience Script

```bash
# From project root
cd student-performance-prediction-model

# Make script executable (first time only)
chmod +x start_api.sh

# Start API
./start_api.sh
```

### Background Mode

If you want API to run in background:

```bash
cd student-performance-prediction-model
source venv/bin/activate
cd api
nohup python app.py > /tmp/performance_api.log 2>&1 &

# Get the process ID
echo $!
```

**Stop Background API:**

```bash
# Find process
lsof -ti:5002

# Kill process
lsof -ti:5002 | xargs kill -9
```

### Check if Already Running

```bash
# Check if port 5002 is in use
lsof -ti:5002

# If it returns a number, API is already running
# If empty, API is not running
```

---

## 🧪 Testing the Model

Multiple ways to test your model is working correctly.

### Test 1: Health Check

**Check API is responsive:**

```bash
curl http://localhost:5002/health
```

**Expected:**

```json
{
  "service": "Student Performance Prediction API",
  "status": "healthy",
  "version": "1.0.0"
}
```

### Test 2: Run Test Script

**Comprehensive system test:**

```bash
cd student-performance-prediction-model
source venv/bin/activate
python test_system.py
```

**What It Tests:**

- ✅ Model files exist
- ✅ API is running
- ✅ Health endpoint responds
- ✅ Prediction endpoint works
- ✅ Multiple student predictions
- ✅ Error handling
- ✅ Response format

**Expected Output:**

```
============================================================
Performance Prediction Model - System Test
============================================================

Test 1: Checking model files...
✓ Model file exists
✓ Scaler file exists
✓ Encoder file exists

Test 2: Testing API health...
✓ API is healthy

Test 3: Testing single student prediction...
Request:
{
  "students": [
    {
      "student_id": 1,
      "age": 16,
      "grade": 11,
      "subject": "Mathematics",
      "marks": 85,
      "attendance": 92
    }
  ]
}

Response:
{
  "success": true,
  "predictions": [
    {
      "student_id": 1,
      "predicted_performance": 87.5,
      "subject": "Mathematics"
    }
  ]
}
✓ Prediction successful

Test 4: Testing multiple students...
✓ Multiple predictions successful

Test 5: Testing error handling...
✓ Error handling works

============================================================
✅ All Tests Passed!
============================================================
```

### Test 3: Manual API Testing

**Test with curl:**

```bash
curl -X POST http://localhost:5002/predict \
  -H "Content-Type: application/json" \
  -d '{
    "students": [
      {
        "student_id": 1,
        "age": 16,
        "grade": 11,
        "subject": "Mathematics",
        "marks": 85,
        "attendance": 92
      }
    ]
  }'
```

**Test with Postman:**

1. Open Postman
2. Set method to POST
3. URL: `http://localhost:5002/predict`
4. Headers: `Content-Type: application/json`
5. Body (raw JSON):

```json
{
  "students": [
    {
      "student_id": 1,
      "age": 16,
      "grade": 11,
      "subject": "Mathematics",
      "marks": 85,
      "attendance": 92
    }
  ]
}
```

### Test 4: Python Script Testing

**Create test_manual.py:**

```python
import requests
import json

# API endpoint
url = "http://localhost:5002/predict"

# Test data
data = {
    "students": [
        {
            "student_id": 1,
            "age": 16,
            "grade": 11,
            "subject": "Mathematics",
            "marks": 85,
            "attendance": 92
        },
        {
            "student_id": 2,
            "age": 15,
            "grade": 10,
            "subject": "Science",
            "marks": 75,
            "attendance": 85
        }
    ]
}

# Make request
response = requests.post(url, json=data)

# Print results
print("Status Code:", response.status_code)
print("\nResponse:")
print(json.dumps(response.json(), indent=2))
```

**Run:**

```bash
python test_manual.py
```

---

## 🎯 Demo Without Laravel

Show the model working with sample data independently.

### Method 1: Interactive Python Demo

**Create demo.py:**

```python
#!/usr/bin/env python3
"""
Interactive Performance Prediction Demo
Demonstrates the model without Laravel dashboard
"""

import sys
import os
sys.path.append(os.path.join(os.path.dirname(__file__), 'src'))

from predictor import PerformancePredictor

def main():
    print("=" * 60)
    print("Student Performance Prediction - Live Demo")
    print("=" * 60)
    print()

    # Initialize predictor
    print("Loading trained model...")
    predictor = PerformancePredictor()
    print("✓ Model loaded successfully\n")

    # Sample students
    students = [
        {
            "student_id": 1,
            "name": "John Doe",
            "age": 16,
            "grade": 11,
            "subject": "Mathematics",
            "marks": 85,
            "attendance": 92
        },
        {
            "student_id": 2,
            "name": "Jane Smith",
            "age": 15,
            "grade": 10,
            "subject": "Science",
            "marks": 78,
            "attendance": 88
        },
        {
            "student_id": 3,
            "name": "Bob Wilson",
            "age": 17,
            "grade": 12,
            "subject": "English",
            "marks": 92,
            "attendance": 95
        },
        {
            "student_id": 4,
            "name": "Alice Brown",
            "age": 16,
            "grade": 11,
            "subject": "History",
            "marks": 70,
            "attendance": 80
        }
    ]

    # Display student data
    print("Sample Students:")
    print("-" * 60)
    for student in students:
        print(f"ID: {student['student_id']} | {student['name']}")
        print(f"   Age: {student['age']} | Grade: {student['grade']}")
        print(f"   Subject: {student['subject']}")
        print(f"   Current Marks: {student['marks']}%")
        print(f"   Attendance: {student['attendance']}%")
        print()

    # Generate predictions
    print("Generating predictions...\n")
    predictions = predictor.predict(students)

    # Display predictions
    print("=" * 60)
    print("PREDICTIONS")
    print("=" * 60)
    print()

    for i, pred in enumerate(predictions):
        student = students[i]
        predicted_score = pred['predicted_performance']

        # Determine performance level
        if predicted_score >= 85:
            level = "Excellent"
            emoji = "🌟"
        elif predicted_score >= 75:
            level = "Good"
            emoji = "✅"
        elif predicted_score >= 60:
            level = "Average"
            emoji = "⚠️"
        else:
            level = "Needs Support"
            emoji = "🚨"

        print(f"{emoji} {student['name']}")
        print(f"   Subject: {student['subject']}")
        print(f"   Current Performance: {student['marks']}%")
        print(f"   Predicted Performance: {predicted_score:.1f}%")
        print(f"   Assessment: {level}")

        # Recommendation
        if predicted_score < student['marks']:
            print(f"   ⚠️  Warning: Performance may decline")
        elif predicted_score > student['marks']:
            print(f"   📈 Good: Performance expected to improve")
        else:
            print(f"   ➡️  Stable: Performance likely to remain consistent")

        print()

    print("=" * 60)
    print("Demo Complete!")
    print("=" * 60)

if __name__ == "__main__":
    main()
```

**Run Demo:**

```bash
cd student-performance-prediction-model
source venv/bin/activate
python demo.py
```

**Expected Output:**

```
============================================================
Student Performance Prediction - Live Demo
============================================================

Loading trained model...
✓ Model loaded successfully

Sample Students:
------------------------------------------------------------
ID: 1 | John Doe
   Age: 16 | Grade: 11
   Subject: Mathematics
   Current Marks: 85%
   Attendance: 92%

ID: 2 | Jane Smith
   Age: 15 | Grade: 10
   Subject: Science
   Current Marks: 78%
   Attendance: 88%

...

============================================================
PREDICTIONS
============================================================

🌟 John Doe
   Subject: Mathematics
   Current Performance: 85%
   Predicted Performance: 87.5%
   Assessment: Excellent
   📈 Good: Performance expected to improve

✅ Jane Smith
   Subject: Science
   Current Performance: 78%
   Predicted Performance: 80.2%
   Assessment: Good
   📈 Good: Performance expected to improve

...

============================================================
Demo Complete!
============================================================
```

### Method 2: API Demo with curl

**Create sample_requests.sh:**

```bash
#!/bin/bash

echo "======================================================"
echo "Performance Prediction API - Demo"
echo "======================================================"
echo

# Check if API is running
echo "1. Checking API health..."
curl -s http://localhost:5002/health | python3 -m json.tool
echo
echo

# Test single student
echo "2. Predicting for single student..."
echo "   Student: John (Age 16, Grade 11, Math, 85%, 92% attendance)"
curl -s -X POST http://localhost:5002/predict \
  -H "Content-Type: application/json" \
  -d '{
    "students": [
      {
        "student_id": 1,
        "age": 16,
        "grade": 11,
        "subject": "Mathematics",
        "marks": 85,
        "attendance": 92
      }
    ]
  }' | python3 -m json.tool
echo
echo

# Test multiple students
echo "3. Predicting for multiple students..."
curl -s -X POST http://localhost:5002/predict \
  -H "Content-Type: application/json" \
  -d '{
    "students": [
      {
        "student_id": 1,
        "age": 16,
        "grade": 11,
        "subject": "Mathematics",
        "marks": 85,
        "attendance": 92
      },
      {
        "student_id": 2,
        "age": 15,
        "grade": 10,
        "subject": "Science",
        "marks": 78,
        "attendance": 88
      },
      {
        "student_id": 3,
        "age": 17,
        "grade": 12,
        "subject": "English",
        "marks": 92,
        "attendance": 95
      }
    ]
  }' | python3 -m json.tool
echo
echo

echo "======================================================"
echo "Demo Complete!"
echo "======================================================"
```

**Run:**

```bash
chmod +x sample_requests.sh
./sample_requests.sh
```

### Method 3: Web Interface Demo

**Create simple_web_demo.html:**

```html
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Performance Prediction Demo</title>
    <style>
      body {
        font-family: Arial, sans-serif;
        max-width: 800px;
        margin: 50px auto;
        padding: 20px;
        background: #f5f5f5;
      }
      .container {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      }
      h1 {
        color: #333;
        text-align: center;
      }
      .input-group {
        margin: 15px 0;
      }
      label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
      }
      input,
      select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        box-sizing: border-box;
      }
      button {
        width: 100%;
        padding: 15px;
        background: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        margin-top: 20px;
      }
      button:hover {
        background: #0056b3;
      }
      .result {
        margin-top: 30px;
        padding: 20px;
        background: #e7f3ff;
        border-radius: 5px;
        display: none;
      }
      .result.show {
        display: block;
      }
      .prediction-score {
        font-size: 48px;
        font-weight: bold;
        color: #007bff;
        text-align: center;
        margin: 20px 0;
      }
    </style>
  </head>
  <body>
    <div class="container">
      <h1>🎓 Student Performance Predictor</h1>

      <div class="input-group">
        <label>Age:</label>
        <input type="number" id="age" value="16" min="10" max="25" />
      </div>

      <div class="input-group">
        <label>Grade:</label>
        <select id="grade">
          <option value="9">Grade 9</option>
          <option value="10">Grade 10</option>
          <option value="11" selected>Grade 11</option>
          <option value="12">Grade 12</option>
          <option value="13">Grade 13</option>
        </select>
      </div>

      <div class="input-group">
        <label>Subject:</label>
        <select id="subject">
          <option value="Mathematics" selected>Mathematics</option>
          <option value="Science">Science</option>
          <option value="English">English</option>
          <option value="History">History</option>
          <option value="Physics">Physics</option>
        </select>
      </div>

      <div class="input-group">
        <label>Current Marks (%):</label>
        <input type="number" id="marks" value="85" min="0" max="100" />
      </div>

      <div class="input-group">
        <label>Attendance (%):</label>
        <input type="number" id="attendance" value="92" min="0" max="100" />
      </div>

      <button onclick="predictPerformance()">🔮 Predict Performance</button>

      <div class="result" id="result">
        <h2>Prediction Result:</h2>
        <div class="prediction-score" id="score">--</div>
        <p id="assessment" style="text-align: center; font-size: 20px;"></p>
      </div>
    </div>

    <script>
      async function predictPerformance() {
        // Get input values
        const data = {
          students: [
            {
              student_id: 1,
              age: parseInt(document.getElementById("age").value),
              grade: parseInt(document.getElementById("grade").value),
              subject: document.getElementById("subject").value,
              marks: parseInt(document.getElementById("marks").value),
              attendance: parseInt(document.getElementById("attendance").value),
            },
          ],
        };

        try {
          // Call API
          const response = await fetch("http://localhost:5002/predict", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify(data),
          });

          const result = await response.json();

          if (result.success) {
            const prediction = result.predictions[0];
            const score = prediction.predicted_performance;

            // Display result
            document.getElementById("score").textContent =
              score.toFixed(1) + "%";

            let assessment = "";
            if (score >= 85) {
              assessment = "🌟 Excellent Performance Expected!";
            } else if (score >= 75) {
              assessment = "✅ Good Performance Expected";
            } else if (score >= 60) {
              assessment = "⚠️ Average Performance Expected";
            } else {
              assessment = "🚨 Needs Support";
            }

            document.getElementById("assessment").textContent = assessment;
            document.getElementById("result").classList.add("show");
          } else {
            alert("Error: " + result.error);
          }
        } catch (error) {
          alert(
            "Error connecting to API. Make sure API is running on port 5002."
          );
          console.error(error);
        }
      }
    </script>
  </body>
</html>
```

**Run:**

1. Make sure API is running on port 5002
2. Open `simple_web_demo.html` in a web browser
3. Enter student details
4. Click "Predict Performance"
5. See the prediction result!

---

## 🐛 Troubleshooting

### Problem 1: Virtual Environment Not Found

**Error:** `venv/bin/activate: No such file or directory`

**Solution:**

```bash
# Run setup first
./setup.sh

# Or create manually
python3 -m venv venv
source venv/bin/activate
pip install -r requirements.txt
```

### Problem 2: Model Files Not Found

**Error:** `FileNotFoundError: models/performance_predictor.pkl not found`

**Solution:**

```bash
# Train the model
source venv/bin/activate
python src/data_preprocessing.py
python src/model_trainer.py
```

### Problem 3: Port Already in Use

**Error:** `Address already in use: Port 5002`

**Solution:**

```bash
# Find process using port
lsof -ti:5002

# Kill the process
lsof -ti:5002 | xargs kill -9

# Start API again
python api/app.py
```

### Problem 4: Module Not Found

**Error:** `ModuleNotFoundError: No module named 'flask'`

**Solution:**

```bash
# Make sure virtual environment is activated
source venv/bin/activate

# Reinstall dependencies
pip install -r requirements.txt
```

### Problem 5: API Not Responding

**Check if running:**

```bash
curl http://localhost:5002/health
```

**Check logs:**

```bash
# If running in background
tail -f /tmp/performance_api.log
```

**Restart:**

```bash
# Stop
lsof -ti:5002 | xargs kill -9

# Start
cd api && python app.py
```

### Problem 6: Wrong Python Version

**Error:** `Python 3.6 or higher required`

**Solution:**

```bash
# Check version
python3 --version

# Use specific version
python3.11 -m venv venv
```

### Problem 7: Permission Denied

**Error:** `Permission denied: ./setup.sh`

**Solution:**

```bash
chmod +x setup.sh
chmod +x start_api.sh
```

---

## 🔍 Verification Checklist

Before considering setup complete, verify:

- [ ] Virtual environment created (`venv/` folder exists)
- [ ] Dependencies installed (`pip list` shows flask, pandas, sklearn)
- [ ] Model files created in `models/` directory
- [ ] Data preprocessed in `data/` directory
- [ ] API starts without errors
- [ ] Health endpoint responds: `curl http://localhost:5002/health`
- [ ] Prediction endpoint works: run `test_system.py`
- [ ] No errors in logs

---

## 📞 Quick Reference

```bash
# Setup from scratch
./setup.sh

# Start API (manual)
source venv/bin/activate && cd api && python app.py

# Start API (script)
./start_api.sh

# Stop API
lsof -ti:5002 | xargs kill -9

# Test API
python test_system.py

# Check if running
lsof -ti:5002

# View logs
tail -f /tmp/performance_api.log

# Retrain model
source venv/bin/activate
python src/model_trainer.py
```

---

**Last Updated:** January 3, 2026
