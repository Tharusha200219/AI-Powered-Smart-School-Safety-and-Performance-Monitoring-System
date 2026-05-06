# Facial Recognition Attendance System - File Documentation

## 📁 Project Structure Overview

This module automatically tracks student attendance using facial recognition technology. It detects faces in video/images, recognizes who they are, and marks them present in the system.

---

## 📄 Root Level Files

### `requirements.txt`

- **What it does**: Lists Python packages needed
- **Key packages**:
  - OpenCV (video/image processing)
  - dlib (face detection)
  - face_recognition (facial comparison)
  - Flask (web API)
- **How to use**: Run `pip install -r requirements.txt`

### `app.py`

- **What it does**: Main entry point for the attendance system
- **Functions**:
  - Loads configuration
  - Initializes all components
  - Starts the Flask server
  - Handles camera feed or uploaded images
- **How to run**: `python app.py`


### `verify_blur.py`

- **What it does**: Tests if face images are blurry
- **Purpose**: Rejects low-quality images that won't recognize well
- **How to use**: `python verify_blur.py --image path/to/image.jpg`

### `verify_engine.py`

- **What it does**: Tests if the entire attendance engine works correctly
- **Tests**:
  - Can faces be detected?
  - Can faces be recognized?
  - Can attendance be marked?
- **How to use**: `python verify_engine.py`

### `Smart_School_Accuracy_Dashboard.ipynb`

- **What it does**: Jupyter notebook showing accuracy metrics and charts
- **Contains**:
  - Recognition accuracy statistics
  - False positive/negative rates
  - Performance graphs

### `model_accuracy_results.json`

- **What it does**: Stores accuracy metrics in JSON format
- **Contains**:
  - Recognition accuracy %
  - Processing speed (FPS)
  - Dataset statistics

### `README.md`

- **What it does**: User-friendly guide for the attendance system
- **Contains**: Setup instructions, usage examples, troubleshooting

---

## ⚙️ `/config` Folder - Settings

### `settings.py`

- **What it does**: Central configuration for the entire system
- **Key settings**:
  - Camera settings (resolution, FPS)
  - Face detection method (MTCNN, RetinaFace, MediaPipe)
  - Confidence thresholds (minimum similarity to match a face)
  - Database connection details
  - API port and host
  - Recognition model type (face_recognition, ArcFace, etc.)
- **Why important?**: Tune these to match your school's setup

### `__init__.py`

- **What it does**: Makes config a Python package

---

## 🧠 `/core` Folder - Core Recognition Logic

### `face_detector.py`

- **What it does**: Detects faces in images/video
- **Process**:
  1. Take video frame or image
  2. Find all faces in the frame
  3. Return bounding boxes (coordinates around faces)
  4. Return confidence scores
- **Methods available**:
  - MTCNN (most accurate, slower)
  - RetinaFace (balanced speed/accuracy)
  - MediaPipe (fastest, less accurate)
- **Output**: Face locations with confidence scores

### `face_recognizer.py`

- **What it does**: Compares detected faces with stored student photos
- **Process**:
  1. Take detected face
  2. Convert to face encoding (128-dimensional vector)
  3. Compare with all stored student encodings
  4. Find the best match
  5. Return student ID if confidence > threshold
- **Confidence threshold**: Usually 0.6 (60% match needed)
- **Output**: Student ID, name, and confidence score

### `face_aligner.py`

- **What it does**: Straightens face images for better recognition
- **Why?**: Faces at angles get misrecognized
- **Process**:
  1. Detect facial landmarks (eyes, nose, mouth)
  2. Calculate rotation angle
  3. Rotate image to align face
  4. Crop to face region only
- **Output**: Aligned face image ready for recognition

### `anti_spoof.py`

- **What it does**: Detects if someone is trying to trick the system
- **Prevents**: Someone using a photo, screen, or mask to spoof attendance
- **Detection methods**:
  - Liveness detection (checks for eye blink, head movement)
  - Texture analysis (photos have different texture than real faces)
  - Depth analysis (uses 3D info if available)
- **Output**: Is-live score (how sure we are it's a real person)

### `attendance_engine.py`

- **What it does**: Main orchestrator that coordinates all components
- **Process**:
  1. Get video frame from camera
  2. Detect faces using face_detector.py
  3. Align faces using face_aligner.py
  4. Check if face is real using anti_spoof.py
  5. Recognize face using face_recognizer.py
  6. Check database if already marked today
  7. Mark attendance if new and confidence is high
  8. Log the action
- **Output**: Attendance record with timestamp, student info, confidence

### `__init__.py`

- **What it does**: Makes core a Python package

---

## 🌐 `/api` Folder - REST API

### `/api/routes/`

- **What it contains**: API endpoint definitions
- **Typical endpoints**:
  - `/attendance/mark`: Mark attendance for a student
  - `/attendance/report`: Get daily attendance report
  - `/attendance/history`: Get student's attendance history
  - `/recognize`: Send image and get recognized student
  - `/enroll`: Add new student face to system
  - `/health`: Check API status

### `__init__.py`

- Makes api folder a Python package

---

## 💾 `/database` Folder

- **What it contains**: Database connection code
- **Connects to**: Laravel database to store attendance records
- **Stores**:
  - Student photos/face encodings
  - Daily attendance records
  - Logs and errors

---

## 📚 `/services` Folder

- **What it contains**: Business logic services
- **Examples**:
  - AttendanceService: Handle attendance marking
  - TemplateService: Store/retrieve face templates
  - NotificationService: Send alerts

---

## 🎓 `/training` Folder

- **What it contains**: Code to train/improve the recognition model
- **Tasks**:
  - Add new student photos
  - Fine-tune recognition thresholds
  - Test with new data

---

## 🛠️ `/utils` Folder

- **What it contains**: Helper functions
- **Examples**:
  - Image preprocessing (resize, normalize)
  - File operations (read/write images)
  - Logging functions
  - Database helpers

---

## 📊 `/data` Folder

- **What it contains**: Training and test datasets
- **Contains**:
  - Student photos
  - Face encodings (pre-computed)
  - Metadata (student ID, name, etc.)

---

## 📈 `/logs` Folder

- **What it contains**: System logs
- **Logged information**:
  - Who recognized students
  - Confidence scores
  - Errors and warnings
  - System performance

---

## 🧪 `/tests` Folder

- **What it contains**: Automated tests
- **Tests**:
  - Face detection accuracy
  - Recognition accuracy
  - Liveness detection
  - API endpoints

---

## 🔄 How Files Work Together

```
Camera Feed / Image Upload
    ↓
face_detector.py (find faces)
    ↓
face_aligner.py (straighten faces)
    ↓
anti_spoof.py (check if real person)
    ↓
face_recognizer.py (identify student)
    ↓
attendance_engine.py (coordinate & log)
    ↓
Database (store attendance)
    ↓
API Response (send result to Laravel)
    ↓
Frontend (display to admin)
```

---

## 🎯 Key Concepts

### Face Encoding

- Each face is converted to a 128-dimensional number
- Similar faces have similar numbers
- Comparison is just calculating distance between numbers

### Confidence Score

- How sure the system is about the recognition
- Scale: 0.0 (different person) to 1.0 (same person)
- Usually need > 0.6 to mark attendance

### Liveness Detection

- Ensures it's a real person, not a photo
- Checks for eye blinks, head movement
- Prevents spoofing attacks

### Anti-Spoofing

- Detects if someone is trying to cheat
- Methods:
  - Photo/screen detection
  - Mask detection
  - 3D liveness (if camera supports)

---

## ⚡ Quick Start

1. **Setup**: `pip install -r requirements.txt`
2. **Enroll students**: Run enrollment script with student photos
3. **Start system**: `python app.py`
4. **Test**: `python verify_engine.py`
5. **Check attendance**: Query database or use API

---

## 🔧 Configuration Tips

Edit `/config/settings.py`:

```python
# Detection method (faster vs more accurate)
FACE_DETECTION_METHOD = 'retinaface'  # or 'mtcnn', 'mediapipe'

# Confidence threshold (lower = more recognitions but more false positives)
RECOGNITION_THRESHOLD = 0.6

# Liveness confidence (how strict to be on spoofing)
LIVENESS_THRESHOLD = 0.8

# Camera resolution (higher = slower but more accurate)
CAMERA_WIDTH = 1280
CAMERA_HEIGHT = 720
```

---

## 📊 Output Format

Recognition result returns JSON like:

```json
{
  "success": true,
  "student_id": "S12345",
  "student_name": "Ahmed Hassan",
  "confidence": 0.95,
  "is_live": true,
  "liveness_score": 0.98,
  "timestamp": "2024-05-05T08:30:15Z",
  "attendance_marked": true
}
```

---

## ⚠️ Important Notes

1. **Privacy**: Store face data securely, comply with data protection laws
2. **Accuracy**: Lighting, angle, and image quality affect accuracy
3. **Enrollment**: Enroll students with multiple photos in different lighting
4. **Maintenance**: Periodically retrain with new photos for better accuracy
5. **Backup**: Always have manual attendance as backup
