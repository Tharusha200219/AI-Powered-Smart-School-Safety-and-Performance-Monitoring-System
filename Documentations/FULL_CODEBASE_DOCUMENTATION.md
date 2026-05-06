# 📚 FULL CODEBASE DOCUMENTATION: Smart School Safety and Performance Monitoring System

This document provides a comprehensive technical breakdown of the four core models implemented in the Smart School System. It includes implementation details, architectural descriptions, and the actual source code for key components.

---

## 📑 Table of Contents
1. [Facial Recognition Attendance System](#1-facial-recognition-attendance-system)
2. [RFID Wristband Attendance System (IoT)](#2-rfid-wristband-attendance-system-iot)
3. [Student Performance Prediction Model (AI)](#3-student-performance-prediction-model-ai)
4. [Student Seating Arrangement Model (Algorithm)](#4-student-seating-arrangement-model-algorithm)
5. [System Integration and API Orchestration](#5-system-integration-and-api-orchestration)

---

## 🤖 1. Facial Recognition Attendance System

### 1.1 Overview
The facial recognition system is a production-ready biometric attendance solution. It uses a Multi-task Cascaded Convolutional Network (MTCNN) for face detection and an InceptionResnetV1 (FaceNet/ArcFace) model for generating 512-dimensional face embeddings.

### 1.2 Implementation Workflow
1. **Detection**: MTCNN identifies bounding boxes and 5 facial landmarks (eyes, nose, mouth corners).
2. **Alignment**: The face is rotated and scaled based on landmarks to ensure eye centers are horizontal.
3. **Encoding**: The aligned face is passed through the pre-trained CNN to generate a numerical embedding.
4. **Matching**: Cosine similarity is calculated against the database of registered student embeddings.
5. **Marking**: If similarity > 0.6, the attendance is logged in the MySQL database and forwarded to the Laravel dashboard.

### 1.3 Core Implementation (app.py)
This is the main entry point for the Facial Recognition Flask API.

```python
"""
Facial Recognition Attendance System - Main Flask Application
"""
import os
import logging
import cv2
import numpy as np
from flask import Flask, jsonify, request
from flask_cors import CORS

# ... (Logging and Component Initializations)

@app.route('/recognize_face', methods=['POST'])
def recognize_face_root():
    """
    Root-level face recognition endpoint for Dashboard.
    """
    if 'image' not in request.files:
        return jsonify({'success': False, 'message': 'No image file provided'}), 400
    
    file = request.files['image']
    image_bytes = file.read()
    nparr = np.frombuffer(image_bytes, np.uint8)
    image = cv2.imdecode(nparr, cv2.IMREAD_COLOR)
    
    try:
        # Preprocess: Apply CLAHE for contrast normalization
        lab = cv2.cvtColor(image, cv2.COLOR_BGR2LAB)
        l, a, b = cv2.split(lab)
        clahe = cv2.createCLAHE(clipLimit=2.0, tileGridSize=(8, 8))
        l = clahe.apply(l)
        image = cv2.cvtColor(cv2.merge([l, a, b]), cv2.COLOR_LAB2BGR)
        
        # Process frame through the recognition engine
        results = app.attendance_engine.process_frame(image, mark_attendance=False)
        
        if not results:
            return jsonify({'success': False, 'message': 'No face detected', 'face_detected': False})
        
        best_result = results[0]
        
        if not best_result.is_recognized:
            return jsonify({'success': False, 'message': 'Face not recognized', 'face_detected': True})
        
        return jsonify({
            'success': True,
            'student_id': best_result.student_id,
            'confidence': float(best_result.confidence),
            'student_name': best_result.student_name,
            'face_detected': True
        })
        
    except Exception as e:
        return jsonify({'success': False, 'message': str(e)}), 500
```

---

## 📶 2. RFID Wristband Attendance System (IoT)

### 2.1 Hardware Architecture
- **Controller**: Arduino Uno R3 (SMD CH340).
- **RFID**: MFRC522 (RC522) reader connected via SPI.
- **Output**: 1602 LCD (I2C) for name display and RGB LED for status feedback.

### 2.2 Arduino Firmware (rfid_serial_reader.ino)
This firmware runs on the Arduino, reading tag UIDs and sending them via JSON to the serial port.

```cpp
/*
 * RFID Attendance Reader — Arduino UNO R3 + RC522 + LCD + RGB LED
 */
#include <SPI.h>
#include <MFRC522.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>

#define RC522_RST_PIN 9
#define RC522_SS_PIN 10
#define LED_GREEN 5

MFRC522 mfrc522(RC522_SS_PIN, RC522_RST_PIN);
LiquidCrystal_I2C lcd(0x27, 16, 2);

void setup() {
    Serial.begin(115200);
    SPI.begin();
    mfrc522.PCD_Init();
    lcd.init();
    lcd.backlight();
    lcd.print("SCAN WRISTBAND");
    pinMode(LED_GREEN, OUTPUT);
}

void loop() {
    if (!mfrc522.PICC_IsNewCardPresent() || !mfrc522.PICC_ReadCardSerial()) return;

    // Convert UID to hex string
    String uid = "";
    for (byte i = 0; i < mfrc522.uid.size; i++) {
        uid += String(mfrc522.uid.uidByte[i] < 0x10 ? "0" : "");
        uid += String(mfrc522.uid.uidByte[i], HEX);
    }
    uid.toUpperCase();

    // Send JSON to Bridge
    Serial.println("{\"uid\":\"" + uid + "\",\"device_id\":\"DOOR_01\"}");

    // Visual Feedback
    lcd.setCursor(0, 0);
    lcd.print(" SCAN SUCCESS! ");
    digitalWrite(LED_GREEN, HIGH);
    delay(500);
    digitalWrite(LED_GREEN, LOW);
    lcd.clear();
    lcd.print("SCAN WRISTBAND");
}
```

### 2.3 Python Serial Bridge (rfid_bridge.py)
This script runs on a connected PC, bridging the Arduino's serial output to the Laravel Web API.

```python
import serial
import requests
import json

SERIAL_PORT = "/dev/ttyUSB0"
BAUD_RATE = 115200
SERVER_URL = "http://127.0.0.1:8000/api/rfid/scan"

def main():
    ser = serial.Serial(SERIAL_PORT, BAUD_RATE, timeout=1)
    while True:
        line = ser.readline().decode('utf-8').strip()
        if line.startswith("{"):
            try:
                data = json.loads(line)
                uid = data.get("uid")
                # Forward to Laravel Dashboard
                resp = requests.post(SERVER_URL, json={"uid": uid})
                print(f"Forwarded UID: {uid} | Status: {resp.status_code}")
            except Exception as e:
                print(f"Error: {e}")

if __name__ == "__main__":
    main()
```

---

## 📊 3. Student Performance Prediction Model (AI)

### 3.1 Model Logic
The prediction engine uses **XGBoost** (Extreme Gradient Boosting) to forecast student marks for the upcoming term. It doesn't just look at raw scores; it performs **Feature Engineering** to calculate student "momentum" and "volatility."

### 3.2 Key Features
- **Temporal Features**: Term-wise marks (T1, T2, T3).
- **Attendance Impact**: Analyzes how attendance percentage correlates with score changes.
- **Volatility**: Identifies students with inconsistent performance.
- **Confidence Intervals**: Returns 95% numerical bounds for every prediction.

### 3.3 Core Implementation (predictor.py)
```python
class StudentPerformancePredictor:
    def __init__(self):
        self.model = joblib.load(MODEL_PATH)
        self.scaler = joblib.load(SCALER_PATH)

    def _engineer_features(self, age, grade, attendance, t1, t2, t3):
        marks_avg = (t1 + t2 + t3) / 3.0
        marks_slope = (t3 - t1) / 2.0 # Improvement trend
        marks_volatility = np.std([t1, t2, t3])
        is_crashing = 1 if (t2 - t3) > 30 else 0 # Sudden drop detection
        
        return {
            'attendance_score': attendance / 100.0,
            'marks_avg': marks_avg,
            'marks_slope': marks_slope,
            'marks_volatility': marks_volatility,
            'is_crashing': is_crashing
        }

    def predict(self, student_data):
        # Prepare and scale features
        X, subject_names = self.prepare_input(student_data)
        X_scaled = self.scaler.transform(X)
        
        # XGBoost Prediction
        predictions = self.model.predict(X_scaled)
        
        # Calculate 95% Confidence Interval
        # Method: 1.96 * StdDev (adjusted for attendance and consistency)
        results = []
        for i, pred in enumerate(predictions):
            lower, upper = self._calculate_confidence_interval(pred, attendance, marks)
            results.append({
                'subject': subject_names[i],
                'predicted_performance': round(float(pred), 2),
                '95_ci': [lower, upper],
                'trend': "Improving" if marks_slope > 5 else "Declining"
            })
        return results
```

---

## 🪑 4. Student Seating Arrangement Model (Algorithm)

### 4.1 Optimization Strategy: High-Low Pairing
This algorithm aims to maximize peer learning by strategically placing high-performing students next to those who may need academic support.

### 4.2 Algorithm Steps
1. Sort students by `average_marks` (Descending).
2. Use two pointers: `left` (starts at highest mark) and `right` (starts at lowest mark).
3. Assign seats in a zigzag pattern: **High -> Low -> 2nd High -> 2nd Low**.
4. Compute Row/Column coordinates based on classroom dimensions.

### 4.3 Core Implementation (seating_generator.py)
```python
class SeatingArrangementGenerator:
    def _generate_high_low_pairing(self, sorted_students):
        seating_map = []
        n = len(sorted_students)
        left = 0       # Highest pointer
        right = n - 1  # Lowest pointer
        seat_num = 1
        
        while left <= right:
            # Place High Performer
            if left <= right:
                seating_map.append(self._create_seat_assignment(
                    sorted_students[left], seat_num, 'high'
                ))
                seat_num += 1
                left += 1
            
            # Place Low Performer
            if left <= right:
                seating_map.append(self._create_seat_assignment(
                    sorted_students[right], seat_num, 'low'
                ))
                seat_num += 1
                right -= 1
        return seating_map

    def _create_seat_assignment(self, student, seat_number, perf_level):
        row = (seat_number - 1) // self.seats_per_row + 1
        col = (seat_number - 1) % self.seats_per_row + 1
        return {
            'seat_label': f"S{seat_number}",
            'row': row,
            'column': col,
            'student_name': student['name'],
            'performance': perf_level
        }
```

---

## 🔌 5. System Integration and API Orchestration

### 5.1 The Microservices Link
Each model acts as a standalone service, allowing the school system to remain responsive even under heavy AI processing loads.

| Module | Communication | Service Port | Data Type |
|--------|---------------|--------------|-----------|
| Facial Recognition | REST (Flask) | 5004 | Image/JSON |
| RFID Attendance | Serial -> REST | 8000 | UID/JSON |
| Performance Prediction | REST (Flask) | 5002 | Academic JSON |
| Seating Arrangement | REST (Flask) | 5003 | Classroom JSON |

### 5.2 Security & Data Privacy
- **Encrypted Biometrics**: Face images are deleted immediately after registration; only 512-dimensional numerical vectors are stored.
- **REST Authentication**: Each AI service validates incoming requests against a pre-shared API key defined in the Laravel `.env`.
