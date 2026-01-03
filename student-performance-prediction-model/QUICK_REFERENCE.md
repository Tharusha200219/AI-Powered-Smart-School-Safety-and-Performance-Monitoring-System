# Quick Reference Guide

## 🚀 Quick Start Commands

```bash
# Setup (run once)
cd student-performance-prediction-model
pip install -r requirements.txt
python src/data_preprocessing.py
python src/model_trainer.py

# Start API Server
cd api && python app.py

# Test API
curl http://localhost:5000/health
```

## 📁 Important Files

| Purpose            | File Location                                                        |
| ------------------ | -------------------------------------------------------------------- |
| Data cleaning      | `src/data_preprocessing.py`                                          |
| Model training     | `src/model_trainer.py`                                               |
| Predictions        | `src/predictor.py`                                                   |
| API server         | `api/app.py`                                                         |
| Laravel service    | `app/Services/PerformancePredictionService.php`                      |
| Laravel controller | `app/Http/Controllers/PerformancePredictionController.php`           |
| Student view       | `resources/views/student/predictions.blade.php`                      |
| Widget component   | `resources/views/components/performance-prediction-widget.blade.php` |

## 🔧 Laravel Setup

```bash
# Add to .env
echo "PREDICTION_API_URL=http://localhost:5000" >> .env

# Clear cache
php artisan config:clear
php artisan cache:clear

# Check routes
php artisan route:list | grep predictions
```

## 📊 API Endpoints

| Method | Endpoint         | Purpose                   |
| ------ | ---------------- | ------------------------- |
| GET    | `/health`        | Check API status          |
| POST   | `/predict`       | Single student prediction |
| POST   | `/predict/batch` | Multiple students         |

## 🧪 Testing

```bash
# Test with curl
curl -X POST http://localhost:5000/predict \
  -H "Content-Type: application/json" \
  -d '{
    "student_id": 1,
    "age": 15,
    "grade": 10,
    "subjects": [
      {"subject_name": "Math", "attendance": 85, "marks": 75}
    ]
  }'
```

## 📚 Documentation Files

- `README.md` - Project overview
- `SETUP.md` - Setup instructions
- `docs/METHODOLOGY.md` - Technical details
- `PROJECT_SUMMARY.md` - Complete summary
- `LARAVEL_INTEGRATION.md` - Sidebar integration

## 🔍 Troubleshooting

| Issue            | Solution                                           |
| ---------------- | -------------------------------------------------- |
| Port 5000 busy   | Kill process: `lsof -ti:5000 \| xargs kill -9`     |
| Module not found | `pip install -r requirements.txt`                  |
| No predictions   | Check API is running: `curl localhost:5000/health` |
| Laravel errors   | `php artisan config:clear`                         |

## 📈 Model Information

- **Algorithm:** Linear Regression
- **Features:** age, grade, attendance, marks, subject
- **Target:** future_performance (0-100)
- **Metrics:** MAE, RMSE, R²

## 🎯 URLs (After Setup)

- API Health: `http://localhost:5000/health`
- Student Predictions: `http://your-app/admin/predictions/my-predictions`
- Admin View: `http://your-app/admin/predictions/student/{id}`

## ⚡ Quick Commands

```bash
# Retrain model with new data
python src/data_preprocessing.py && python src/model_trainer.py

# Start API in background
nohup python api/app.py > api.log 2>&1 &

# Check API logs
tail -f api.log

# Kill API process
pkill -f "python api/app.py"
```

## 🔐 Environment Variables

```env
# Laravel .env
PREDICTION_API_URL=http://localhost:5000
```

## 📱 Access Points

**For Students:**

- Dashboard widget (if added)
- Sidebar → "Performance Predictions"
- `/admin/predictions/my-predictions`

**For Teachers/Admin:**

- Student detail page
- `/admin/predictions/student/{id}`

## 🎨 Widget Usage

```blade
{{-- In any blade file --}}
@php
    $service = app(\App\Services\PerformancePredictionService::class);
    $predictions = $service->predictStudentPerformance($student);
@endphp

<x-performance-prediction-widget :predictions="$predictions" />
```

## 📊 Data Format

**Input:**

```json
{
  "student_id": 123,
  "age": 15,
  "grade": 10,
  "subjects": [{ "subject_name": "Math", "attendance": 85, "marks": 75 }]
}
```

**Output:**

```json
{
  "predictions": [
    {
      "subject": "Math",
      "current_performance": 75.0,
      "predicted_performance": 78.5,
      "prediction_trend": "improving",
      "confidence": 0.89
    }
  ]
}
```

## 🛠️ Maintenance

```bash
# Update dependencies
pip install --upgrade -r requirements.txt

# Backup models
cp -r models/ models_backup_$(date +%Y%m%d)/

# Clean old data
rm -f data/*.csv
```

## 💡 Tips

1. ✅ Start API before testing Laravel integration
2. ✅ Check API health endpoint first
3. ✅ Use real school data for better predictions
4. ✅ Retrain model monthly with new data
5. ✅ Cache predictions in Laravel (1 hour)
6. ✅ Monitor API logs for errors

## 🔗 Related Commands

```bash
# Python
python --version          # Check Python version
pip list                  # List installed packages
pip freeze > requirements.txt  # Save dependencies

# Laravel
php artisan config:cache  # Cache config
php artisan route:cache   # Cache routes
php artisan view:clear    # Clear view cache

# Server
netstat -tulpn | grep 5000  # Check port usage
ps aux | grep python        # Find Python processes
```

## 📞 Support Checklist

When reporting issues, provide:

- [ ] API health check response
- [ ] Error messages from logs
- [ ] Laravel version
- [ ] Python version
- [ ] Browser console errors (if UI issue)
- [ ] Request/response examples

## ✅ Validation Checklist

Before going live:

- [ ] API responds to health check
- [ ] Predictions work via curl
- [ ] Laravel can connect to API
- [ ] Sidebar link works
- [ ] Predictions display correctly
- [ ] No console errors
- [ ] Permissions set correctly
- [ ] Model trained with real data

---

**Remember:** Keep the API running for the system to work!

```bash
# Always have this running:
python api/app.py
```
