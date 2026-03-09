# 🎓 AI-Powered Smart School Safety and Performance Monitoring System: Comprehensive Documentation

## 📑 1. Project Overview

The **AI-Powered Smart School Safety and Performance Monitoring System** is a cutting-edge educational ecosystem that integrates advanced artificial intelligence, machine learning, and IoT to automate school administration, ensure student safety, and optimize academic performance.

The system utilizes a **microservices architecture**, where specialized AI models act as independent services communicating with a central **Laravel-based Dashboard** through REST APIs.

---

## 🏗️ 2. System Architecture

The architecture is designed for scalability and reliability, separating heavy AI processing from the user interface.

- **Frontend/Orchestrator**: Laravel 11.x Web Application.
- **AI Services**: 6 Specialized Flask-based Python APIs.
- **Database**: MySQL 8.0+.
- **Hardware Integration**: ESP32-CAM (Video/Face), Arduino (RFID/NFC).
- **Communication Protocol**: RESTful API (HTTP/JSON), MQTT (Hardware).

---

## 📊 3. The Smart School Dashboard (Laravel)

The dashboard serves as the command center for school administrators, teachers, parents, and security staff.

### 🔑 Key Modules

1.  **User Management**: Role-based access control (RBAC) for Admins, Teachers, Students, Parents, and Security.
2.  **Attendance Hub**: Centralized view of attendance from Face Recognition, RFID, and manual entry.
3.  **Performance Analytics**: Visualizes student progress, predicts future scores, and identifies weak areas.
4.  **Security Center**: Real-time alerts from Audio and Video threat detection models.
5.  **Homework Management**: Automated creation and grading of assignments using NLP.
6.  **Seating Arrangement**: Dynamic AI-optimized classroom layouts.
7.  **Reports & Notifications**: Automated monthly performance reports sent to parents via Email/SMS.

---

## 🤖 4. AI Models & Services

### 4.1 Facial Recognition Attendance

- **Purpose**: Automated student attendance.
- **Technology**: MTCNN (Detection), FaceNet InceptionResnetV1 (Recognition).
- **Features**:
  - Captures 40 images per student for high-accuracy training.
  - Real-time 512-dimensional embedding matching.
  - Anti-spoofing/Liveness detection.
- **Port**: `5004`

### 4.2 Student Performance Prediction

- **Purpose**: Forecast academic outcomes and identify "at-risk" students.
- **Technology**: XGBoost, Scikit-learn (Random Forest, Gradient Boosting).
- **Features**:
  - Analyzes temporal trends (momentum/improvement).
  - Provides 95% confidence intervals for predictions.
- **Port**: `5002` (Configurable)

### 4.3 Audio-Based Threat Detection

- **Purpose**: Identify danger in classrooms via sound analysis.
- **Technology**: 1D CNN + Bidirectional LSTM.
- **Features**:
  - Detects screaming, glass breaking, and aggressive speech.
  - Multi-language support (English & Sinhala).
  - Privacy-first: Processes audio in memory, never records.
- **Port**: `5001` or `5002` (Service Port: `5001`)

### 4.4 Video-Based Threat & Object Detection

- **Purpose**: Detect physical violence and left-behind objects.
- **Technology**: YOLOv8 (Object Detection), SlowFast (Action Recognition), DeepSORT (Tracking).
- **Features**:
  - Recognizes fighting, pushing, and aggressive behavior.
  - Monitors classrooms after hours for forgotten items.
- **Port**: `5002` or `5003` (Service Port: `5003`)

### 4.5 Homework Management AI (NLP)

- **Purpose**: Automate the homework lifecycle.
- **Technology**: HuggingFace Transformers, Sentence-BERT.
- **Features**:
  - Automatic question generation (MCQ, Short, Descriptive) from lesson content.
  - AI-based subjective answer evaluation with semantic feedback.
- **Port**: `5001` or `5003`

### 4.6 Student Seating Optimizer

- **Purpose**: Optimal classroom positioning for learning.
- **Technology**: Constraint-based Optimization / Genetic Algorithms.
- **Features**:
  - Considers student performance levels and social dynamics.
  - Maximizes engagement while minimizing disruptions.
- **Port**: `5003`

---

## 🔌 5. Port Configuration Summary

Each service is configured via the Laravel `.env` file to communicate with specific ports.

| AI Service                 | Default Flask Port | Dashboard Config Variable        |
| :------------------------- | :----------------- | :------------------------------- |
| **Homework Management**    | `5001`             | `HOMEWORK_AI_BASE_URL`           |
| **Audio Threat Detection** | `5001`             | `AUDIO_THREAT_API_URL`           |
| **Performance Prediction** | `5002`             | `PERFORMANCE_PREDICTION_API_URL` |
| **Video Threat Detection** | `5003`             | `VIDEO_THREAT_API_URL`           |
| **Seating Arrangement**    | `5003`             | `SEATING_ARRANGEMENT_API_URL`    |
| **Face Recognition**       | `5004`             | `FACE_RECOGNITION_API_URL`       |

> [!WARNING]
> In the default development environment, some services share ports. Ensure you adjust the `PORT` in each `app.py` or `.env` if running multiple services simultaneously.

---

## 🛠️ 6. Core Technology Stack

- **Backend Framework**: Laravel 11 (PHP 8.2+).
- **AI Frameworks**: Python 3.10+, PyTorch, TensorFlow, Scikit-learn.
- **Hardware**: Arduino Uno (RFID), ESP32-CAM (Video Surveillance).
- **Database**: MySQL (Primary), SQLite (AI Service Local Cache).

## 📊 7. Database Logic

The system uses a relational database to link AI outputs to real-world entities:

- `students`: Central repository for student profiles and face data links.
- `homework_submissions`: Stores AI-evaluated scores and feedback.
- `attendance`: Unified logs from all capture sources (Face, RFID, Manual).
- `threat_alerts`: Real-time logs of safety incidents detected by AI.

---

## 🚀 8. Getting Started

1.  **Dashboard**: Run `composer install`, `npm install`, and `php artisan migrate`.
2.  **AI Services**: Create virtual environments for each module and run `pip install -r requirements.txt`.
3.  **Integration**: Ensure the `.env` in the Dashboard points to the correct local IP and ports of the Flask services.
4.  **Hardware**: Use the provided Arduino sketches in the `/arduino` folder to set up RFID/Camera modules.

---