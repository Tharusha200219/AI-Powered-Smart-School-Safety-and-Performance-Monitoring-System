# Facial Recognition Attendance - Complete Workflow Guide

## 🚀 Complete System Workflows

This guide explains exactly what happens when you:

1. **Enroll a New Student** - How face data is stored
2. **Mark Attendance** - How recognition and attendance work in real-time
3. **Generate Reports** - How attendance data is analyzed

---

## 📸 WORKFLOW 1: ENROLLING A NEW STUDENT

### Goal

Capture and store a student's face so the system can recognize them during attendance.

### Step-by-Step Process

#### **STEP 1: Student Arrives at Enrollment Station**

**Scenario:** New student "Ahmed Hassan" needs enrollment for attendance system

```
Student: Ahmed Hassan
Grade: 10
Student ID: S12345
Date: 2024-05-05
```

---

#### **STEP 2: Capture Multiple Face Photos**

```python
# File: core/attendance_engine.py → enroll_student()

print("📸 Starting Student Enrollment")
print("Instructions: Look at camera, different angles")
print("Capturing 5 photos from different angles...")

photos_captured = 0

while photos_captured < 5:
    # Capture from webcam
    frame = capture_frame_from_camera()

    # Check image quality
    blurriness = check_image_blur(frame)

    if blurriness < 100:  # Good quality
        print(f"✓ Photo {photos_captured + 1}: Good quality")
        save_photo(frame, f"training_data/S12345_photo_{photos_captured+1}.jpg")
        photos_captured += 1
    else:
        print("✗ Photo blurry, please try again")

print("✓ Captured 5 clear photos")
```

**Photos captured:**

```
training_data/
├─ S12345_photo_1.jpg (front view)
├─ S12345_photo_2.jpg (left angle)
├─ S12345_photo_3.jpg (right angle)
├─ S12345_photo_4.jpg (slightly up)
└─ S12345_photo_5.jpg (slightly down)
```

---

#### **STEP 3: Detect Faces in Each Photo**

```python
# File: core/face_detector.py

face_detections = []

for photo_path in photos:
    # Load image
    image = cv2.imread(photo_path)

    # Detect faces
    faces = detector.detect_faces(image)

    # Should detect exactly 1 face per photo
    if len(faces) == 1:
        face = faces[0]
        print(f"✓ {photo_path}: 1 face detected")
        print(f"  Confidence: {face.confidence:.2f}")
        print(f"  Bounding box: {face.bbox}")
        face_detections.append((photo_path, face))
    elif len(faces) == 0:
        print(f"✗ {photo_path}: No face detected!")
    else:
        print(f"✗ {photo_path}: Multiple faces detected ({len(faces)})!")

if len(face_detections) < 5:
    print("⚠ Not all photos have clear faces. Re-capture needed.")
else:
    print("✓ All 5 photos have clear face detections")
```

**Output:**

```
✓ photo_1.jpg: 1 face detected, Confidence: 0.98
✓ photo_2.jpg: 1 face detected, Confidence: 0.95
✓ photo_3.jpg: 1 face detected, Confidence: 0.97
✓ photo_4.jpg: 1 face detected, Confidence: 0.93
✓ photo_5.jpg: 1 face detected, Confidence: 0.96
```

---

#### **STEP 4: Align Detected Faces**

```python
# File: core/face_aligner.py

aligned_faces = []

for photo_path, face_detection in face_detections:
    # Load image
    image = cv2.imread(photo_path)

    # Detect landmarks (eyes, nose, mouth)
    landmarks = detect_landmarks(image, face_detection)

    # Calculate rotation needed
    angle = calculate_face_angle(landmarks)

    # Align face
    aligned_face = align_face(image, landmarks, angle)

    # Crop to face region only
    aligned_face_cropped = crop_to_face_region(aligned_face)

    aligned_faces.append(aligned_face_cropped)
    print(f"✓ Aligned and cropped {photo_path}")
    print(f"  Rotation correction: {angle}°")

print("✓ All 5 faces aligned and cropped")
```

**Alignment example:**

```
Before:                After:
(Tilted face)          (Straight face)
  \                      |
   \___                  |___
       \                     \
```

---

#### **STEP 5: Generate Face Encodings**

```python
# File: core/face_recognizer.py

face_encodings = []

for aligned_face in aligned_faces:
    # Convert face image to encoding
    # (128-dimensional vector representing unique face features)
    encoding = generate_face_encoding(aligned_face)

    face_encodings.append(encoding)
    print(f"✓ Generated encoding for face {len(face_encodings)}")
    print(f"  Encoding shape: {encoding.shape}")  # (128,)
    print(f"  Sample values: {encoding[:5]}")     # First 5 values

# Average all 5 encodings for better accuracy
student_master_encoding = np.mean(face_encodings, axis=0)

print("\n✓ Master encoding created (average of 5 photos)")
print(f"Master encoding shape: {student_master_encoding.shape}")
print(f"Sample values: {student_master_encoding[:5]}")
```

**What is encoding?**

```
Face Image (224×224 pixels)
    ↓
[Process through neural network]
    ↓
Face Encoding
[0.23, -0.45, 0.12, 0.78, ..., -0.34]  (128 numbers)
    ↓
This 128-number vector is the "fingerprint" of the face
Similar faces have similar encodings
```

---

#### **STEP 6: Check Liveness (Anti-Spoofing)**

```python
# File: core/anti_spoof.py

liveness_checks = []

for aligned_face in aligned_faces:
    # Check 1: Texture analysis (real face vs photo)
    texture_score = analyze_texture(aligned_face)

    # Check 2: Frequency domain check
    freq_score = check_frequency_patterns(aligned_face)

    # Combine scores
    liveness_score = (texture_score + freq_score) / 2

    liveness_checks.append(liveness_score)

    status = "✓ REAL" if liveness_score > 0.7 else "✗ SPOOF DETECTED"
    print(f"Photo {len(liveness_checks)}: {status}")
    print(f"  Liveness score: {liveness_score:.2f}")

avg_liveness = np.mean(liveness_checks)

if avg_liveness > 0.7:
    print("\n✓ Student is a real person (liveness verified)")
else:
    print("\n✗ SPOOF DETECTED! Enrollment rejected.")
    # Stop enrollment
```

**Why anti-spoofing?**

- Prevents someone using a photo of Ahmed to enroll as Ahmed
- Prevents using a screen or mask
- Ensures only real people can be enrolled

---

#### **STEP 7: Store Face Data in Database**

```python
# File: database/enrollment_service.py

# Create enrollment record
enrollment_record = {
    'student_id': 'S12345',
    'name': 'Ahmed Hassan',
    'grade': 10,
    'enrollment_date': datetime.now().isoformat(),
    'face_template': student_master_encoding.tolist(),  # 128 numbers
    'encoding_version': '1.0',
    'liveness_verified': True,
    'photos_count': 5,
    'quality_score': avg_quality_score,
    'status': 'active'
}

# Save to database
db.insert_enrollment(enrollment_record)

print("✓ Enrollment record saved to database")
print(f"  Student ID: S12345")
print(f"  Face template size: {len(student_master_encoding)} dimensions")
print(f"  Database table: 'enrollments'")
```

**Database structure:**

```
enrollments table:
┌──────────────────────────────────┐
│ S12345 | Ahmed Hassan | [0.23, -0.45, ..., -0.34] │
│ S12346 | Fatima Ali   | [-0.15, 0.62, ..., 0.21]  │
│ S12347 | Omar Khan    | [0.44, -0.12, ..., 0.56]  │
└──────────────────────────────────┘
```

---

#### **STEP 8: Store Training Photos** (For Future Improvement)

```python
# File: training/photo_storage.py

# Archive photos for model improvement
import shutil

for i, photo_path in enumerate(photos, 1):
    # Move to training dataset
    destination = f"training/face_templates/S12345/photo_{i}.jpg"
    shutil.copy(photo_path, destination)
    print(f"✓ Archived {photo_path} → {destination}")

print("✓ Training photos archived for future model retraining")
```

---

#### ✅ **ENROLLMENT COMPLETE!**

```
✓ 5 photos captured
✓ Faces detected and aligned
✓ Encodings generated
✓ Liveness verified
✓ Database record created
✓ Photos archived

Ahmed Hassan is now enrolled!
Ready for attendance marking.
```

---

---

## ✅ WORKFLOW 2: MARKING ATTENDANCE

### Goal

Recognize students from camera feed and automatically mark them present.

### Step-by-Step Process

#### **STEP 1: Camera Captures Frame**

**Scenario:** Class starts at 8:00 AM. Students stand in front of camera.

```python
# File: core/attendance_engine.py → mark_attendance_from_camera()

print("📷 Attendance System Started")
print("Time: 08:00:00 AM")
print("Listening to camera...")

# Continuous camera loop
while True:
    # Capture frame from camera
    frame = camera.read()  # 1920×1440 resolution

    # Frame data: RGB image array
    print(f"✓ Frame captured: {frame.shape}")  # (1440, 1920, 3)

    # Continue to next step
    break
```

---

#### **STEP 2: Detect Faces in Frame**

```python
# File: core/face_detector.py

faces_detected = detector.detect_faces(frame)

print(f"📍 Faces detected: {len(faces_detected)}")

for i, face in enumerate(faces_detected, 1):
    print(f"\nFace {i}:")
    print(f"  Bounding box: {face.bbox}")
    print(f"  Confidence: {face.confidence:.2f}")
    print(f"  Position: Row {face.bbox[1]}-{face.bbox[3]}, Col {face.bbox[0]}-{face.bbox[2]}")

# Example output:
# Faces detected: 3
#
# Face 1:
#   Bounding box: (150, 200, 400, 500)
#   Confidence: 0.97
#   Position: Row 200-500, Col 150-400
#
# Face 2:
#   Bounding box: (600, 180, 900, 520)
#   Confidence: 0.96
#   Position: Row 180-520, Col 600-900
```

---

#### **STEP 3: Align Each Detected Face**

```python
# File: core/face_aligner.py

aligned_faces_list = []

for i, face in enumerate(faces_detected, 1):
    # Extract face region from frame
    face_region = extract_face_region(frame, face.bbox)

    # Detect landmarks
    landmarks = detect_landmarks(face_region, face)

    # Align
    aligned_face = align_face(face_region, landmarks)

    aligned_faces_list.append(aligned_face)
    print(f"✓ Face {i} aligned")

print(f"✓ All {len(aligned_faces_list)} faces aligned")
```

---

#### **STEP 4: Check Liveness (Anti-Spoofing)**

```python
# File: core/anti_spoof.py

live_faces_list = []

for i, aligned_face in enumerate(aligned_faces_list, 1):
    # Liveness check
    liveness_score = check_liveness(aligned_face)

    is_live = liveness_score > 0.7

    if is_live:
        print(f"✓ Face {i}: REAL PERSON (score: {liveness_score:.2f})")
        live_faces_list.append(aligned_face)
    else:
        print(f"✗ Face {i}: FAKE/SPOOF (score: {liveness_score:.2f})")
        # Don't process this face further

print(f"✓ {len(live_faces_list)} real people detected")
```

---

#### **STEP 5: Generate Encodings for Detected Faces**

```python
# File: core/face_recognizer.py

detected_encodings = []

for i, aligned_face in enumerate(live_faces_list, 1):
    # Generate encoding
    encoding = generate_face_encoding(aligned_face)

    detected_encodings.append(encoding)
    print(f"✓ Encoding generated for person {i}")

print(f"✓ {len(detected_encodings)} encodings generated")
```

---

#### **STEP 6: Compare with Enrolled Students**

```python
# File: core/face_recognizer.py

# Load all enrolled students from database
enrolled_students = db.load_all_enrollments()  # Load all 500+ students

recognition_results = []

for person_idx, detected_encoding in enumerate(detected_encodings, 1):
    print(f"\n🔍 Recognizing person {person_idx}...")

    best_match = None
    best_distance = float('inf')

    # Compare with each enrolled student
    for student in enrolled_students:
        student_encoding = np.array(student['face_template'])

        # Calculate distance between encodings
        # (smaller distance = more similar = same person)
        distance = np.linalg.norm(detected_encoding - student_encoding)

        if distance < best_distance:
            best_distance = distance
            best_match = student

    # Convert distance to confidence (0-1 scale)
    # Threshold: distance ≤ 0.6 = recognized
    confidence = max(0, 1 - (best_distance / 0.6))
    is_recognized = best_distance ≤ 0.6

    result = {
        'person_index': person_idx,
        'is_recognized': is_recognized,
        'student_id': best_match['student_id'] if is_recognized else None,
        'name': best_match['name'] if is_recognized else 'UNKNOWN',
        'distance': best_distance,
        'confidence': confidence
    }

    recognition_results.append(result)

    status = "✓ RECOGNIZED" if is_recognized else "✗ UNKNOWN"
    print(f"{status}: {result['name']}")
    print(f"  Distance: {best_distance:.4f}")
    print(f"  Confidence: {confidence*100:.1f}%")

# Output:
# 🔍 Recognizing person 1...
# ✓ RECOGNIZED: Ahmed Hassan
#   Distance: 0.3421
#   Confidence: 95.3%
#
# 🔍 Recognizing person 2...
# ✓ RECOGNIZED: Fatima Ali
#   Distance: 0.4123
#   Confidence: 87.2%
#
# 🔍 Recognizing person 3...
# ✗ UNKNOWN: (Visitor?)
#   Distance: 0.8234
#   Confidence: 3.1%
```

**How recognition works:**

```
Detected Face                Enrolled Students
    ↓                              ↓
   [encoding]                  [encoding1]
   [0.23,                       [0.15,
    -0.45,                       0.22,
     0.12,                      -0.08,
     ...]                        ...]
        ↓
    Calculate Distance
    distance = √Σ(xi - yi)²

    Person 1: distance = 0.34 < 0.6 → MATCH! (Ahmed)
    Person 2: distance = 0.41 < 0.6 → MATCH! (Fatima)
    Person 3: distance = 0.82 > 0.6 → NO MATCH (Unknown)
```

---

#### **STEP 7: Verify Not Already Marked Today**

```python
# File: services/attendance_service.py

# Get today's date
today = datetime.now().date()

for result in recognition_results:
    if not result['is_recognized']:
        print(f"  {result['name']}: Skipped (not recognized)")
        continue

    student_id = result['student_id']

    # Check if already marked today
    already_marked = db.check_attendance(student_id, today)

    if already_marked:
        print(f"  {result['name']}: Already marked at {already_marked['time']}")
    else:
        result['should_mark'] = True
        print(f"  {result['name']}: Ready to mark")
```

**Why check?**

- Prevent double-marking the same student
- Students shouldn't get marked twice in one day

---

#### **STEP 8: Mark Attendance in Database**

```python
# File: services/attendance_service.py

attendance_records = []

for result in recognition_results:
    if not result.get('should_mark'):
        continue

    # Create attendance record
    record = {
        'student_id': result['student_id'],
        'student_name': result['name'],
        'date': datetime.now().date().isoformat(),
        'time': datetime.now().time().isoformat(),
        'method': 'facial_recognition',
        'confidence': result['confidence'],
        'camera_id': 'CAMERA_001',
        'status': 'present',
        'liveness_score': liveness_score
    }

    # Save to database
    db.insert_attendance(record)

    attendance_records.append(record)

    print(f"✓ Attendance marked for {result['name']}")

print(f"\n✓ {len(attendance_records)} students marked present")
```

**Attendance database:**

```
attendance table:
┌──────────┬────────────┬──────────┬────────┬────────────┐
│student_id│student_name│   date   │  time  │  status    │
├──────────┼────────────┼──────────┼────────┼────────────┤
│  S12345  │Ahmed Hassan│2024-05-05│08:00:15│  present   │
│  S12346  │ Fatima Ali │2024-05-05│08:00:23│  present   │
│  S12347  │ Omar Khan  │2024-05-05│08:02:34│  present   │
└──────────┴────────────┴──────────┴────────┴────────────┘
```

---

#### **STEP 9: Display Real-Time Feedback**

```python
# File: core/attendance_engine.py

# Draw bounding boxes and names on frame
output_frame = frame.copy()

for result in recognition_results:
    face = faces_detected[result['person_index'] - 1]

    x1, y1, x2, y2 = face.bbox

    if result['is_recognized']:
        # Green box for recognized
        color = (0, 255, 0)  # Green
        label = f"{result['name']} ({result['confidence']*100:.0f}%)"
    else:
        # Red box for unknown
        color = (0, 0, 255)  # Red
        label = "UNKNOWN"

    # Draw rectangle
    cv2.rectangle(output_frame, (x1, y1), (x2, y2), color, 2)

    # Draw label
    cv2.putText(output_frame, label, (x1, y1-10),
                cv2.FONT_HERSHEY_SIMPLEX, 0.5, color, 2)

# Display on screen
cv2.imshow('Attendance', output_frame)

# Output:
# ┌─────────────────────────────────────────┐
# │ [GREEN] Ahmed Hassan (95%)  [GREEN] Fatima Ali (87%) │
# │                                         │
# │ [RED] UNKNOWN (3%)                      │
# │                                         │
# └─────────────────────────────────────────┘
```

---

#### ✅ **ATTENDANCE MARKED!**

```
✓ 3 faces detected
✓ Faces aligned
✓ Liveness verified (3 real people)
✓ 2 faces recognized (Ahmed, Fatima)
✓ 1 unknown person detected
✓ Attendance marked for 2 students
✓ Database updated

Attendance complete for today!
```

---

---

## 📊 WORKFLOW 3: GENERATING ATTENDANCE REPORT

### Goal

Create daily and weekly attendance reports showing who attended and who were absent.

### Step-by-Step Process

#### **STEP 1: Load Attendance Data**

```python
# File: services/report_service.py

from datetime import datetime, timedelta

# Get all students
all_students = db.load_all_enrollments()
total_students = len(all_students)

# Get date range (e.g., last 7 days)
today = datetime.now().date()
date_range_start = today - timedelta(days=6)
date_range_end = today

# Load attendance records for this range
attendance_records = db.load_attendance(
    date_from=date_range_start,
    date_to=date_range_end
)

print(f"Total students: {total_students}")
print(f"Date range: {date_range_start} to {date_range_end}")
print(f"Attendance records found: {len(attendance_records)}")
```

---

#### **STEP 2: Build Attendance Matrix**

```python
# File: services/report_service.py

# Create matrix: Students × Days
import pandas as pd

dates = pd.date_range(start=date_range_start, end=date_range_end).date

attendance_matrix = pd.DataFrame(
    index=[s['student_id'] for s in all_students],
    columns=dates
)

# Fill with "Absent" by default
attendance_matrix = attendance_matrix.fillna('Absent')

# Mark "Present" where attendance recorded
for record in attendance_records:
    date = pd.to_datetime(record['date']).date()
    student_id = record['student_id']

    if student_id in attendance_matrix.index:
        attendance_matrix.loc[student_id, date] = 'Present'

print("\nAttendance Matrix (Sample):")
print(attendance_matrix.head())

# Output:
#          2024-04-29  2024-04-30  2024-05-01  ...  2024-05-05
# S12345     Present      Present     Absent    ...    Present
# S12346     Present      Absent      Present   ...    Present
# S12347     Absent       Present      Present   ...    Present
```

---

#### **STEP 3: Calculate Attendance Statistics**

```python
# File: services/report_service.py

statistics = []

for student_id in attendance_matrix.index:
    student = db.get_student(student_id)

    attendance_row = attendance_matrix.loc[student_id]

    present_days = (attendance_row == 'Present').sum()
    absent_days = (attendance_row == 'Absent').sum()
    total_days = len(attendance_row)

    attendance_percentage = (present_days / total_days) * 100 if total_days > 0 else 0

    statistics.append({
        'student_id': student_id,
        'name': student['name'],
        'grade': student['grade'],
        'present_days': present_days,
        'absent_days': absent_days,
        'total_days': total_days,
        'attendance_percentage': attendance_percentage,
        'status': 'Good' if attendance_percentage >= 75 else 'Warning' if attendance_percentage >= 50 else 'Poor'
    })

# Sort by attendance percentage
statistics.sort(key=lambda x: x['attendance_percentage'], reverse=True)

print("\nAttendance Statistics:")
print("ID     | Name          | Present | Absent | Total | %      | Status")
print("-------|---------------|---------|--------|-------|--------|--------")

for stat in statistics[:10]:  # Show top 10
    print(f"{stat['student_id']:6}| {stat['name']:13}| {stat['present_days']:7} | {stat['absent_days']:6} | {stat['total_days']:5} | {stat['attendance_percentage']:6.1f}% | {stat['status']}")

# Output:
# ID     | Name          | Present | Absent | Total | %      | Status
# -------|---------------|---------|--------|-------|--------|--------
# S12345 | Ahmed Hassan  |       7 |      0 |     7 | 100.0% | Good
# S12346 | Fatima Ali    |       6 |      1 |     7 |  85.7% | Good
# S12347 | Omar Khan     |       6 |      1 |     7 |  85.7% | Good
# S12348 | Zainab Ali    |       5 |      2 |     7 |  71.4% | Warning
# S12349 | Hassan Ahmed  |       3 |      4 |     7 |  42.9% | Poor
```

---

#### **STEP 4: Generate Visualizations**

```python
# File: services/report_service.py

import matplotlib.pyplot as plt

# Chart 1: Overall Attendance Distribution
fig, axes = plt.subplots(2, 2, figsize=(14, 10))

# Plot 1: Attendance Percentage Distribution
ax1 = axes[0, 0]
attendance_percentages = [s['attendance_percentage'] for s in statistics]
ax1.hist(attendance_percentages, bins=10, edgecolor='black', color='skyblue')
ax1.set_xlabel('Attendance Percentage')
ax1.set_ylabel('Number of Students')
ax1.set_title('Distribution of Student Attendance')
ax1.axvline(x=75, color='red', linestyle='--', label='Good threshold (75%)')
ax1.legend()

# Plot 2: Attendance Status Pie Chart
ax2 = axes[0, 1]
status_counts = {}
for stat in statistics:
    status = stat['status']
    status_counts[status] = status_counts.get(status, 0) + 1

ax2.pie(status_counts.values(), labels=status_counts.keys(), autopct='%1.1f%%')
ax2.set_title('Student Status Distribution')

# Plot 3: Top 10 Students
ax3 = axes[1, 0]
top_10 = statistics[:10]
names = [s['name'][:10] for s in top_10]
percentages = [s['attendance_percentage'] for s in top_10]
ax3.barh(names, percentages, color='green')
ax3.set_xlabel('Attendance %')
ax3.set_title('Top 10 Students (by attendance)')
ax3.set_xlim([0, 105])

# Plot 4: Daily Attendance Trend
ax4 = axes[1, 1]
daily_present = []
for date in attendance_matrix.columns:
    present_count = (attendance_matrix[date] == 'Present').sum()
    daily_present.append(present_count)

ax4.plot(attendance_matrix.columns, daily_present, marker='o', color='blue')
ax4.set_xlabel('Date')
ax4.set_ylabel('Students Present')
ax4.set_title('Daily Attendance Trend')
ax4.grid(True, alpha=0.3)
plt.xticks(rotation=45)

plt.tight_layout()
plt.savefig('output/attendance_report.png', dpi=300, bbox_inches='tight')
print("✓ Visualizations saved to attendance_report.png")
```

---

#### **STEP 5: Generate PDF Report**

```python
# File: services/report_service.py

from reportlab.lib.pagesizes import letter, A4
from reportlab.platypus import SimpleDocTemplate, Table, Paragraph, Spacer, PageBreak
from reportlab.lib.styles import getSampleStyleSheet

# Create PDF
pdf_file = f"output/attendance_report_{today}.pdf"
doc = SimpleDocTemplate(pdf_file, pagesize=A4)
story = []

# Title
styles = getSampleStyleSheet()
title = Paragraph(f"<b>Attendance Report - {today}</b>", styles['Heading1'])
story.append(title)
story.append(Spacer(1, 0.3*inch))

# Summary Statistics
summary_text = f"""
Total Students: {len(statistics)}<br/>
Period: {date_range_start} to {date_range_end}<br/>
Students with Good Attendance (≥75%): {sum(1 for s in statistics if s['attendance_percentage'] >= 75)}<br/>
Students with Warning Status (50-75%): {sum(1 for s in statistics if 50 <= s['attendance_percentage'] < 75)}<br/>
Students with Poor Attendance (<50%): {sum(1 for s in statistics if s['attendance_percentage'] < 50)}
"""
story.append(Paragraph(summary_text, styles['Normal']))
story.append(Spacer(1, 0.3*inch))

# Attendance Table
table_data = [['ID', 'Name', 'Grade', 'Present', 'Absent', 'Total', '%', 'Status']]

for stat in statistics:
    table_data.append([
        stat['student_id'],
        stat['name'],
        str(stat['grade']),
        str(stat['present_days']),
        str(stat['absent_days']),
        str(stat['total_days']),
        f"{stat['attendance_percentage']:.1f}%",
        stat['status']
    ])

table = Table(table_data)
story.append(table)

# Build PDF
doc.build(story)

print(f"✓ PDF report generated: {pdf_file}")
```

---

#### **STEP 6: Generate Alerts for Low Attendance**

```python
# File: services/report_service.py

alerts = []

for stat in statistics:
    if stat['attendance_percentage'] < 75:
        alerts.append({
            'student_id': stat['student_id'],
            'name': stat['name'],
            'attendance': stat['attendance_percentage'],
            'action': 'Email to parent' if stat['attendance_percentage'] < 50 else 'Warning email'
        })

print("\n⚠️ Attendance Alerts:")
print("ID     | Name          | Attendance | Action")
print("-------|---------------|------------|------------------")

for alert in alerts:
    print(f"{alert['student_id']:6}| {alert['name']:13}| {alert['attendance']:6.1f}%   | {alert['action']}")

# Send emails
for alert in alerts:
    if alert['attendance'] < 50:
        send_urgent_email(alert['student_id'], "Low Attendance Alert")
    else:
        send_warning_email(alert['student_id'], "Attendance Warning")

print(f"\n✓ {len(alerts)} alert emails sent")
```

---

#### ✅ **REPORT GENERATED!**

```
✓ Data loaded (500 students, 7 days)
✓ Attendance matrix built
✓ Statistics calculated
✓ Visualizations generated
✓ PDF report created
✓ Alerts sent for low attendance

Reports ready:
  - PDF: attendance_report_2024-05-05.pdf
  - Charts: attendance_report.png
  - Alerts: 45 students
```

---

---

## 🔄 Complete Recognition Flow Summary

```
Student at Camera
    ↓
┌──────────────────────────────┐
│ 1. Detect Faces              │
│    (MTCNN/RetinaFace)        │
└──────────────────────────────┘
    ↓
┌──────────────────────────────┐
│ 2. Align Faces               │
│    (Straighten, crop)        │
└──────────────────────────────┘
    ↓
┌──────────────────────────────┐
│ 3. Check Liveness            │
│    (Real vs Spoof)           │
└──────────────────────────────┘
    ↓
┌──────────────────────────────┐
│ 4. Generate Encodings        │
│    (128-dimensional vectors) │
└──────────────────────────────┘
    ↓
┌──────────────────────────────┐
│ 5. Compare with Database     │
│    (Find best match)         │
└──────────────────────────────┘
    ↓
┌──────────────────────────────┐
│ 6. Verify Not Already Marked │
│    (Check today's attendance)│
└──────────────────────────────┘
    ↓
┌──────────────────────────────┐
│ 7. Mark Attendance           │
│    (Store in database)       │
└──────────────────────────────┘
    ↓
✓ Student Attendance Marked
    ↓
Update Daily Dashboard
```
