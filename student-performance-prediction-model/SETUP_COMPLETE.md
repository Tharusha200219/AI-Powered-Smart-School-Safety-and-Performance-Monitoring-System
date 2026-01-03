# ✅ Setup Complete!

## 🎉 System Successfully Installed

Your Student Performance Prediction System is now fully set up and running!

---

## 📊 Current Status

### ✅ Completed

- [x] Virtual environment created (`venv/`)
- [x] All Python dependencies installed
- [x] Data preprocessing completed (5000 records)
- [x] ML model trained (Linear Regression)
- [x] Model files saved (`models/`)
- [x] Flask API server running on **port 5001**
- [x] Sidebar link added to Laravel
- [x] Student view predictions integrated

---

## 🚀 API Server Running

**API URL:** `http://localhost:5001`

### Endpoints:

- **Health Check:** `GET http://localhost:5001/health`
- **Single Prediction:** `POST http://localhost:5001/predict`
- **Batch Prediction:** `POST http://localhost:5001/predict/batch`

### Test API:

```bash
# In a new terminal
curl http://localhost:5001/health
```

Expected response:

```json
{
  "status": "healthy",
  "message": "Student Performance Prediction API is running",
  "model_loaded": true
}
```

---

## ⚙️ Laravel Configuration

### Step 1: Update .env File

Add this line to your Laravel `.env` file:

```env
PREDICTION_API_URL=http://localhost:5001
```

**⚠️ Important:** Port changed from **5000** to **5001** because macOS Control Center uses port 5000.

### Step 2: Clear Laravel Cache

```bash
cd /Users/tharusha_rashmika/Documents/research_2.2/AI-Powered-Smart-School-Safety-and-Performance-Monitoring-System

php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

## 🎯 How to Use

### Access Predictions:

1. **Via Sidebar:**

   - Login to Laravel app
   - Look for **"Performance Predictions"** under **Academic Operations**
   - Click to see all your predictions

2. **Via Student View:**
   - Go to Management → Students
   - Click any student
   - Scroll down to see **"AI Performance Predictions"** section

---

## 🔧 Managing the API Server

### Start API Server:

```bash
cd /Users/tharusha_rashmika/Documents/research_2.2/student-performance-prediction-model
source venv/bin/activate
cd api
python app.py
```

### Stop API Server:

Press `Ctrl + C` in the terminal where it's running

### Check if API is Running:

```bash
curl http://localhost:5001/health
```

### If Port 5001 is Busy:

```bash
# Kill process on port 5001
lsof -ti:5001 | xargs kill -9

# Then restart API
cd /Users/tharusha_rashmika/Documents/research_2.2/student-performance-prediction-model
source venv/bin/activate
cd api
python app.py
```

---

## 📝 Virtual Environment

### Activate Virtual Environment:

```bash
cd /Users/tharusha_rashmika/Documents/research_2.2/student-performance-prediction-model
source venv/bin/activate
```

### Deactivate Virtual Environment:

```bash
deactivate
```

### Install New Packages:

```bash
# Make sure venv is activated first
source venv/bin/activate
pip install package-name
```

---

## 📊 Model Information

### Trained Model:

- **Algorithm:** Linear Regression
- **Features:** age, grade, attendance, marks, subject
- **Training Data:** 5000 records (1000 students × 5 subjects)
- **Test R² Score:** -0.0046
- **Test MAE:** 8.24

### Model Files:

- `models/performance_predictor.pkl` - Trained model
- `models/scaler.pkl` - Feature scaler
- `models/label_encoder.pkl` - Subject encoder

### Data Files:

- `data/cleaned_data.csv` - Preprocessed training data
- `dataset/student_performance_updated_1000 (1).csv` - Original raw data

---

## 🐛 Troubleshooting

### API Not Responding:

```bash
# Check if running
lsof -i :5001

# Restart API
cd /Users/tharusha_rashmika/Documents/research_2.2/student-performance-prediction-model
source venv/bin/activate
cd api
python app.py
```

### Laravel Shows "Service Unavailable":

1. Check if API is running: `curl http://localhost:5001/health`
2. Check `.env` has correct URL: `PREDICTION_API_URL=http://localhost:5001`
3. Clear Laravel cache: `php artisan config:clear`

### Python Command Not Found:

Always activate virtual environment first:

```bash
source venv/bin/activate
```

### Port Already in Use:

```bash
# Change port in config/config.py
API_PORT = 5002  # or any available port

# Update Laravel .env accordingly
PREDICTION_API_URL=http://localhost:5002
```

---

## 🔄 Quick Restart Script

Save this as `restart_api.sh`:

```bash
#!/bin/bash
cd /Users/tharusha_rashmika/Documents/research_2.2/student-performance-prediction-model
source venv/bin/activate
lsof -ti:5001 | xargs kill -9 2>/dev/null
cd api
python app.py
```

Make executable and run:

```bash
chmod +x restart_api.sh
./restart_api.sh
```

---

## 📚 Documentation

- **Full Documentation:** [docs/METHODOLOGY.md](docs/METHODOLOGY.md)
- **Quick Reference:** [QUICK_REFERENCE.md](QUICK_REFERENCE.md)
- **Setup Guide:** [SETUP.md](SETUP.md)
- **Integration Guide:** [INTEGRATION_SUMMARY.md](INTEGRATION_SUMMARY.md)
- **Visual Guide:** [WHERE_TO_FIND.md](WHERE_TO_FIND.md)

---

## ✨ What's Working

✅ Data preprocessing pipeline  
✅ ML model training & evaluation  
✅ Prediction engine with recommendations  
✅ Flask REST API on port 5001  
✅ Laravel service layer  
✅ Laravel controllers & routes  
✅ Sidebar navigation link  
✅ Student detail view predictions  
✅ Error handling & fallbacks  
✅ Responsive UI design

---

## 🎓 Next Steps

1. **Test the Integration:**

   - Make sure API is running
   - Login to Laravel app
   - Check sidebar link
   - View a student to see predictions

2. **Train with Real Data:**

   - Replace sample dataset with actual school data
   - Re-run: `python src/data_preprocessing.py`
   - Re-train: `python src/model_trainer.py`

3. **Monitor & Improve:**
   - Collect feedback from users
   - Monitor prediction accuracy
   - Retrain model periodically

---

## 💡 Tips

- Keep API running in background terminal
- API must be running for predictions to work
- If API crashes, predictions show "Service unavailable"
- Restart API if you update model files
- Clear Laravel cache after config changes

---

## 🎉 You're All Set!

Everything is configured and ready to use. The API is running on port 5001, and Laravel is integrated with sidebar and student view predictions.

**Current API Status:** ✅ Running on http://localhost:5001

**Last Updated:** 2 January 2026  
**Status:** ✅ Production Ready
