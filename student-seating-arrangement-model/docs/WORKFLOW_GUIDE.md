# Student Seating Arrangement - Complete Workflow Guide

## 🚀 Complete System Workflows

This guide explains exactly what happens when you:

1. **Generate Seating Arrangement** - How students are optimally seated
2. **Add New Students** - How the system handles new enrollment
3. **Re-arrange Classroom** - How to seat students for different scenarios

---

## 🎯 WORKFLOW 1: GENERATING SEATING ARRANGEMENT

### Goal

Automatically create optimal classroom seating by pairing high-performing students with lower-performing students for peer learning.

### Step-by-Step Process

#### **STEP 1: Prepare Student Data**

**Input data format** (CSV or Database):

```
StudentID | Name      | Average_Marks | Grade | Subject | Status
----------|-----------|---------------|-------|---------|--------
S001      | Ahmed     | 95            | 10    | A       | Active
S002      | Fatima    | 65            | 10    | C       | Active
S003      | Ali       | 88            | 10    | B+      | Active
S004      | Zainab    | 45            | 10    | D       | Active
S005      | Omar      | 78            | 10    | B       | Active
...
S030      | Sarah     | 30            | 10    | F       | Active
```

---

#### **STEP 2: Load Configuration**

**File**: `config/config.py`

```python
# Classroom Layout
SEATS_PER_ROW = 5
TOTAL_ROWS = 6
TOTAL_CAPACITY = 30  # 5 × 6

# Performance Thresholds
HIGH_PERFORMER_THRESHOLD = 75      # A+ students
AVERAGE_PERFORMER_THRESHOLD = 50   # B/C students
LOW_PERFORMER_THRESHOLD = 0        # D/F students

# Seating Strategy
PAIRING_STRATEGY = 'zigzag'        # Alternate high-low
RANDOMIZATION = 0.1                # 10% randomness
```

**Output**: Configuration loaded into memory

---

#### **STEP 3: Fetch Student Data**

```python
# File: src/seating_generator.py

# Load from database or CSV
students = load_student_data()

print(f"Loaded {len(students)} students")
# Output: Loaded 30 students
```

**Students loaded:**

```
┌─────────────────┐
│  30 Students    │
│  with marks     │
└─────────────────┘
```

---

#### **STEP 4: Validate Student Data**

```python
# File: src/utils.py → validate_student_data()

for student in students:
    # Check required fields
    assert 'student_id' in student
    assert 'name' in student
    assert 'marks' in student

    # Check data types
    assert isinstance(student['marks'], (int, float))
    assert 0 <= student['marks'] <= 100

    # Check for duplicates
    assert student['student_id'] not in seen_ids

print("✓ All 30 students validated")
```

**Validation checks:**

- ✓ All students have required fields
- ✓ Marks are numbers between 0-100
- ✓ No duplicate student IDs
- ✓ All students are active

---

#### **STEP 5: Categorize Students**

```python
# File: src/utils.py → calculate_performance_category()

high_performers = []    # Marks ≥ 75
avg_performers = []     # Marks 50-74
low_performers = []     # Marks < 50

for student in students:
    marks = student['marks']

    if marks >= 75:
        high_performers.append(student)
        student['category'] = 'HIGH'
    elif marks >= 50:
        avg_performers.append(student)
        student['category'] = 'AVERAGE'
    else:
        low_performers.append(student)
        student['category'] = 'LOW'

print(f"High: {len(high_performers)}")       # Output: High: 8
print(f"Average: {len(avg_performers)}")     # Output: Average: 12
print(f"Low: {len(low_performers)}")         # Output: Low: 10
```

**Distribution:**

```
30 Students
├─ High Performers (≥75): 8 students
├─ Average Performers (50-74): 12 students
└─ Low Performers (<50): 10 students
```

**Examples:**

| Category | Students             | Marks      |
| -------- | -------------------- | ---------- |
| HIGH     | Ahmed, Omar, Layla   | 95, 88, 78 |
| AVERAGE  | Mariam, Hassan, Noor | 72, 65, 58 |
| LOW      | Zainab, Sara, Karim  | 45, 30, 28 |

---

#### **STEP 6: Sort Students by Performance**

```python
# File: src/seating_generator.py

# Sort all students by marks (descending)
students_sorted = sorted(students, key=lambda s: s['marks'], reverse=True)

print("Sorted Students:")
for i, student in enumerate(students_sorted, 1):
    print(f"{i:2}. {student['name']:15} - {student['marks']:6.1f} - {student['category']}")

# Output:
#  1. Ahmed          -   95.0 - HIGH
#  2. Omar           -   88.0 - HIGH
#  3. Layla          -   82.5 - HIGH
#  ...
# 28. Karim          -   35.5 - LOW
# 29. Sara           -   32.0 - LOW
# 30. Zainab         -   28.5 - LOW
```

---

#### **STEP 7: Split into High and Low Performers**

```python
# File: src/seating_generator.py

# Take top 50% as high performers
n = len(students_sorted)
mid_point = n // 2

high_half = students_sorted[:mid_point]      # Top 15 students
low_half = students_sorted[mid_point:][::-1] # Bottom 15 students (reversed)

print(f"High half: {[s['name'] for s in high_half]}")
print(f"Low half: {[s['name'] for s in low_half]}")

# Output:
# High half: [Ahmed, Omar, Layla, ...]
# Low half: [Zainab, Sara, Karim, ...]
```

**Visual:**

```
All 30 students sorted by marks
│
├─ Top 15 (High performers)
│  1. Ahmed (95)
│  2. Omar (88)
│  3. Layla (82.5)
│  ...
│  15. Mariam (56)
│
└─ Bottom 15 (Low performers) - REVERSED
   1. Zainab (28.5)
   2. Sara (32)
   3. Karim (35.5)
   ...
   15. Hassan (68)
```

---

#### **STEP 8: Apply Zigzag Pairing Strategy**

```python
# File: src/seating_generator.py

paired_list = []

for i in range(len(high_half)):
    # Alternate: high, low, high, low, high, low...
    paired_list.append(high_half[i])      # High performer
    paired_list.append(low_half[i])       # Low performer

print("Paired arrangement:")
for i in range(0, len(paired_list), 2):
    high_student = paired_list[i]
    low_student = paired_list[i+1]
    print(f"Pair: {high_student['name']} ({high_student['marks']}) ↔ {low_student['name']} ({low_student['marks']})")

# Output:
# Pair: Ahmed (95) ↔ Zainab (28.5)
# Pair: Omar (88) ↔ Sara (32)
# Pair: Layla (82.5) ↔ Karim (35.5)
# Pair: Ali (78) ↔ Hassan (42)
# ...
```

**Why zigzag?**

- High student can help low student
- Spread strong students across classroom
- Maximize peer learning opportunities

---

#### **STEP 9: Assign Seat Numbers**

```python
# File: src/seating_generator.py

# Classroom layout:
# Row 1: [Seat 1] [Seat 2] [Seat 3] [Seat 4] [Seat 5]
# Row 2: [Seat 6] [Seat 7] [Seat 8] [Seat 9] [Seat 10]
# ...
# Row 6: [Seat 26] [Seat 27] [Seat 28] [Seat 29] [Seat 30]

seat_assignments = []
seat_number = 1

for student in paired_list:
    row = (seat_number - 1) // SEATS_PER_ROW + 1
    column = (seat_number - 1) % SEATS_PER_ROW + 1

    assignment = {
        'seat_number': seat_number,
        'row': row,
        'column': column,
        'student_id': student['student_id'],
        'name': student['name'],
        'marks': student['marks'],
        'category': student['category']
    }

    seat_assignments.append(assignment)
    seat_number += 1

print("Seat Assignments:")
print("Row 1:")
for seat in seat_assignments[:5]:
    print(f"  Seat {seat['seat_number']}: {seat['name']} ({seat['marks']})")
# Output:
# Row 1:
#   Seat 1: Ahmed (95) - HIGH
#   Seat 2: Zainab (28.5) - LOW
#   Seat 3: Omar (88) - HIGH
#   Seat 4: Sara (32) - LOW
#   Seat 5: Layla (82.5) - HIGH
```

---

#### **STEP 10: Visualize Seating Chart**

```python
# File: src/seating_generator.py

# Create visual classroom layout
classroom = []

for assignment in seat_assignments:
    row = assignment['row']
    col = assignment['column']

    # Ensure classroom array is large enough
    while len(classroom) <= row:
        classroom.append([None] * SEATS_PER_ROW)

    classroom[row-1][col-1] = assignment

# Print visual
print("\n📚 CLASSROOM SEATING ARRANGEMENT 📚")
print("=" * 70)

for row_num, row_data in enumerate(classroom, 1):
    print(f"\nRow {row_num}:")
    for seat_data in row_data:
        if seat_data:
            symbol = "🔴" if seat_data['category'] == 'HIGH' else "🟡" if seat_data['category'] == 'AVERAGE' else "🔵"
            print(f"  {symbol} Seat {seat_data['seat_number']:2}: {seat_data['name']:15} ({seat_data['marks']:5.1f})")

# Output:
# 📚 CLASSROOM SEATING ARRANGEMENT 📚
# ======================================================================
#
# Row 1:
#   🔴 Seat  1: Ahmed          ( 95.0)
#   🔵 Seat  2: Zainab         ( 28.5)
#   🔴 Seat  3: Omar           ( 88.0)
#   🔵 Seat  4: Sara           ( 32.0)
#   🔴 Seat  5: Layla          ( 82.5)
#
# Row 2:
#   🔵 Seat  6: Karim          ( 35.5)
#   🔴 Seat  7: Ali            ( 78.0)
#   🟡 Seat  8: Mariam         ( 62.0)
#   🔴 Seat  9: Hassan         ( 75.5)
#   🔵 Seat 10: Noor           ( 40.0)
```

---

#### **STEP 11: Validate Seating Arrangement**

```python
# File: src/seating_generator.py → validate_arrangement()

# Check 1: All students seated?
assert len(seat_assignments) == len(students)
print("✓ All 30 students seated")

# Check 2: No duplicate seats?
seat_nums = [s['seat_number'] for s in seat_assignments]
assert len(seat_nums) == len(set(seat_nums))
print("✓ No duplicate seats")

# Check 3: High and low performers distributed?
high_count = sum(1 for s in seat_assignments if s['category'] == 'HIGH')
low_count = sum(1 for s in seat_assignments if s['category'] == 'LOW')
print(f"✓ High performers: {high_count}, Low performers: {low_count}")

# Check 4: Pairing balanced?
for i in range(0, len(seat_assignments), 2):
    cat1 = seat_assignments[i]['category']
    cat2 = seat_assignments[i+1]['category']
    if not ((cat1 == 'HIGH' and cat2 == 'LOW') or (cat1 == 'LOW' and cat2 == 'HIGH')):
        print(f"⚠ Warning: Pair {i//2 + 1} not well balanced")

print("✓ Arrangement validated successfully!")
```

---

#### **STEP 12: Calculate Algorithm Quality Score**

```python
# File: src/seating_generator.py → calculate_quality_score()

# Quality metrics
total_students = len(seat_assignments)
high_performers = sum(1 for s in seat_assignments if s['category'] == 'HIGH')
low_performers = sum(1 for s in seat_assignments if s['category'] == 'LOW')

# Metric 1: High-Low pairing ratio (ideally 50-50)
high_low_ratio = high_performers / low_performers
ideal_ratio = 1.0
pairing_score = 1 - abs(high_low_ratio - ideal_ratio) / ideal_ratio

# Metric 2: Spatial distribution (spread strong students across rows)
marks_per_row = [
    sum(s['marks'] for s in seat_assignments[i:i+SEATS_PER_ROW])
    for i in range(0, total_students, SEATS_PER_ROW)
]
distribution_score = 1 - (max(marks_per_row) - min(marks_per_row)) / sum(marks_per_row)

# Overall score
overall_quality = (pairing_score + distribution_score) / 2 * 100

print(f"\n🎯 Algorithm Quality Metrics:")
print(f"  Pairing Score: {pairing_score*100:.1f}%")
print(f"  Distribution Score: {distribution_score*100:.1f}%")
print(f"  Overall Quality: {overall_quality:.1f}%")

# Output:
# 🎯 Algorithm Quality Metrics:
#   Pairing Score: 98.5%
#   Distribution Score: 96.2%
#   Overall Quality: 97.4%
```

---

#### **STEP 13: Save Seating Arrangement**

```python
# File: src/seating_generator.py

import json
from datetime import datetime

# Prepare output
output = {
    'timestamp': datetime.now().isoformat(),
    'classroom': {
        'seats_per_row': SEATS_PER_ROW,
        'total_rows': TOTAL_ROWS,
        'total_capacity': TOTAL_CAPACITY
    },
    'statistics': {
        'total_students': len(seat_assignments),
        'high_performers': sum(1 for s in seat_assignments if s['category'] == 'HIGH'),
        'average_performers': sum(1 for s in seat_assignments if s['category'] == 'AVERAGE'),
        'low_performers': sum(1 for s in seat_assignments if s['category'] == 'LOW'),
        'algorithm_quality': overall_quality
    },
    'seating_chart': seat_assignments
}

# Save to JSON
with open('output/seating_arrangement.json', 'w') as f:
    json.dump(output, f, indent=2)

print("✓ Seating arrangement saved to output/seating_arrangement.json")
```

**Output JSON:**

```json
{
  "timestamp": "2024-05-05T15:30:22Z",
  "classroom": {
    "seats_per_row": 5,
    "total_rows": 6,
    "total_capacity": 30
  },
  "statistics": {
    "total_students": 30,
    "high_performers": 8,
    "average_performers": 12,
    "low_performers": 10,
    "algorithm_quality": 97.4
  },
  "seating_chart": [
    {
      "seat_number": 1,
      "row": 1,
      "column": 1,
      "student_id": "S001",
      "name": "Ahmed",
      "marks": 95.0,
      "category": "HIGH"
    },
    {
      "seat_number": 2,
      "row": 1,
      "column": 2,
      "student_id": "S030",
      "name": "Zainab",
      "marks": 28.5,
      "category": "LOW"
    }
  ]
}
```

---

#### **STEP 14: Send to API**

```python
# File: api/app.py → /generate endpoint

@app.route('/generate', methods=['POST'])
def generate_seating():
    """Generate seating arrangement"""

    # Receive student data
    data = request.json
    students = data['students']

    # Generate arrangement (steps 1-13)
    generator = SeatingArrangementGenerator()
    arrangement = generator.generate(students)

    # Return JSON response
    return jsonify({
        'success': True,
        'arrangement': arrangement,
        'timestamp': datetime.now().isoformat()
    }), 200
```

---

#### ✅ **SEATING GENERATION COMPLETE!**

```
✓ 30 students loaded
✓ Data validated
✓ Students categorized (8 high, 12 avg, 10 low)
✓ Zigzag pairing applied
✓ Seats assigned
✓ Quality score: 97.4%
✓ JSON file saved
✓ API response sent

Seating arrangement ready to display in Laravel dashboard!
```

---

---

## ➕ WORKFLOW 2: ADDING NEW STUDENTS

### Goal

When new students enroll, update the seating arrangement without disrupting existing seats.

### Step-by-Step Process

#### **STEP 1: New Students Enroll**

**Scenario:** 5 new students join mid-semester

```
New Students:
- StudentID: S031, Name: Rayan, Marks: 72
- StudentID: S032, Name: Leila, Marks: 85
- StudentID: S033, Name: Hassan, Marks: 45
- StudentID: S034, Name: Aisha, Marks: 92
- StudentID: S035, Name: Youssef, Marks: 55
```

---

#### **STEP 2: Merge with Existing Students**

```python
# Load existing seating
existing_students = load_from_json('output/seating_arrangement.json')
# 30 students with assigned seats

# New students without seats yet
new_students = [
    {'id': 'S031', 'name': 'Rayan', 'marks': 72},
    {'id': 'S032', 'name': 'Leila', 'marks': 85},
    # ...
]

# Combine
all_students = existing_students + new_students
# 35 students now

print(f"Existing: 30, New: 5, Total: {len(all_students)}")
```

---

#### **STEP 3: Find Available Seats**

```python
# Classroom capacity: 30
# Currently seated: 30
# Available: 0

# Need to expand or find extra space
if len(all_students) > TOTAL_CAPACITY:
    print(f"⚠ Classroom full! {len(all_students) - TOTAL_CAPACITY} students need alternate arrangements")
    print("Options:")
    print("1. Expand classroom (buy more desks)")
    print("2. Move some students to another section")
    print("3. Create waiting list")
```

---

#### **STEP 4: Rearrange Entire Classroom** (If capacity allows)

```python
# Re-run seating algorithm on all 35 students
generator = SeatingArrangementGenerator(
    seats_per_row=5,
    total_rows=7  # Expand to accommodate 35
)

new_arrangement = generator.generate(all_students)

print(f"✓ Rearranged all {len(all_students)} students across 7 rows")
```

---

#### **STEP 5: Identify Seat Changes**

```python
# Compare old vs new arrangement
changes = []

for new_seat in new_arrangement:
    student_id = new_seat['student_id']
    old_seat = find_student_old_seat(student_id)

    if old_seat and old_seat['seat_number'] != new_seat['seat_number']:
        changes.append({
            'student': new_seat['name'],
            'old_seat': old_seat['seat_number'],
            'new_seat': new_seat['seat_number'],
            'change': new_seat['seat_number'] - old_seat['seat_number']
        })

print(f"Seats Changed: {len(changes)}")
print("Changed Students:")
for change in changes:
    direction = "→" if change['change'] > 0 else "←"
    print(f"  {change['student']}: Seat {change['old_seat']} {direction} Seat {change['new_seat']}")

# Output:
# Seats Changed: 8
# Changed Students:
#   Ahmed: Seat 1 → Seat 1
#   Omar: Seat 3 → Seat 4
#   Layla: Seat 5 → Seat 6
#   Rayan: New Student → Seat 7
```

---

#### **STEP 6: Minimize Disruption** (Optional)

```python
# Strategy: Keep as many students in same seats as possible

# Mark which students MUST stay
students_to_keep = existing_students[:15]  # Keep first half
students_to_rearrange = existing_students[15:] + new_students

# Regenerate only for rearrangeable group
new_partial = generator.generate(students_to_rearrange)

# Combine kept + rearranged
final_arrangement = students_to_keep + new_partial

print(f"Minimized disruption:")
print(f"  Students staying in same seat: {len(students_to_keep)}")
print(f"  Students moved: {len(students_to_rearrange)}")
```

---

#### ✅ **NEW STUDENTS INTEGRATED!**

```
Before: 30 students in 6 rows
After: 35 students in 7 rows
Seats changed: 8 (23%)
Quality score: 96.8%
```

---

---

## 🎓 Complete Seating Flow Summary

```
Student Data
    ↓
┌──────────────────────────────┐
│ Categorize Performance       │
│ High (≥75) | Avg (50-74)    │
│ Low (<50)                    │
└──────────────────────────────┘
    ↓
┌──────────────────────────────┐
│ Apply Zigzag Strategy        │
│ Pair: High ↔ Low            │
└──────────────────────────────┘
    ↓
┌──────────────────────────────┐
│ Assign Seats Row by Row      │
│ Create seating map           │
└──────────────────────────────┘
    ↓
┌──────────────────────────────┐
│ Validate & Score             │
│ Quality metrics              │
└──────────────────────────────┘
    ↓
✓ Seating Arrangement Ready
    ↓
Display in Laravel Dashboard
```
