# Visual Architecture Guide

## System Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                     USER INTERFACE LAYER                         │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌──────────────────────┐      ┌──────────────────────┐        │
│  │   Admin Dashboard    │      │   Student View       │        │
│  │                      │      │                      │        │
│  │  • List Classes      │      │  • My Seat Number    │        │
│  │  • Generate Button   │      │  • Row & Column      │        │
│  │  • View Arrangement  │      │  • Classroom Map     │        │
│  └──────────────────────┘      └──────────────────────┘        │
│           │                              │                      │
└───────────┼──────────────────────────────┼──────────────────────┘
            │                              │
            ▼                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                    LARAVEL APPLICATION                           │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │         SeatingArrangementController                      │  │
│  │  • index()          • show()                              │  │
│  │  • generate()       • showMySeat()                        │  │
│  │  • getArrangement() • getMySeat()                         │  │
│  └──────────────────────────────────────────────────────────┘  │
│                             │                                   │
│                             ▼                                   │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │         SeatingArrangementService                         │  │
│  │                                                            │  │
│  │  • generateSeatingArrangement()                           │  │
│  │  • getSeatingArrangement() [with cache]                   │  │
│  │  • getStudentSeat()                                       │  │
│  │  • prepareStudentData()                                   │  │
│  │  • calculateAverageMarks()                                │  │
│  └──────────────────────────────────────────────────────────┘  │
│                             │                                   │
│                             ▼                                   │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │                    Database Layer                         │  │
│  │                                                            │  │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐               │  │
│  │  │ students │  │  marks   │  │ subjects │               │  │
│  │  └──────────┘  └──────────┘  └──────────┘               │  │
│  └──────────────────────────────────────────────────────────┘  │
│                             │                                   │
└─────────────────────────────┼───────────────────────────────────┘
                              │
                              │ HTTP Request
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                    PYTHON API (Flask)                            │
│                    Port: 5001                                    │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │                API Endpoints (app.py)                     │  │
│  │                                                            │  │
│  │  • POST /generate-seating                                 │  │
│  │  • GET  /student-seat                                     │  │
│  │  • GET  /health                                           │  │
│  │  • POST /visualize                                        │  │
│  └──────────────────────────────────────────────────────────┘  │
│                             │                                   │
│                             ▼                                   │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │       SeatingArrangementGenerator                         │  │
│  │       (seating_generator.py)                              │  │
│  │                                                            │  │
│  │  • generate_arrangement()                                 │  │
│  │  • _sort_students_by_performance()                        │  │
│  │  • _generate_high_low_pairing()                           │  │
│  │  • _create_seat_assignment()                              │  │
│  └──────────────────────────────────────────────────────────┘  │
│                             │                                   │
│                             ▼                                   │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │              Utility Functions (utils.py)                 │  │
│  │                                                            │  │
│  │  • validate_student_data()                                │  │
│  │  • calculate_average_marks()                              │  │
│  │  • format_seat_number()                                   │  │
│  └──────────────────────────────────────────────────────────┘  │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

## Data Flow Diagram

### Admin Generates Seating

```
1. Admin clicks "Generate" for Grade 11-A
   │
   ├─▶ Browser: AJAX POST request
   │
   ├─▶ Laravel Controller: generate()
   │
   ├─▶ Laravel Service: generateSeatingArrangement('11', 'A')
   │
   ├─▶ Database: Fetch all students in Grade 11-A
   │   └─▶ Student model with grade_level='11', section='A'
   │
   ├─▶ Database: Get marks for each student
   │   └─▶ Mark model, most recent term
   │
   ├─▶ Service: Calculate average marks per student
   │
   ├─▶ Service: Prepare data for API
   │   └─▶ Array of: {student_id, name, average_marks, grade, section}
   │
   ├─▶ HTTP Request: POST to Python API
   │   URL: http://localhost:5001/generate-seating
   │   Body: {grade, section, students[], seats_per_row, total_rows}
   │
   ├─▶ Python API: Receive request
   │
   ├─▶ Generator: generate_arrangement()
   │   │
   │   ├─▶ Sort students by marks (descending)
   │   │   [92, 88, 85, 78, 70, 60, 52, 48, 45, 41]
   │   │
   │   ├─▶ Zigzag pairing (left=high, right=low)
   │   │   Seat 1: 92 (left++)
   │   │   Seat 2: 41 (right--)
   │   │   Seat 3: 88 (left++)
   │   │   Seat 4: 45 (right--)
   │   │   ...
   │   │
   │   └─▶ Create seat assignments with positions
   │       {seat_number, seat_label, row, column, student_info...}
   │
   ├─▶ Python API: Return JSON response
   │   {success: true, data: {arrangement, strategy, description...}}
   │
   ├─▶ Laravel Service: Cache result (60 min)
   │   Cache key: "seating_arrangement_11_A"
   │
   ├─▶ Laravel Controller: Return JSON to frontend
   │
   └─▶ Browser: Show success message + redirect to view
```

### Student Views Seat

```
1. Student logs in and navigates to "My Seat"
   │
   ├─▶ Laravel Controller: showMySeat()
   │
   ├─▶ Get authenticated user
   │   └─▶ Auth::user()
   │
   ├─▶ Find student record
   │   └─▶ Student::where('user_id', $user->id)->first()
   │
   ├─▶ Laravel Service: getStudentSeat($student_id)
   │
   ├─▶ Service: Get cached arrangement
   │   Cache key: "seating_arrangement_{grade}_{section}"
   │   │
   │   ├─▶ If cached: Return from cache
   │   │
   │   └─▶ If not cached: Generate new arrangement
   │
   ├─▶ Service: Find student in arrangement
   │   Loop through arrangement['arrangement']
   │   Match student_id
   │
   ├─▶ Controller: Return view with seat data
   │
   └─▶ Browser: Display seat number, position, and classroom map
```

## Algorithm Flow

### High-Low Pairing Algorithm

```
Input: Students = [A:92, B:88, C:85, D:78, E:70, F:60, G:52, H:48, I:45, J:41]

Step 1: Sort by marks (descending)
┌────────────────────────────────────────────────────────┐
│ [A:92, B:88, C:85, D:78, E:70, F:60, G:52, H:48, I:45, J:41] │
└────────────────────────────────────────────────────────┘
   ▲                                                     ▲
   │                                                     │
  LEFT                                                RIGHT
(high performers)                              (low performers)

Step 2: Initialize
left = 0 (points to A:92)
right = 9 (points to J:41)
seat_num = 1

Step 3: Zigzag Assignment Loop

Iteration 1:
  Seat 1 ← students[left]  = A:92  (high)   │ left++  (now 1)
  Seat 2 ← students[right] = J:41  (low)    │ right-- (now 8)

Iteration 2:
  Seat 3 ← students[left]  = B:88  (high)   │ left++  (now 2)
  Seat 4 ← students[right] = I:45  (low)    │ right-- (now 7)

Iteration 3:
  Seat 5 ← students[left]  = C:85  (high)   │ left++  (now 3)
  Seat 6 ← students[right] = H:48  (low)    │ right-- (now 6)

Iteration 4:
  Seat 7 ← students[left]  = D:78  (medium) │ left++  (now 4)
  Seat 8 ← students[right] = G:52  (medium) │ right-- (now 5)

Iteration 5:
  Seat 9 ← students[left]  = E:70  (medium) │ left++  (now 5)
  Seat 10 ← students[right] = F:60 (medium) │ right-- (now 4)

  left (5) > right (4) → STOP

Step 4: Result
┌────────────────────────────────────────────────────────┐
│ Seats: [A:92, J:41, B:88, I:45, C:85, H:48, D:78, G:52, E:70, F:60] │
└────────────────────────────────────────────────────────┘

Step 5: Map to Grid (5 seats per row)
Row 1: [A:92] [J:41] [B:88] [I:45] [C:85]
       S1     S2     S3     S4     S5

Row 2: [H:48] [D:78] [G:52] [E:70] [F:60]
       S6     S7     S8     S9     S10

Output: Each seat has:
  - seat_number: 1-10
  - seat_label: S1-S10
  - row: 1-2
  - column: 1-5
  - student info (id, name, marks)
  - performance_level: high/medium/low
```

## Performance Level Classification

```
                100%
                 │
                 ├─────────────────────┐
                 │   HIGH PERFORMERS   │ ≥ 75%
                 │   (Color: Green)    │
                75%─────────────────────┤
                 │                     │
                 │  MEDIUM PERFORMERS  │ 50-75%
                 │   (Color: Blue)     │
                 │                     │
                50%─────────────────────┤
                 │   LOW PERFORMERS    │ < 50%
                 │   (Color: Yellow)   │
                 └─────────────────────┘
                 0%

Classification Logic:
  if marks >= 75:  return 'high'
  elif marks >= 50: return 'medium'
  else:            return 'low'
```

## Classroom Layout Example

```
                    FRONT OF CLASSROOM
    ═══════════════════════════════════════════════════

    Row 1:  [S1-H]  [S2-L]  [S3-H]  [S4-L]  [S5-H]
            Alice   Frank   Bob     Emma    Charlie
            92%     41%     88%     50%     85%

    Row 2:  [S6-L]  [S7-M]  [S8-M]  [S9-M]  [S10-M]
            Henry   David   Grace   Ivy     Jack
            48%     78%     52%     70%     60%

    ═══════════════════════════════════════════════════
                    BACK OF CLASSROOM

    Legend:
    H = High Performer (Green)
    M = Medium Performer (Blue)
    L = Low Performer (Yellow)

    Pairing Pattern:
    • S1 (High) next to S2 (Low)
    • S3 (High) next to S4 (Low)
    • S5 (High) is edge seat
    • S6 (Low) next to S7 (Medium)
    • Middle rows have more medium performers
```

## Cache Flow

```
Request for Grade 11-A Seating
       │
       ▼
┌──────────────────┐
│ Check Cache      │
│ Key: seating_    │
│ arrangement_11_A │
└────────┬─────────┘
         │
    ┌────┴────┐
    │         │
  Found    Not Found
    │         │
    ▼         ▼
┌────────┐  ┌──────────────┐
│ Return │  │ Generate New │
│ Cached │  │ Arrangement  │
│ Data   │  └──────┬───────┘
└────────┘         │
                   ▼
              ┌──────────┐
              │ Store in │
              │ Cache    │
              │ TTL: 60m │
              └────┬─────┘
                   │
                   ▼
              ┌──────────┐
              │ Return   │
              │ Data     │
              └──────────┘

Cache Invalidation:
• Manual: Admin clicks "Generate" again
• Automatic: After 60 minutes
• Service method: clearCache($grade, $section)
```

## Request/Response Examples

### Example 1: Generate Seating

**Request:**

```http
POST http://localhost:5001/generate-seating
Content-Type: application/json

{
  "grade": "11",
  "section": "A",
  "students": [
    {
      "student_id": "S001",
      "name": "Alice Johnson",
      "average_marks": 92.0,
      "grade": "11",
      "section": "A"
    },
    ...
  ],
  "seats_per_row": 5,
  "total_rows": 2
}
```

**Response:**

```json
{
  "success": true,
  "data": {
    "grade": "11",
    "section": "A",
    "total_students": 10,
    "seating_capacity": 10,
    "seats_per_row": 5,
    "total_rows": 2,
    "strategy": "high_low_pairing",
    "description": "High-performing students are seated next to lower-performing students to encourage peer learning",
    "arrangement": [
      {
        "seat_number": 1,
        "seat_label": "S1",
        "row": 1,
        "column": 1,
        "student_id": "S001",
        "student_name": "Alice Johnson",
        "average_marks": 92.0,
        "performance_level": "high",
        "grade": "11",
        "section": "A"
      },
      ...
    ]
  }
}
```

## Error Handling Flow

```
User Request
     │
     ▼
┌──────────────┐
│ Validation   │
│ Layer        │
└──────┬───────┘
       │
   ┌───┴────┐
   │ Valid? │
   └───┬────┘
       │
    No │ Yes
       │  │
       ▼  ▼
   ┌────────┐  ┌─────────────┐
   │ Return │  │ Process     │
   │ 400    │  │ Request     │
   │ Error  │  └──────┬──────┘
   └────────┘         │
                      │
                  ┌───┴────┐
                  │Success?│
                  └───┬────┘
                      │
                   No │ Yes
                      │  │
                      ▼  ▼
                  ┌────────┐  ┌─────────┐
                  │ Log    │  │ Return  │
                  │ Error  │  │ 200 OK  │
                  │ Return │  └─────────┘
                  │ 500    │
                  └────────┘

Error Types:
• 400: Invalid input data
• 404: Student/arrangement not found
• 500: Internal server error
• 503: Service unavailable
```

## Technology Stack Visualization

```
┌─────────────────────────────────────────────────────────┐
│                    FRONTEND LAYER                        │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │  Bootstrap 4 │  │    jQuery    │  │ SweetAlert2  │  │
│  │     (UI)     │  │   (AJAX)     │  │   (Alerts)   │  │
│  └──────────────┘  └──────────────┘  └──────────────┘  │
└─────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────┐
│                  LARAVEL BACKEND                         │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │    Routes    │  │ Controllers  │  │   Services   │  │
│  │  (web.php)   │  │    (HTTP)    │  │   (Logic)    │  │
│  └──────────────┘  └──────────────┘  └──────────────┘  │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │   Eloquent   │  │    Cache     │  │     Blade    │  │
│  │     ORM      │  │   (Redis)    │  │   Templates  │  │
│  └──────────────┘  └──────────────┘  └──────────────┘  │
└─────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────┐
│                   PYTHON API LAYER                       │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │    Flask     │  │  Flask-CORS  │  │    Logging   │  │
│  │  (API Core)  │  │    (CORS)    │  │   (Errors)   │  │
│  └──────────────┘  └──────────────┘  └──────────────┘  │
│  ┌──────────────┐  ┌──────────────┐                    │
│  │   Algorithm  │  │   Utilities  │                    │
│  │ (Generator)  │  │  (Helpers)   │                    │
│  └──────────────┘  └──────────────┘                    │
└─────────────────────────────────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────┐
│                    DATABASE LAYER                        │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  │
│  │   students   │  │    marks     │  │   subjects   │  │
│  │    table     │  │    table     │  │    table     │  │
│  └──────────────┘  └──────────────┘  └──────────────┘  │
└─────────────────────────────────────────────────────────┘
```

---

This visual guide provides a comprehensive overview of how all components work together!
