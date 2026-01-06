# 🎓 AI-Powered Smart School Safety and Performance Monitoring System

<div align="center">

![Status](https://img.shields.io/badge/status-active-success.svg)
![Python](https://img.shields.io/badge/python-3.8+-blue.svg)
![PHP](https://img.shields.io/badge/php-8.2+-777BB4.svg)
![Laravel](https://img.shields.io/badge/laravel-11.x-FF2D20.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)

An integrated AI-powered solution designed to enhance school safety, optimize educational outcomes, and automate administrative tasks through cutting-edge machine learning technologies.

[Key Features](#-key-features) • [Architecture](#-system-architecture) • [Installation](#-installation) • [Components](#-system-components) • [Documentation](#-documentation)

</div>

---

## 📋 Table of Contents

- [Project Overview](#-project-overview)
- [Key Features](#-key-features)
- [System Architecture](#-system-architecture)
- [System Components](#-system-components)
- [Technology Stack](#-technology-stack)
- [Project Dependencies](#-project-dependencies)
- [Installation](#-installation)
- [Quick Start](#-quick-start)
- [API Endpoints](#-api-endpoints)
- [Hardware Requirements](#-hardware-requirements)
- [Documentation](#-documentation)
- [Contributing](#-contributing)
- [License](#-license)

---

## 🎯 Project Overview

The **AI-Powered Smart School Safety and Performance Monitoring System** is a comprehensive, multi-component platform that leverages artificial intelligence, machine learning, and IoT technologies to create a safer and more efficient educational environment. The system integrates six specialized modules working together through a centralized dashboard to provide:

### Safety Monitoring

- **Real-time Audio Threat Detection** - Identifies dangerous sounds (screaming, glass breaking, aggressive speech)
- **Video-Based Threat Detection** - Detects violent behavior, fighting, or aggressive actions
- **Left-Behind Object Detection** - Monitors classrooms for forgotten items after hours

### Performance Optimization

- **Student Performance Prediction** - Forecasts academic outcomes based on attendance and marks
- **Intelligent Seating Arrangement** - Optimizes classroom seating for better learning outcomes
- **Homework Management System** - Automates homework generation, distribution, and evaluation

### Administrative Excellence

- **Centralized Dashboard** - Laravel-based web interface for system-wide management
- **Real-time Alerts** - Multi-channel notifications (Email, Telegram, SMS)
- **Analytics & Reporting** - Comprehensive performance and safety reports

---

## ✨ Key Features

### 🛡️ Safety & Security

- ✅ **Real-time audio analysis** for threat detection (screaming, glass breaking, threatening speech)
- ✅ **Video surveillance** with AI-powered action recognition
- ✅ **Multi-language support** (English & Sinhala) for speech analysis
- ✅ **Privacy-first design** - no audio/video storage, memory-only processing
- ✅ **ESP32-CAM integration** for low-cost IoT camera deployment
- ✅ **Schedule-aware monitoring** based on school timetables
- ✅ **Instant alert system** with configurable notification channels

### 📊 Performance & Analytics

- ✅ **Predictive analytics** for student performance across all subjects
- ✅ **Automated homework generation** using NLP and AI
- ✅ **Intelligent answer evaluation** for both MCQ and descriptive questions
- ✅ **ML-based seating optimization** considering student relationships and performance
- ✅ **Real-time dashboards** with comprehensive visualization
- ✅ **Monthly performance reports** for teachers and parents

### 🎓 Academic Management

- ✅ **Multi-subject support** (Science, Mathematics, History, English, Health Science)
- ✅ **Question generation** from lesson content
- ✅ **Automatic grading system** with feedback generation
- ✅ **Performance tracking** and trend analysis
- ✅ **Teacher workload reduction** through automation

---

## 🏗️ System Architecture

The system follows a **microservices architecture** with specialized AI/ML modules communicating with a central Laravel dashboard through REST APIs.

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                           PRESENTATION LAYER                                    │
│                                                                                 │
│  ┌────────────────────────────────────────────────────────────────────────┐   │
│  │            Laravel Dashboard (Web Interface)                            │   │
│  │  • Admin Portal   • Teacher Portal   • Student Portal   • Security     │   │
│  │  • Analytics      • Reports          • Alerts          • Settings      │   │
│  └────────────────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────────────────┘
                                      │
                                      │ REST API / WebSocket
                                      ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│                         APPLICATION LAYER (Backend)                             │
│                                                                                 │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐               │
│  │   User Mgmt     │  │   Homework      │  │   Performance   │               │
│  │   • Auth        │  │   • Generation  │  │   • Tracking    │               │
│  │   • Roles       │  │   • Grading     │  │   • Analytics   │               │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘               │
│                                                                                 │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐               │
│  │   Alert Mgmt    │  │   Schedule      │  │   Camera Mgmt   │               │
│  │   • Email       │  │   • Timetable   │  │   • Config      │               │
│  │   • SMS         │  │   • Class Time  │  │   • Zones       │               │
│  │   • Telegram    │  └─────────────────┘  └─────────────────┘               │
│  └─────────────────┘                                                           │
└─────────────────────────────────────────────────────────────────────────────────┘
                                      │
                        ┌─────────────┼──────────────┐
                        │             │              │
                        ▼             ▼              ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│                           AI/ML PROCESSING LAYER                                │
│                                                                                 │
│  ┌─────────────────────────────────────────────────────────────────────────┐  │
│  │  Audio Threat Detection (Flask API)                                     │  │
│  │  • Non-speech threat detection (1D CNN + BiLSTM)                        │  │
│  │  • Speech-to-text conversion (English/Sinhala)                          │  │
│  │  • Keyword-based threat analysis                                        │  │
│  │  • Adaptive noise calibration                                           │  │
│  │  Port: 5001                                                             │  │
│  └─────────────────────────────────────────────────────────────────────────┘  │
│                                                                                 │
│  ┌─────────────────────────────────────────────────────────────────────────┐  │
│  │  Video Threat & Object Detection (Flask API)                            │  │
│  │  • Object detection (YOLOv8)                                            │  │
│  │  • Action recognition (SlowFast)                                    │  │
│  │  • Object tracking (DeepSORT)                                           │  │
│  │  • Temporal analysis with configurable thresholds                       │  │
│  │  Port: 5002                                                             │  │
│  └─────────────────────────────────────────────────────────────────────────┘  │
│                                                                                 │
│  ┌─────────────────────────────────────────────────────────────────────────┐  │
│  │  Homework Management AI (Flask API)                                     │  │
│  │  • Question generation (NLP + Transformers)                             │  │
│  │  • Answer evaluation (Sentence similarity)                              │  │
│  │  • Automated feedback generation                                        │  │
│  │  • Multi-subject support                                                │  │
│  │  Port: 5003                                                             │  │
│  └─────────────────────────────────────────────────────────────────────────┘  │
│                                                                                 │
│  ┌─────────────────────────────────────────────────────────────────────────┐  │
│  │  Student Performance Prediction (Flask API)                             │  │
│  │  • ML-based performance forecasting                                     │  │
│  │  • Subject-wise prediction models                                       │  │
│  │  • Attendance & marks correlation analysis                              │  │
│  │  Port: 5004                                                             │  │
│  └─────────────────────────────────────────────────────────────────────────┘  │
│                                                                                 │
│  ┌─────────────────────────────────────────────────────────────────────────┐  │
│  │  Seating Arrangement Optimizer (Flask API)                              │  │
│  │  • Constraint-based optimization                                        │  │
│  │  • Student relationship analysis                                        │  │
│  │  • Performance-based grouping                                           │  │
│  │  Port: 5005                                                             │  │
│  └─────────────────────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────────────────────┘
                                      │
                                      │ MQTT / HTTP
                                      ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│                           HARDWARE LAYER (IoT)                                  │
│                                                                                 │
│                      ┌──────────────┐  ┌──────────────┐                         │
│                      │  ESP32-CAM   │  │  ESP32-CAM   │                         │
│                      │  Classroom   │  │  Library     │                         │
│                      └──────────────┘  └──────────────┘                         │
│                                                                                 │
│  ┌──────────────────────────────────────────────────────────────────────────┐   │
│  │                    Microphones (Web Audio API)                           │   │
│  │         Browser-based audio capture in monitored areas                   │   │
│  └──────────────────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────────────────┘
                                      │
                                      ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│                       NOTIFICATION LAYER                                        │
│                                                                                 │
│  📧 Email Alerts    │    📱 Telegram Bot    │    💬 SMS Gateway                │
│                                                                                 │
│  Principal, Teachers, Security Staff, Parents                                  │
└─────────────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────────────┐
│                           DATA LAYER                                            │
│                                                                                 │
│  ┌──────────────────────────────────────────────────────────────────────────┐ │
│  │  MySQL Database (Laravel)                                                 │ │
│  │  • Users, Students, Teachers, Classes, Schedules                         │ │
│  │  • Homework, Submissions, Grades, Performance Records                    │ │
│  │  • Alert Logs, Camera Configurations, System Settings                    │ │
│  └──────────────────────────────────────────────────────────────────────────┘ │
│                                                                                 │
│  ┌──────────────────────────────────────────────────────────────────────────┐ │
│  │  File Storage                                                             │ │
│  │  • ML Model Checkpoints (.pt, .pkl files)                                │ │
│  │  • Student Datasets (CSV files)                                          │ │
│  │  • Homework Documents, Performance Reports                               │ │
│  └──────────────────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────────────┘
```

### Architecture Highlights

- **Microservices Design**: Each AI/ML component runs independently as a Flask API
- **RESTful Communication**: Standardized API contracts between components
- **Privacy-First Processing**: Audio/video processed in memory, never stored
- **Real-time Updates**: WebSocket support for live alerts and monitoring
- **Scalable Infrastructure**: Components can be scaled independently
- **IoT Integration**: ESP32-CAM devices for cost-effective video surveillance
- **Multi-channel Alerts**: Configurable notification delivery across platforms

---

## 🧩 System Components

### 1. 📊 Smart School Dashboard (Laravel)

**Location**: `Smart-School-Safety-and-Performance-Monitoring-System Dashboard/`

The central administrative hub providing:

- User management (Admin, Teachers, Students, Security Staff)
- Real-time alert monitoring and management
- Performance analytics and reporting
- System configuration and settings
- Schedule and timetable management
- Integration with all AI/ML modules

**Technology**: Laravel 11.x, PHP 8.2+, MySQL, Blade Templates, Spatie Permissions

---

### 2. 🎧 Audio-Based Threat Detection

**Location**: `AudioBasedThreatDetection-Models/`

Real-time audio intelligence for safety monitoring:

- **Non-speech detection**: Identifies screaming, crying, shouting, glass breaking
- **Speech analysis**: Converts speech to text and analyzes for threats
- **Multi-language**: Supports English and Sinhala
- **Noise calibration**: Adapts to environmental noise levels
- **Privacy-focused**: No audio recording or storage

**Models**: 1D CNN + Bidirectional LSTM for audio classification

**API Port**: 5001

---

### 3. 📹 Video-Based Threat & Object Detection

**Location**: `Video_Based_Left_Behind_Object_and_Threat_Detection-main/`

Dual-purpose video surveillance system:

#### Left-Behind Object Detection

- Monitors classrooms according to schedules
- Identifies forgotten items (bags, books, bottles)
- Triggers alerts 1 hour after last class ends
- Temporal tracking with configurable thresholds

#### Threat Detection

- Real-time action recognition
- Detects fighting, pushing, aggressive behavior
- Immediate alerts to principal/teachers
- Multi-camera support with zone configuration

**Models**: YOLOv8 (object detection), SlowFast (threa detection), DeepSORT (multi-object tracking)

**API Port**: 5002

**Hardware**: ESP32-CAM IoT cameras

---

### 4. 📝 Homework Management & Performance Monitoring

**Location**: `Homework-Management-and-Performance-Monitoring-System/`

Automated homework workflow:

- **Question generation**: AI-generated questions from lesson content
- **Multi-subject support**: Science, Math, History, English, Health Science
- **Automatic grading**: Instant MCQ grading + NLP-based subjective evaluation
- **Feedback generation**: Personalized feedback for students
- **Performance analytics**: Real-time dashboards and monthly reports

**Models**: Transformers, Sentence-BERT, NLP processors

**API Port**: 5003

---

### 5. 📈 Student Performance Prediction

**Location**: `student-performance-prediction-model/`

ML-based performance forecasting:

- Predicts student performance for each subject
- Analyzes attendance and marks correlation
- Provides trend analysis (improving/declining)
- Confidence scores for predictions
- Early intervention recommendations

**Models**: Regression models (Random Forest, Gradient Boosting, Linear Regression)

**API Port**: 5004

---

### 6. 🪑 Student Seating Arrangement Optimizer

**Location**: `student-seating-arrangement-model/`

Intelligent classroom seating optimization:

- Considers student relationships and performance
- Maximizes learning outcomes
- Minimizes disruptions
- Constraint-based optimization
- Teacher preference integration

**API Port**: 5005

---

## 🛠️ Technology Stack

### Backend & AI/ML

| Component              | Technology                  | Version |
| ---------------------- | --------------------------- | ------- |
| **Web Framework**      | Flask                       | 3.0.0+  |
| **Deep Learning**      | PyTorch                     | 2.0.0+  |
| **Computer Vision**    | YOLOv8 (Ultralytics)        | 8.0.0+  |
| **NLP**                | Transformers, NLTK          | 4.36.0+ |
| **Audio Processing**   | Torchaudio, SoundFile       | 2.0.0+  |
| **Video Processing**   | OpenCV, MoviePy             | 4.8.0+  |
| **ML Libraries**       | Scikit-learn, NumPy, Pandas | 1.3.0+  |
| **Action Recognition** | PyTorchVideo                | 0.1.5+  |

### Dashboard & Frontend

| Component          | Technology                 | Version |
| ------------------ | -------------------------- | ------- |
| **Web Framework**  | Laravel                    | 11.x    |
| **Language**       | PHP                        | 8.2+    |
| **Database**       | MySQL                      | 8.0+    |
| **Frontend**       | Blade Templates, Bootstrap | -       |
| **Data Tables**    | Yajra DataTables           | 11.0+   |
| **Authentication** | Laravel UI                 | 4.6+    |
| **Permissions**    | Spatie Laravel Permission  | 6.21+   |

### IoT Hardware

| Component         | Specification                 |
| ----------------- | ----------------------------- |
| **Cameras**       | ESP32-CAM modules             |
| **Communication** | WiFi, MQTT                    |
| **Audio**         | Web Audio API (browser-based) |

### Deployment & Infrastructure

- **OS**: Linux (Ubuntu 20.04+), macOS, Windows
- **Python**: 3.8 - 3.14
- **Web Server**: Apache/Nginx
- **Process Manager**: PM2, Supervisor
- **GPU**: CUDA-capable (recommended for real-time processing)

---

## 📦 Project Dependencies

### Core Python Dependencies

#### Audio Threat Detection

```
torch>=2.0.0
torchaudio>=2.0.0
numpy>=1.24.0
scipy>=1.11.0
soundfile>=0.12.0
pydub>=0.25.0
SpeechRecognition>=3.10.0
flask>=3.0.0
flask-cors>=4.0.0
scikit-learn>=1.3.0
```

#### Video Threat Detection

```
torch>=2.0.0
torchvision>=0.15.0
tensorflow>=2.13.0
ultralytics>=8.0.0
opencv-python>=4.8.0
pytorchvideo>=0.1.5
filterpy>=1.4.5
moviepy>=1.0.3
paho-mqtt>=1.6.1
```

#### Homework Management

```
transformers>=4.36.0
torch>=2.0.0
sentence-transformers>=2.2.2
nltk>=3.8.1
scikit-learn>=1.3.0
flask>=3.0.0
huggingface-hub>=0.19.0
```

#### Performance Prediction & Seating

```
flask>=3.0.0
scikit-learn>=1.3.0
numpy>=1.26.0
pandas>=2.0.0
joblib>=1.3.0
```

### Dashboard Dependencies (Laravel)

```json
{
  "require": {
    "php": "^8.2",
    "laravel/framework": "^11.0",
    "laravel/ui": "^4.6",
    "spatie/laravel-permission": "^6.21",
    "yajra/laravel-datatables": "^11.0"
  }
}
```

---

## 🚀 Installation

### Prerequisites

- **Python 3.8+** (3.10 recommended)
- **PHP 8.2+**
- **Composer** (for Laravel)
- **Node.js 18+** (for asset compilation)
- **MySQL 8.0+**
- **Git**
- **CUDA-capable GPU** (optional, but recommended for real-time video processing)

### System Requirements

| Component   | Minimum | Recommended           |
| ----------- | ------- | --------------------- |
| **CPU**     | 4 cores | 8+ cores              |
| **RAM**     | 8 GB    | 16+ GB                |
| **Storage** | 50 GB   | 100+ GB SSD           |
| **GPU**     | -       | NVIDIA with 4GB+ VRAM |

---

## 🏁 Quick Start

### 1. Clone the Repository

```bash
git clone <repository-url>
cd AI-Powered-Smart-School-Safety-and-Performance-Monitoring-System
```

### 2. Set Up the Dashboard (Laravel)

```bash
cd "Smart-School-Safety-and-Performance-Monitoring-System Dashboard"

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env file
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=school_system
# DB_USERNAME=root
# DB_PASSWORD=

# Run migrations and seeders
php artisan migrate --seed

# Build assets
npm run build

# Start the server
php artisan serve
```

The dashboard will be available at `http://localhost:8000`

### 3. Set Up Audio Threat Detection

```bash
cd AudioBasedThreatDetection-Models

# Create virtual environment
python -m venv venv
source venv/bin/activate  # On Windows: venv\Scripts\activate

# Install dependencies
pip install -r requirements.txt

# Start the API
python app.py
```

API will run on `http://localhost:5001`

### 4. Set Up Video Threat Detection

```bash
cd Video_Based_Left_Behind_Object_and_Threat_Detection-main

# Create virtual environment
python -m venv venv
source venv/bin/activate

# Install dependencies
pip install -r requirements.txt

# Download YOLOv8 weights (automatic on first run)
# Configure cameras in config/camera_config.json

# Start the API
python app.py
```

API will run on `http://localhost:5002`

### 5. Set Up Homework Management

```bash
cd Homework-Management-and-Performance-Monitoring-System

# Create virtual environment
python -m venv venv
source venv/bin/activate

# Install dependencies
pip install -r requirements.txt

# Download NLTK data
python -c "import nltk; nltk.download('punkt'); nltk.download('stopwords')"

# Start the API
python app.py
```

API will run on `http://localhost:5003`

### 6. Set Up Performance Prediction

```bash
cd student-performance-prediction-model

# Create virtual environment
python -m venv venv
source venv/bin/activate

# Install dependencies
pip install -r requirements.txt

# Train initial models
python src/model_trainer.py

# Start the API
cd api
python app.py
```

API will run on `http://localhost:5004`

### 7. Set Up Seating Arrangement

```bash
cd student-seating-arrangement-model

# Create virtual environment
python -m venv venv
source venv/bin/activate

# Install dependencies
pip install -r requirements.txt

# Start the API
cd api
python app.py
```

API will run on `http://localhost:5005`

### 8. Start All Services (Automated)

Use the provided shell scripts to start all APIs:

```bash
# Start all APIs
./start_both_apis.sh

# Stop all APIs
./stop_both_apis.sh
```

---

## 🔌 API Endpoints

### Audio Threat Detection API (Port 5001)

```http
POST /api/detect
Content-Type: application/json

{
  "audio_data": "base64_encoded_audio",
  "sample_rate": 16000
}

Response:
{
  "threat_detected": true,
  "threat_type": "screaming",
  "confidence": 0.92,
  "timestamp": "2026-01-05T10:30:45Z"
}
```

### Video Threat Detection API (Port 5002)

```http
POST /api/analyze-frame
Content-Type: application/json

{
  "camera_id": "classroom_1A",
  "frame": "base64_encoded_image"
}

Response:
{
  "threats": [
    {
      "type": "fighting",
      "confidence": 0.88,
      "location": [120, 340, 250, 480]
    }
  ],
  "objects": [
    {
      "class": "backpack",
      "confidence": 0.95,
      "duration": 3720
    }
  ]
}
```

### Homework Management API (Port 5003)

```http
POST /api/generate-questions
Content-Type: application/json

{
  "subject": "Science",
  "topic": "Photosynthesis",
  "lesson_content": "Plants use sunlight...",
  "question_count": 5
}

Response:
{
  "questions": [
    {
      "type": "mcq",
      "question": "What is photosynthesis?",
      "options": ["A", "B", "C", "D"],
      "correct_answer": "C"
    }
  ]
}
```

### Performance Prediction API (Port 5004)

```http
POST /api/predict
Content-Type: application/json

{
  "student_id": 123,
  "subjects": [
    {
      "subject_name": "Mathematics",
      "attendance": 85.5,
      "marks": 78.0
    }
  ]
}

Response:
{
  "predictions": [
    {
      "subject": "Mathematics",
      "predicted_performance": 82.5,
      "trend": "improving",
      "confidence": 0.89
    }
  ]
}
```

### Seating Arrangement API (Port 5005)

```http
POST /api/optimize
Content-Type: application/json

{
  "class_id": "10A",
  "students": [...],
  "constraints": {
    "separate_friends": true,
    "group_by_performance": false
  }
}

Response:
{
  "arrangement": [
    {"row": 1, "seat": 1, "student_id": 101},
    {"row": 1, "seat": 2, "student_id": 102}
  ],
  "optimization_score": 0.87
}
```

---

## 💻 Hardware Requirements

### Server Requirements

| Use Case                         | CPU       | RAM   | GPU       | Storage    |
| -------------------------------- | --------- | ----- | --------- | ---------- |
| **Development**                  | 4 cores   | 8 GB  | Optional  | 50 GB      |
| **Small School (<500 students)** | 8 cores   | 16 GB | GTX 1060  | 100 GB SSD |
| **Medium School (500-1500)**     | 16 cores  | 32 GB | RTX 3060  | 250 GB SSD |
| **Large School (>1500)**         | 32+ cores | 64 GB | RTX 3080+ | 500 GB SSD |

### IoT Hardware

| Item                                         | Price(LKR)|       
| -------------------------------------------- | --------- | 
| **ESP32 OV2640 Camera and Development bord** | 2,880.00  | 
| **2 SD Card 64GB and MicroSD Card Module**   | 3140.00   | 
| **Arduino UNO R3 and UNO+WiFi R3**           | 4,110.00  | 
| **RFID Card Reader/Writer RC522 x 2**        | 800.00    | 


#### ESP32-CAM Specifications

- **Processor**: ESP32-S (dual-core 160MHz)
- **Camera**: OV2640 (2MP)
- **WiFi**: 802.11 b/g/n
- **Memory**: 520KB SRAM + 4MB PSRAM
  

#### Deployment Recommendations

- 1 camera per classroom
- 1 camera per hallway/corridor
- 1 camera for main entrance
- 1 camera for playground (optional)

**Estimated cost for 20-camera setup**: 100000LKR

---

## 📚 Documentation

Detailed documentation for each component:

- **Audio Threat Detection**: [AudioBasedThreatDetection-Models/README.md](AudioBasedThreatDetection-Models/README.md)
- **Video Threat Detection**: [Video_Based_Left_Behind_Object_and_Threat_Detection-main/README.md](Video_Based_Left_Behind_Object_and_Threat_Detection-main/README.md)
- **Homework Management**: [Homework-Management-and-Performance-Monitoring-System/DOCUMENTATION.md](Homework-Management-and-Performance-Monitoring-System/DOCUMENTATION.md)
- **Performance Prediction**: [student-performance-prediction-model/README.md](student-performance-prediction-model/README.md)
- **Seating Arrangement**: [student-seating-arrangement-model/README.md](student-seating-arrangement-model/README.md)
- **Dashboard Setup**: [Smart-School-Safety-and-Performance-Monitoring-System Dashboard/README.md](Smart-School-Safety-and-Performance-Monitoring-System Dashboard/README.md)

### Additional Resources

- **API Documentation**: See individual component README files
- **Model Training Guides**: Available in respective model directories
- **ESP32-CAM Setup**: [Video_Based_Left_Behind_Object_and_Threat_Detection-main/firmware/](Video_Based_Left_Behind_Object_and_Threat_Detection-main/firmware/)
- **Troubleshooting**: Check component-specific documentation

---

## 🔒 Privacy & Security

This system prioritizes user privacy:

- ✅ **No audio/video storage** - All processing done in memory
- ✅ **Minimal data retention** - Only metadata and alerts stored
- ✅ **Encrypted communication** - HTTPS/TLS for all API calls
- ✅ **Role-based access control** - Granular permissions system
- ✅ **Audit logging** - Complete activity tracking
- ✅ **GDPR compliant** - Designed with privacy regulations in mind
- ✅ **User consent** - Explicit permission required for monitoring

---

## 🧪 Testing

### Run Tests for Individual Components

```bash
# Audio Threat Detection
cd AudioBasedThreatDetection-Models
python -m pytest tests/

# Video Threat Detection
cd Video_Based_Left_Behind_Object_and_Threat_Detection-main
python test_system.py

# Performance Prediction
cd student-performance-prediction-model
python test_system.py

# Laravel Dashboard
cd "Smart-School-Safety-and-Performance-Monitoring-System Dashboard"
php artisan test
```

---

## 🤝 Contributing

We welcome contributions! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Development Guidelines

- Follow PEP 8 for Python code
- Use PSR-12 for PHP code
- Write unit tests for new features
- Update documentation
- Maintain backward compatibility

---

## 🐛 Troubleshooting

### Common Issues

#### Port Already in Use

```bash
# Find process using port
lsof -ti:5001 | xargs kill -9  # macOS/Linux
netstat -ano | findstr :5001   # Windows
```

#### CUDA/GPU Issues

```bash
# Verify CUDA installation
python -c "import torch; print(torch.cuda.is_available())"

# Force CPU mode (add to .env)
USE_GPU=false
```

#### Model Download Failures

```bash
# Manually download models
cd models/saved/
wget <model-url>
```

#### Database Connection Issues

```bash
# Check MySQL service
sudo systemctl status mysql  # Linux
brew services list | grep mysql  # macOS

# Reset migrations
php artisan migrate:fresh --seed
```

---

## 📊 Performance Benchmarks

| Component                  | Latency | Throughput  | GPU Usage |
| -------------------------- | ------- | ----------- | --------- |
| **Audio Detection**        | <100ms  | 30 fps      | N/A       |
| **Video Detection**        | <50ms   | 25 fps      | 60%       |
| **Homework Generation**    | 2-5s    | 10 req/min  | N/A       |
| **Performance Prediction** | <200ms  | 100 req/min | N/A       |

_Benchmarks on: Intel i7-10700K, 32GB RAM, RTX 3070_

---

## 📅 Roadmap

### Phase 1 - Core Features (Completed)

- ✅ Audio threat detection
- ✅ Video threat detection
- ✅ Homework management
- ✅ Performance prediction
- ✅ Dashboard integration

### Phase 2 - Enhancements (In Progress)

- 🔄 Mobile application
- 🔄 Advanced analytics dashboard
- 🔄 Parent portal
- 🔄 Integration with existing school systems

### Phase 3 - Advanced Features (Planned)

- 📋 Face recognition for attendance
- 📋 Emotion detection
- 📋 Predictive maintenance for equipment
- 📋 AI-powered tutoring system



## 🙏 Acknowledgments

- **YOLOv8** - Ultralytics for object detection
- **PyTorch** - Facebook AI Research
- **Laravel** - Taylor Otwell and the Laravel community
- **Transformers** - Hugging Face
- **OpenCV** - Open Source Computer Vision Library



<div align="center">

**Built with ❤️ for safer and smarter schools**

[⬆ Back to Top](#-ai-powered-smart-school-safety-and-performance-monitoring-system)

</div>
