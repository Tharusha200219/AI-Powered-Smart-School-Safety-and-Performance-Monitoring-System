# 🎯 How to Access Seating Arrangements

## ✅ Setup Complete - API is Running!

The seating arrangement system is now **fully integrated** and accessible from your Laravel application.

---

## 📍 **For Administrators & Teachers**

### 1. Access the Seating Dashboard

Navigate to your Laravel application and look in the sidebar under **"Academic Operations"**:

```
Academic Operations
  ├── Assignments
  ├── Grades
  ├── Timetable Viewer
  └── 🪑 Seating Arrangements  ← Click here!
```

Or directly access: **http://localhost:8000/admin/seating**

### 2. Generate Seating for a Class

On the seating dashboard, you'll see cards for each grade-section combination:

```
┌─────────────────────────┐
│  Grade 11-A             │
│  Grade 11, Section A    │
│                         │
│  [Generate]  [View]     │
└─────────────────────────┘
```

**Click "Generate"** to create a new seating arrangement based on student marks.

### 3. View Seating Layout

After generating, click **"View"** to see:

- Visual classroom grid
- Color-coded student performance
- Seat numbers and positions
- Student names and marks

---

## 📍 **For Students**

### Access Your Seat Assignment

In the sidebar under **"Academic Operations"**:

```
Academic Operations
  ├── My Seat  ← Click here!
  └── Performance Predictions
```

Or directly access: **http://localhost:8000/admin/seating/my-seat**

You'll see:

- Your seat number (e.g., S15)
- Row and column position
- Classroom layout with your seat highlighted
- Your performance level

---

## 🎨 What You'll See

### Admin Dashboard

```
┌──────────────────────────────────────────────────────┐
│          Generate Seating Arrangements                │
├──────────────────────────────────────────────────────┤
│                                                       │
│  ┌───────────────┐  ┌───────────────┐  ┌──────────┐ │
│  │  Grade 11-A   │  │  Grade 11-B   │  │ Grade 11-C│ │
│  │               │  │               │  │           │ │
│  │ [Generate]    │  │ [Generate]    │  │[Generate] │ │
│  │ [View]        │  │ [View]        │  │[View]     │ │
│  └───────────────┘  └───────────────┘  └──────────┘ │
│                                                       │
└──────────────────────────────────────────────────────┘
```

### Seating Layout View

```
═══════════════════════════════════════
         FRONT OF CLASSROOM
───────────────────────────────────────
Row 1:  [S1-H]  [S2-L]  [S3-H]  [S4-L]
        Alice   Frank   Bob     Emma
        92%     41%     88%     50%

Row 2:  [S5-M]  [S6-M]  [S7-M]  [S8-M]
        Charlie David   Grace   Henry
        78%     65%     72%     68%
───────────────────────────────────────
         BACK OF CLASSROOM
═══════════════════════════════════════

Legend:
🟢 H = High Performer (75%+)
🔵 M = Medium Performer (50-75%)
🟡 L = Low Performer (<50%)
```

### Student View

```
┌────────────────────────────────┐
│    Your Seat Assignment        │
├────────────────────────────────┤
│                                │
│  Seat Number:    S15           │
│  Row:            3             │
│  Column:         5             │
│  Grade:          11-A          │
│  Performance:    🟢 High       │
│                                │
│  [View Classroom Layout]       │
│                                │
└────────────────────────────────┘
```

---

## 🔧 Troubleshooting

### "Seating Arrangements" not showing in sidebar?

**Solution:** Clear your browser cache and refresh:

```bash
# Clear Laravel cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Then refresh your browser with Ctrl+Shift+R (or Cmd+Shift+R on Mac)
```

### "Unable to connect to seating service"?

**Solution:** Make sure the API is running:

```bash
# Check if API is running
curl http://localhost:5001/health

# If not running, start it
cd "student seating arrangement model"
./start_api.sh
```

### No classes showing up?

**Solution:** Make sure you have:

1. Students in the database with `grade_level` and `section` filled
2. Students marked as active (`is_active = 1`)
3. Marks entered for students in the recent term

---

## ⚡ Quick Commands

### Start the seating API:

```bash
cd "student seating arrangement model"
./start_api.sh
```

### Start Laravel server:

```bash
cd "AI-Powered-Smart-School-Safety-and-Performance-Monitoring-System"
php artisan serve
```

### Clear all caches:

```bash
php artisan config:clear && php artisan cache:clear && php artisan view:clear
```

---

## 🎯 Quick Access URLs

When Laravel is running on `http://localhost:8000`:

- **Admin Dashboard:** http://localhost:8000/admin/seating
- **Student Seat View:** http://localhost:8000/admin/seating/my-seat
- **API Health:** http://localhost:5001/health

---

## 📊 How It Works

1. **Admin clicks "Generate"** for a class
2. **Laravel fetches** all students in that grade-section
3. **Calculates** average marks from most recent term
4. **Sends to Python API** for seating generation
5. **Algorithm pairs** high performers with low performers
6. **Returns arrangement** with seat assignments
7. **Displays** beautiful visual classroom layout

---

## ✅ System Status

- ✅ API Running: http://localhost:5001
- ✅ Sidebar Menu: Added "Seating Arrangements" (Admin/Teacher) and "My Seat" (Student)
- ✅ Routes: All routes configured and working
- ✅ Views: Dashboard, layout view, and student view created
- ✅ Cache: Cleared and ready

---

## 🎉 You're All Set!

The seating arrangement feature is now **fully accessible** from your Laravel application!

1. **Start Laravel** if not running: `php artisan serve`
2. **Login** as admin, teacher, or student
3. **Look for** "Seating Arrangements" or "My Seat" in the sidebar
4. **Click and use!**

**Need help?** Check [METHODOLOGY.md](METHODOLOGY.md) for detailed documentation.

---

**Last Updated:** January 2, 2026  
**Status:** ✅ Fully Integrated and Accessible
