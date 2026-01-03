# Quick Start Guide - Student Seating Arrangement System

## 🚀 Quick Setup (5 Minutes)

### Step 1: Install Python Dependencies

```bash
cd "student seating arrangement model"
pip install -r requirements.txt
```

### Step 2: Start the API

```bash
chmod +x start_api.sh
./start_api.sh
```

Or manually:

```bash
cd api
python app.py
```

### Step 3: Configure Laravel

Add to your `.env` file:

```env
SEATING_API_URL=http://localhost:5001
```

### Step 4: Clear Laravel Cache

```bash
cd "AI-Powered-Smart-School-Safety-and-Performance-Monitoring-System"
php artisan config:clear
php artisan cache:clear
```

### Step 5: Test

Visit: `http://your-laravel-app/admin/seating`

---

## 📖 How to Use

### Admin Usage

1. **Navigate to Seating Management**

   - URL: `/admin/seating`
   - You'll see all grade-section combinations

2. **Generate Seating Arrangement**

   - Click "Generate" button for any class
   - Wait a few seconds
   - Click "View" to see the arrangement

3. **View Arrangement**
   - See visual classroom layout
   - Students color-coded by performance:
     - 🟢 Green = High Performer (75%+)
     - 🔵 Blue = Medium Performer (50-75%)
     - 🟡 Yellow = Low Performer (<50%)

### Student Usage

1. **View Your Seat**
   - URL: `/admin/seating/my-seat`
   - See your seat number, row, and column
   - View classroom layout with your seat highlighted

---

## 🎯 How It Works

### The Algorithm

```
1. Get all students in a grade-section
2. Calculate average marks from most recent term
3. Sort students: Highest to Lowest marks
4. Pair alternately:
   - Seat 1: Highest performer
   - Seat 2: Lowest performer
   - Seat 3: 2nd highest
   - Seat 4: 2nd lowest
   - Continue...
5. Assign to grid positions (rows & columns)
```

### Example

Class of 6 students with marks:

- Alice: 90% (Highest)
- Bob: 85%
- Charlie: 70%
- David: 60%
- Emma: 50%
- Frank: 40% (Lowest)

**Seating Order:**

1. S1: Alice (90%) - High
2. S2: Frank (40%) - Low
3. S3: Bob (85%) - High
4. S4: Emma (50%) - Low
5. S5: Charlie (70%) - Medium
6. S6: David (60%) - Medium

**Grid (2 rows x 3 seats):**

```
Row 1: [Alice-90%] [Frank-40%] [Bob-85%]
Row 2: [Emma-50%]  [Charlie-70%] [David-60%]
```

---

## 🔧 Troubleshooting

### API Won't Start

```bash
# Check if port is in use
lsof -i :5001

# Try different port
export SEATING_API_PORT=5002
python api/app.py
```

### Laravel Can't Connect

```bash
# Test API directly
curl http://localhost:5001/health

# Should return:
# {"status": "healthy", ...}
```

### No Students Found

- Ensure students exist in database
- Check `grade_level` and `section` fields match
- Verify students have `is_active = 1`

### Students Have No Marks

- Enter marks in the marks table
- System uses most recent term marks
- Default is 50% if no marks found

---

## 📁 File Structure

```
student seating arrangement model/
├── api/
│   └── app.py              # Flask API (Port 5001)
├── src/
│   ├── seating_generator.py   # Core algorithm
│   └── utils.py               # Helper functions
├── config/
│   └── config.py              # Configuration
├── requirements.txt        # Python packages
├── start_api.sh           # Startup script
├── METHODOLOGY.md         # Full documentation
└── README.md             # Overview

Laravel Integration/
├── app/
│   ├── Services/
│   │   └── SeatingArrangementService.php  # Service layer
│   └── Http/Controllers/
│       └── SeatingArrangementController.php  # Controller
├── resources/views/
│   ├── admin/seating/
│   │   ├── index.blade.php   # Admin dashboard
│   │   └── show.blade.php    # View arrangement
│   └── student/
│       └── seating.blade.php # Student view
└── routes/
    └── web.php            # Routes added
```

---

## 🔑 Key Endpoints

### Python API

- `GET /health` - Health check
- `POST /generate-seating` - Generate arrangement
- `GET /student-seat?student_id=X` - Get student seat

### Laravel Routes

- `GET /admin/seating` - Admin dashboard
- `POST /admin/seating/generate` - Generate
- `GET /admin/seating/show/{grade}/{section}` - View
- `GET /admin/seating/my-seat` - Student view

---

## 💡 Tips

1. **Generate Once Per Term**: Create new arrangements at start of each term
2. **Cache is Smart**: Arrangements are cached for 60 minutes
3. **Regenerate Anytime**: Click "Generate" again to recreate
4. **Check Health**: Visit `/admin/seating/health` to verify API status
5. **Performance**: Works instantly for classes up to 50 students

---

## 🎓 Educational Benefits

- **Peer Learning**: High performers help low performers
- **Engagement**: Varied seating keeps students interested
- **Fairness**: No clustering or favoritism
- **Inclusive**: Everyone gets equal opportunity

---

## ⚙️ Configuration Options

Edit `config/config.py`:

```python
API_PORT = 5001              # Change API port
DEFAULT_SEATS_PER_ROW = 5    # Classroom layout
DEFAULT_ROWS = 6             # Number of rows
MAX_STUDENTS_PER_CLASS = 40  # Maximum capacity
```

Edit Laravel `.env`:

```env
SEATING_API_URL=http://localhost:5001
```

---

## 📞 Need Help?

1. Read [METHODOLOGY.md](METHODOLOGY.md) for detailed docs
2. Check troubleshooting section above
3. Review code comments in source files
4. Check Laravel logs: `storage/logs/laravel.log`
5. Check Python output in terminal

---

## ✅ Checklist

Before going live:

- [ ] Python API is running on port 5001
- [ ] Laravel can connect to API (check health endpoint)
- [ ] Students have marks entered in database
- [ ] Routes are accessible (check permissions)
- [ ] Views render correctly
- [ ] Cache is working
- [ ] Tested with real class data

---

## 🚦 Production Deployment

For production:

1. **Use Supervisor** to keep API running:

```ini
[program:seating-api]
command=/usr/bin/python3 /path/to/api/app.py
directory=/path/to/student seating arrangement model/api
autostart=true
autorestart=true
```

2. **Use Reverse Proxy** (Nginx):

```nginx
location /seating-api/ {
    proxy_pass http://127.0.0.1:5001/;
}
```

3. **Set Environment**:

```env
SEATING_API_DEBUG=False
```

4. **Enable Logging**:
   Configure file-based logging in Python

---

**You're all set! 🎉**

Start generating optimal seating arrangements for your school!
