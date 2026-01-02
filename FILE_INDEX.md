# Complete File Index

## Python ML System

### Core Source Files

- `src/data_preprocessing.py` - Data cleaning and preparation
- `src/model_trainer.py` - ML model training with Linear Regression
- `src/predictor.py` - Real-time prediction engine

### API Server

- `api/app.py` - Flask REST API server
- `api/requirements.txt` - API-specific dependencies

### Configuration

- `config/config.py` - Centralized configuration settings

### Documentation

- `README.md` - Project overview and quick start guide
- `SETUP.md` - Detailed setup instructions
- `QUICK_REFERENCE.md` - Quick command reference
- `PROJECT_SUMMARY.md` - Complete project summary
- `LARAVEL_INTEGRATION.md` - Sidebar and view integration guide
- `docs/METHODOLOGY.md` - Full technical documentation (65+ pages)

### Setup and Configuration

- `setup.sh` - Automated setup script (executable)
- `.env.example` - Environment configuration template
- `.gitignore` - Git ignore patterns
- `requirements.txt` - Python dependencies

### Data (Generated)

- `data/cleaned_data.csv` - Processed training data (created by setup)
- `dataset/student_performance_updated_1000 (1).csv` - Original raw dataset

### Models (Generated)

- `models/performance_predictor.pkl` - Trained Linear Regression model
- `models/scaler.pkl` - Feature scaler
- `models/label_encoder.pkl` - Subject name encoder

## Laravel Integration Files

### Backend Services

- `app/Services/PerformancePredictionService.php` - API communication service
- `app/Http/Controllers/PerformancePredictionController.php` - Route controller

### Configuration

- `config/services.php` - Added prediction API configuration
- `routes/web.php` - Added prediction routes

### Views

- `resources/views/student/predictions.blade.php` - Full prediction page for students
- `resources/views/components/performance-prediction-widget.blade.php` - Reusable widget

### Environment

- `.env` - Add `PREDICTION_API_URL=http://localhost:5000`

## File Count Summary

| Category        | Files  | Description          |
| --------------- | ------ | -------------------- |
| Python Source   | 3      | Core ML logic        |
| API             | 2      | REST API server      |
| Laravel Backend | 2      | Service + Controller |
| Laravel Views   | 2      | UI components        |
| Configuration   | 5      | Config files         |
| Documentation   | 7      | Guides and docs      |
| Setup           | 3      | Setup scripts        |
| Generated       | 5      | Models and data      |
| **Total**       | **29** | **Complete system**  |

## Directory Structure

```
student-performance-prediction-model/
├── 📄 README.md                          [Project overview]
├── 📄 SETUP.md                           [Setup guide]
├── 📄 QUICK_REFERENCE.md                 [Quick commands]
├── 📄 PROJECT_SUMMARY.md                 [Complete summary]
├── 📄 LARAVEL_INTEGRATION.md             [Integration guide]
├── 📄 requirements.txt                   [Python deps]
├── 📄 .gitignore                         [Git ignore]
├── 📄 .env.example                       [Env template]
├── 🔧 setup.sh                           [Setup script ⚡]
│
├── 📂 src/                               [Source code]
│   ├── data_preprocessing.py             [Data cleaning]
│   ├── model_trainer.py                  [Training]
│   └── predictor.py                      [Predictions]
│
├── 📂 api/                               [Flask API]
│   ├── app.py                            [API server]
│   └── requirements.txt                  [API deps]
│
├── 📂 config/                            [Configuration]
│   └── config.py                         [Settings]
│
├── 📂 docs/                              [Documentation]
│   └── METHODOLOGY.md                    [Technical docs]
│
├── 📂 data/                              [Generated data]
│   └── cleaned_data.csv                  [Processed]
│
├── 📂 models/                            [Trained models]
│   ├── performance_predictor.pkl         [ML model]
│   ├── scaler.pkl                        [Scaler]
│   └── label_encoder.pkl                 [Encoder]
│
└── 📂 dataset/                           [Raw data]
    └── student_performance_updated_1000 (1).csv

Laravel (AI-Powered-Smart-School-Safety-and-Performance-Monitoring-System/):
├── 📂 app/
│   ├── Services/
│   │   └── PerformancePredictionService.php
│   └── Http/Controllers/
│       └── PerformancePredictionController.php
│
├── 📂 config/
│   └── services.php                      [Updated]
│
├── 📂 routes/
│   └── web.php                           [Updated]
│
└── 📂 resources/views/
    ├── student/
    │   └── predictions.blade.php
    └── components/
        └── performance-prediction-widget.blade.php
```

## File Relationships

```
Data Flow:
dataset/*.csv → data_preprocessing.py → data/cleaned_data.csv
                                      ↓
data/cleaned_data.csv → model_trainer.py → models/*.pkl
                                         ↓
models/*.pkl → predictor.py → Flask API (app.py)
                                         ↓
                    Laravel Service → Controller → Views
```

## Key Files by Task

### Initial Setup

1. `setup.sh` - Run this first
2. `requirements.txt` - Dependencies
3. `.env.example` - Configuration template

### Development

1. `src/data_preprocessing.py` - Modify data cleaning
2. `src/model_trainer.py` - Adjust ML algorithm
3. `src/predictor.py` - Change prediction logic
4. `api/app.py` - Modify API endpoints

### Integration

1. `app/Services/PerformancePredictionService.php` - Laravel service
2. `app/Http/Controllers/PerformancePredictionController.php` - Routes
3. `resources/views/components/performance-prediction-widget.blade.php` - UI

### Documentation

1. `README.md` - Start here
2. `QUICK_REFERENCE.md` - Commands
3. `docs/METHODOLOGY.md` - Technical details
4. `LARAVEL_INTEGRATION.md` - UI integration

## Configuration Files

| File                  | Purpose          | When to Edit        |
| --------------------- | ---------------- | ------------------- |
| `config/config.py`    | Python settings  | Change ports, paths |
| `config/services.php` | Laravel services | Add API URL         |
| `.env` (Laravel)      | Environment      | API connection      |
| `api/app.py`          | API config       | CORS, debug mode    |

## Generated Files (Don't Edit)

These files are created automatically:

- `data/cleaned_data.csv`
- `models/performance_predictor.pkl`
- `models/scaler.pkl`
- `models/label_encoder.pkl`

To regenerate, run:

```bash
python src/data_preprocessing.py
python src/model_trainer.py
```

## Documentation Hierarchy

```
Entry Point: README.md
    ├── Quick Start → QUICK_REFERENCE.md
    ├── Setup → SETUP.md
    ├── Technical → docs/METHODOLOGY.md
    ├── Integration → LARAVEL_INTEGRATION.md
    └── Summary → PROJECT_SUMMARY.md
```

## File Sizes (Approximate)

| File Type     | Size           | Count |
| ------------- | -------------- | ----- |
| Python source | ~10-20 KB each | 3     |
| API           | ~8 KB          | 1     |
| Laravel PHP   | ~5-10 KB each  | 2     |
| Views         | ~5-8 KB each   | 2     |
| Docs          | ~50-100 KB     | 7     |
| Dataset       | ~100 KB        | 1     |
| Models        | ~10-50 KB      | 3     |

## Backup Priority

**Critical (Backup Always):**

1. `src/` - Source code
2. `api/app.py` - API server
3. Laravel integration files
4. Documentation

**Important (Backup Regularly):**

1. `dataset/` - Raw data
2. `config/` - Configuration

**Generated (Can Recreate):**

1. `models/` - Retrain if lost
2. `data/` - Reprocess if lost

## Quick Access

**Most Used:**

- `api/app.py` - Start API server
- `QUICK_REFERENCE.md` - Commands
- `PerformancePredictionService.php` - Laravel service

**For Debugging:**

- API logs (console output)
- Laravel logs (`storage/logs/laravel.log`)
- Browser console (F12)

**For Updates:**

- `src/model_trainer.py` - Retrain model
- `config/config.py` - Change settings
- Views - Modify UI

---

## Navigation Tips

1. **New to project?** Start with `README.md`
2. **Setting up?** Follow `SETUP.md`
3. **Need quick help?** Check `QUICK_REFERENCE.md`
4. **Technical details?** Read `docs/METHODOLOGY.md`
5. **Adding to sidebar?** See `LARAVEL_INTEGRATION.md`
6. **Complete overview?** View `PROJECT_SUMMARY.md`

All files are well-commented and organized by function!
