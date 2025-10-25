# 📚 Attendance System Quick Start Guide

## 🎯 Access the System

### Via Sidebar Menu

1. Log in to the admin panel
2. Look for **Management** section in the left sidebar
3. Click on **Attendance** (📋 fact_check icon)
4. You'll land on the **Attendance Dashboard**

---

## 📊 Dashboard Overview

### What You See

-   **4 Statistics Cards** at the top:

    -   🟢 Present (green)
    -   🔴 Absent (red)
    -   🟡 Late (yellow)
    -   🔵 Total Students (blue)

-   **Recent Check-Ins Table** below showing:
    -   Student name and code
    -   Class and grade
    -   Status badge
    -   Check-in/check-out times
    -   Recording method (NFC/Manual)

### Auto-Refresh

-   Dashboard refreshes automatically every 30 seconds
-   Manual refresh button available

---

## 🎫 NFC Attendance (Automatic)

### How It Works

1. Arduino must be connected and running
2. Student approaches NFC reader
3. Student taps their NFC wristband
4. **First Tap**: Records check-in
    - Green notification appears
    - Status set to "present"
    - Late flag if after 8:00 AM
5. **Second Tap**: Records check-out
    - Blue notification appears
    - Duration calculated
    - Session closed

### What Students See

```
🟢 Check-In Success!
John Doe
Time: 08:15 AM
Status: Late (after 8:00 AM)
```

```
🔵 Check-Out Success!
John Doe
Check-in: 08:15 AM
Check-out: 02:30 PM
Duration: 6 hours 15 minutes
```

---

## ✍️ Manual Attendance Entry

### Access

-   Click **"Manual Entry"** button on dashboard
-   Or go to: Attendance → Manual Entry

### Step-by-Step Process

#### Step 1: Find Student

1. Enter student code in the search box
    - Example: `STU2024001`
2. Press Enter or click "Search Student"
3. Student information appears:
    - Full name
    - Student code
    - Class and grade
    - Today's attendance status (if any)

#### Step 2: Record Attendance

1. **Select Attendance Type**:

    - ☑️ **Check In** - Student arrived
    - ☑️ **Check Out** - Student left
    - ☑️ **Mark Absent** - Student didn't come

2. **Set Date** (optional):

    - Defaults to today
    - Can select past dates for corrections

3. **Set Time** (optional):

    - For Check In: Enter arrival time
    - For Check Out: Enter departure time
    - Defaults to current time

4. **Add Notes** (optional):

    - Reason for absence
    - Late arrival explanation
    - Any special circumstances

5. Click **"Record Attendance"** button

### Example: Late Arrival

```
Student Code: STU2024001
Type: Check In
Date: 2025-01-07
Time: 09:30 AM
Notes: Late due to doctor appointment
```

### Example: Mark Absent

```
Student Code: STU2024002
Type: Mark Absent
Date: 2025-01-07
Notes: Sick leave
```

---

## 📋 View All Attendance

### Access

-   Click **"View All"** button on dashboard
-   Or go to: Attendance → View All

### Filters Available

1. **Date**: Select specific date
2. **Status**:
    - All Status
    - Present
    - Absent
    - Late
    - Excused
3. **Class**: Filter by specific class

### Example Queries

**"Show me all absent students today"**

-   Date: 2025-01-07
-   Status: Absent
-   Class: All Classes
-   Click "Filter"

**"Show me late arrivals in Class 10A"**

-   Date: 2025-01-07
-   Status: Late
-   Class: 10A
-   Click "Filter"

**"Show me all attendance for yesterday"**

-   Date: 2025-01-06
-   Status: All Status
-   Class: All Classes
-   Click "Filter"

### Clear Filters

-   Click **"Clear"** button to reset all filters

---

## 🎨 Understanding Status Badges

### Status Indicators

-   🟢 **Present** (Green badge) - Student checked in
-   🔴 **Absent** (Red badge) - Student not present
-   🟡 **Late** (Yellow badge) - Checked in after 8:00 AM
-   🔵 **Excused** (Blue badge) - Approved absence

### Method Indicators

-   🔵 **NFC** - Automated via NFC reader
-   ⚫ **Manual** - Entered by staff

### Late Icon

-   ⏰ **Schedule Icon** - Appears next to status if late

---

## ⚙️ Common Workflows

### Morning Check-In (Automated)

```
1. Arduino NFC reader is on
2. Students arrive at school
3. Each student taps wristband
4. System records check-in
5. Dashboard updates automatically
6. Late students flagged (after 8:00 AM)
```

### Afternoon Check-Out (Automated)

```
1. Students leaving school
2. Each student taps wristband again
3. System records check-out
4. Duration calculated
5. Record marked complete
```

### Manual Correction

```
1. Staff notices student forgot to tap
2. Go to Manual Entry
3. Search student by code
4. Select "Check In" or "Check Out"
5. Set correct time
6. Add note: "Forgot to tap card"
7. Record attendance
```

### Marking Absent Students

```
Option 1: Individual
- Use Manual Entry for each student
- Select "Mark Absent"
- Add reason

Option 2: Bulk (via backend)
- Call autoMarkAbsent() function
- Marks all students without check-in as absent
```

---

## 🔍 Searching Students

### By Student Code

```
Search: STU2024001
Result: Shows student information
```

### Tips

-   Student code is case-insensitive
-   No spaces needed
-   Press Enter to search quickly
-   Error message if not found

---

## 📈 Statistics & Reports

### Today's Statistics

Available on dashboard:

-   Total students in system
-   Present count and percentage
-   Absent count and percentage
-   Late count (of present students)

### View Individual Student

```
Future enhancement - track:
- Attendance percentage
- Late arrival count
- Absence history
- Patterns and trends
```

---

## ⚠️ Common Issues & Solutions

### Issue: Student Not Found

**Problem**: Search returns "Student not found"
**Solution**:

-   Check student code spelling
-   Verify student exists in system
-   Check if student is active

### Issue: Already Checked In

**Problem**: "Student already checked in today"
**Solution**:

-   Use "Check Out" instead
-   Or view attendance list to verify

### Issue: Already Checked Out

**Problem**: "Student already checked out today"
**Solution**:

-   Cannot record again same day
-   Use manual entry for corrections if needed

### Issue: NFC Not Working

**Problem**: NFC reader not responding
**Solution**:

-   Check Arduino connection
-   Verify serial port in .env
-   Test with manual entry meanwhile
-   Check Arduino serial monitor

---

## 🎓 Best Practices

### For Staff

1. ✅ Keep NFC reader clean and accessible
2. ✅ Monitor dashboard during peak hours
3. ✅ Use manual entry for corrections
4. ✅ Add notes for unusual circumstances
5. ✅ Review daily statistics at end of day

### For Students

1. ✅ Always tap wristband on arrival
2. ✅ Remember to tap when leaving
3. ✅ Keep wristband clean
4. ✅ Report lost/damaged wristband immediately
5. ✅ Wait for confirmation beep/message

### For Administrators

1. ✅ Review weekly attendance reports
2. ✅ Identify patterns (chronic lateness, absences)
3. ✅ Export data for parent communications
4. ✅ Monitor system health
5. ✅ Train staff on manual entry procedures

---

## 📱 Device Compatibility

### Desktop/Laptop

-   ✅ Full functionality
-   ✅ Best experience on 13"+ screens
-   ✅ All browsers supported

### Tablet

-   ✅ Perfect for kiosk mode
-   ✅ Touch-friendly interface
-   ✅ Dashboard auto-refresh works

### Mobile

-   ✅ Responsive design
-   ✅ Manual entry works well
-   ✅ Dashboard optimized for small screens

---

## 🔐 Security Notes

-   🔒 Only authenticated users can access
-   🔒 All actions logged with user ID
-   🔒 CSRF protection on all forms
-   🔒 Input validation prevents errors
-   🔒 Student data protected

---

## 📞 Need Help?

### Check Documentation

1. `ATTENDANCE_IMPLEMENTATION_SUMMARY.md` - Full technical details
2. `NFC_ATTENDANCE_GUIDE.md` - Setup and configuration
3. `ARDUINO_NFC_SETUP.md` - Hardware setup

### Common Questions

**Q: Can I edit past attendance?**
A: Use manual entry with past date

**Q: What time is considered "late"?**
A: Default is 8:00 AM (configurable)

**Q: Can parents see attendance?**
A: Future enhancement - will add parent portal

**Q: How long is data kept?**
A: Indefinitely (database storage)

**Q: Can I export attendance?**
A: Future enhancement - PDF/Excel export coming

---

## 🎉 Quick Tips

💡 **Tip 1**: Press Enter in search box instead of clicking button
💡 **Tip 2**: Dashboard auto-refreshes - no need to reload manually
💡 **Tip 3**: Status badges are color-coded for quick identification
💡 **Tip 4**: Use filters to quickly find specific records
💡 **Tip 5**: Add notes for unusual situations - helps with reports

---

_This guide will help you get started with the attendance system. For technical details, see the full documentation._

**System Status: ✅ Ready to Use**
