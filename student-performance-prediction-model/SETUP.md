# Student Performance Prediction System - Setup Guide

## Quick Setup (Automated)

### Linux/macOS:

```bash
chmod +x setup.sh
./setup.sh
```

This will automatically:

- Create a virtual environment
- Install all dependencies
- Preprocess data
- Train the model

### Windows:

```cmd
python -m venv venv
venv\Scripts\activate
pip install -r requirements.txt
python src/data_preprocessing.py
python src/model_trainer.py
python src/predictor.py
```

## Manual Setup

### 1. Create Virtual Environment (Required on macOS/Linux)

```bash
# Create virtual environment
python3 -m venv venv

# Activate virtual environment
source venv/bin/activate  # macOS/Linux
# OR
venv\Scripts\activate  # Windows
```

### 2. Install Dependencies

```bash
pip install -r requirements.txt
```

### 3. Preprocess Data

```bash
python src/data_preprocessing.py
```

This will:

- Load the raw dataset
- Clean and prepare the data
- Create subject-wise records
- Save cleaned data to `data/cleaned_data.csv`

### 4. Train Model

```bash
python src/model_trainer.py
```

This will:

- Load cleaned data
- Train Linear Regression model
- Evaluate model performance
- Save trained models to `models/` directory

Expected output:

```
Test R² Score: 0.85+
Test MAE: < 10.0
```

### 5. Test Predictions

```bash
python src/predictor.py
```

This verifies the model works correctly with sample data.

### 6. Start API Server

```bash
# Make sure virtual environment is activated
source venv/bin/activate  # macOS/Linux

cd api
python app.py
```

The API will be available at: `http://localhost:5000`

Test with:

```bash
curl http://localhost:5000/health
```

### 7. Configure Laravel

Add to `.env`:

```
PREDICTION_API_URL=http://localhost:5000
```

Clear cache:

```bash
php artisan config:clear
php artisan cache:clear
```

## Verification

### Test API:

```bash
curl -X POST http://localhost:5000/predict \
  -H "Content-Type: application/json" \
  -d '{
    "student_id": 1,
    "age": 15,
    "grade": 10,
    "subjects": [
      {"subject_name": "Mathematics", "attendance": 85, "marks": 75}
    ]
  }'
```

### Test Laravel Integration:

Visit: `http://your-app/admin/predictions/my-predictions`

## Troubleshooting

### Issue: Port 5000 already in use

```bash
# Find and kill process
lsof -ti:5000 | xargs kill -9

# Or use different port
# Edit config/config.py: API_PORT = 5001
```

### Issue: Module not found

```bash
pip install --upgrade -r requirements.txt
```

### Issue: Permission denied (setup.sh)

```bash
chmod +x setup.sh
```

## Production Deployment

### Using systemd (Linux):

Create `/etc/systemd/system/prediction-api.service`:

```ini
[Unit]
Description=Student Performance Prediction API
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/path/to/student-performance-prediction-model/api
ExecStart=/usr/bin/python3 app.py
Restart=always

[Install]
WantedBy=multi-user.target
```

Enable and start:

```bash
sudo systemctl enable prediction-api
sudo systemctl start prediction-api
sudo systemctl status prediction-api
```

### Using Docker:

Create `Dockerfile`:

```dockerfile
FROM python:3.9-slim

WORKDIR /app
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

COPY . .

EXPOSE 5000
CMD ["python", "api/app.py"]
```

Build and run:

```bash
docker build -t prediction-api .
docker run -d -p 5000:5000 prediction-api
```

## Performance Tips

1. **Use gunicorn for production:**

```bash
pip install gunicorn
gunicorn -w 4 -b 0.0.0.0:5000 api.app:app
```

2. **Enable caching in Laravel:**
   Cache predictions for 1 hour to reduce API calls

3. **Monitor logs:**

```bash
tail -f /path/to/logs/prediction-api.log
```

## Next Steps

1. ✅ Setup complete
2. ✅ API running
3. ✅ Laravel integrated
4. → Add prediction link to sidebar
5. → Train with real school data
6. → Monitor and improve model accuracy

For detailed documentation, see: `docs/METHODOLOGY.md`
