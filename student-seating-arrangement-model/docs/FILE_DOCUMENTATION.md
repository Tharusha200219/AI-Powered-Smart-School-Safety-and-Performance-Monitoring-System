# Student Seating Arrangement Model - File Documentation

## 📁 Project Structure Overview

This module automatically generates optimal seating arrangements in classrooms. It pairs high-performing students with lower-performing students to encourage peer learning and academic support.

---

## 📄 Root Level Files

### `requirements.txt`

- **What it does**: Lists Python packages needed for this module
- **Key packages**:
  - Flask (web server for API)
  - requests (for HTTP calls)
  - numpy, pandas (data processing)
- **How to use**: Run `pip install -r requirements.txt`

### `setup.sh`

- **What it does**: Automated setup script to install all dependencies
- **How to use**: Run `bash setup.sh`

### `start_api.sh`

- **What it does**: Starts the Flask API server
- **Port**: Usually runs on port 5001
- **How to use**: Run `bash start_api.sh`

### `test_system.py`

- **What it does**: Tests if the seating arrangement system works correctly
- **Tests include**:
  - Can it load student data?
  - Can it generate valid seat assignments?
  - Are all students seated?

### `evaluate_algorithm.py`

- **What it does**: Measures how well the seating algorithm works
- **Checks**:
  - Performance balance across seats
  - Distribution of high/low performers
  - Algorithm quality score

---

## ⚙️ `/config` Folder - Settings

### `config.py`

- **What it does**: Central configuration file for the seating system
- **Key settings**:
  - Classroom dimensions (seats per row, number of rows)
  - API port and host
  - Performance categories (high/average/low marks)
  - Database connection details
- **Why important?**: Change settings here without modifying code

---

## 🧠 `/src` Folder - Core Logic

### `seating_generator.py`

- **What it does**: Main algorithm for generating seating arrangements
- **Algorithm steps**:
  1. Load student data with performance marks
  2. Sort students by average marks
  3. Divide into high performers and low performers
  4. Pair them alternately (high ↔ low pattern)
  5. Assign seats row by row in zigzag pattern
- **Key features**:
  - Ensures balanced distribution across classroom
  - High performers scattered throughout to help weaker students
  - Respects classroom layout constraints
- **Output**: Seat assignments with student IDs and seat numbers

### `utils.py`

- **What it does**: Helper functions used by seating_generator.py
- **Contains**:
  - `validate_student_data()`: Checks if student data is valid
  - `calculate_performance_category()`: Categorizes students as high/average/low
  - `format_seat_number()`: Converts row/column to seat number
  - `logger`: Logging setup for debugging
- **Purpose**: Keeps code organized and reusable

### `__init__.py`

- **What it does**: Makes the `/src` folder a Python package
- **Contains**: Imports for easy access to classes/functions
- **When needed?**: Python requires this to recognize folders as modules

---

## 🌐 `/api` Folder - REST API

### `app.py`

- **What it does**: Flask web server for seating arrangement API
- **Endpoints**:
  - `/generate`: Generates new seating arrangement
    - Input: Student list with marks
    - Output: Seat assignments
  - `/validate`: Validates a seating arrangement
  - `/health`: Checks if API is running
- **Features**:
  - CORS enabled (works with Laravel frontend)
  - Error handling for invalid input
  - Returns JSON responses
- **How to use**:
  ```python
  # POST request with student data
  # Returns seat assignments
  ```

---

## 📊 `/data` Folder

- **What it contains**: Student data files needed for seating
- **File format**: CSV with columns like student_id, name, marks, grade

---

## 📈 `/dataset` Folder

- **What it contains**: Sample student datasets for testing
- **Purpose**: Testing without needing live database

---

## 🧪 `/config` Additional

- **JSON config files**: Classroom configuration in JSON format
- **Example**: Rows=6, Seats per row=5 (total 30 students)

---

## 🔄 How Files Work Together

```
Student Data (CSV/Database)
    ↓
validate_student_data() [utils.py]
    ↓
seating_generator.py (calculates optimal arrangement)
    ↓
Seat Assignments (JSON output)
    ↓
app.py (Flask API serves results)
    ↓
Laravel Frontend (displays seating chart)
```

---

## 🎯 Algorithm Overview

### The Zigzag Pairing Strategy:

**Example with 10 students:**

- Marks: [95, 88, 85, 72, 65, 60, 55, 50, 45, 30]
- High performers: [95, 88, 85, 72, 65]
- Low performers: [60, 55, 50, 45, 30]

**Pairing (alternating):**

1. Seat 1: 95 (high)
2. Seat 2: 30 (low)
3. Seat 3: 88 (high)
4. Seat 4: 45 (low)
5. Seat 5: 85 (high)
   ... and so on

**Result**: Every weak student sits near a strong student for peer learning

---

## 📝 Performance Categories

The system classifies students into categories:

- **High Performer**: Marks ≥ 75% (A grade)
- **Average Performer**: Marks 50-74% (B/C grade)
- **Low Performer**: Marks < 50% (D/F grade)

---

## ⚡ Quick Start

1. **Setup**: `bash setup.sh`
2. **Start API**: `bash start_api.sh`
3. **Test system**: `python test_system.py`
4. **Generate seating**:
   - Send POST request to `/generate`
   - Provide student data
   - Receive seating arrangement

---

## 🔧 Customization

To change classroom layout, edit `/config/config.py`:

```python
SEATS_PER_ROW = 5      # Change for wider/narrower classroom
TOTAL_ROWS = 6         # Change for more/fewer rows
TOTAL_CAPACITY = 30    # Auto-calculated
```

---

## 📊 Output Format

Seating arrangement returns JSON like:

```json
{
  "seating_chart": [
    {
      "seat_number": 1,
      "row": 1,
      "column": 1,
      "student_id": "S001",
      "name": "Ahmed",
      "marks": 95
    },
    {
      "seat_number": 2,
      "row": 1,
      "column": 2,
      "student_id": "S020",
      "name": "Fatima",
      "marks": 30
    }
  ],
  "algorithm_quality": 0.92,
  "timestamp": "2024-05-05T10:30:00Z"
}
```
