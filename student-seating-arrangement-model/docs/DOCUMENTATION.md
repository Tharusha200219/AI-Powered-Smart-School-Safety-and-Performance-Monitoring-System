# Student Seating Arrangement Model Documentation

## 1. What This Module Is

This module is a Python Flask service that generates classroom seating arrangements based on student academic performance.

Main idea:

- Sort students by marks
- Mix high performers with low performers
- Assign seats in a zigzag pattern

The service is designed to be called by the Laravel main system.

## 2. Technologies Used

- Python 3
- Flask (`Flask==3.0.0`) for REST API
- Flask-CORS (`Flask-CORS==4.0.0`) for cross-origin requests
- Requests (`requests==2.32.5`) for API testing utilities
- NumPy (`numpy`, used in `evaluate_algorithm.py`) for evaluation metrics
- Shell scripts (`setup.sh`, `start_api.sh`) for setup and startup

## 3. How It Works (End-to-End)

1. Client (Laravel or test script) sends student data to `POST /generate-seating`.
2. API validates required inputs (`grade`, `section`, `students`).
3. `SeatingArrangementGenerator` validates student records.
4. Students are sorted by `average_marks` (descending).
5. High-low pairing algorithm assigns seats:

- Highest mark -> next seat lowest mark -> next seat second highest -> next seat second lowest.

6. System returns seat map with row/column, seat labels, and performance level.

Optional endpoints:

- `GET /student-seat`: find seat for one student from an arrangement payload
- `POST /visualize`: returns text visualization of the classroom
- `GET /health`: service health status

## 4. Algorithm Details

Core strategy: `high_low_pairing`

In `src/seating_generator.py`:

- `_sort_students_by_performance()` sorts by marks
- `_generate_high_low_pairing()` uses two pointers (`left`, `right`)
- `_create_seat_assignment()` computes row and column

Performance labels currently assigned based on list position in sorted data:

- `high`
- `medium`
- `low`

Mark-based cutoff categories (defined in `src/utils.py` via `calculate_performance_category`) are:

- `high`: `average_marks >= 75`
- `medium`: `50 <= average_marks < 75`
- `low`: `average_marks < 50`

Note:

- Current seat assignment in `src/seating_generator.py` uses list-position logic during pairing.
- The mark cutoff function above is available as a utility and can be used if you want strict mark-based labeling everywhere.

## 5. Project Structure and What Each File Does

### Root files

- `requirements.txt`
  - Python packages required to run the API.
- `setup.sh`
  - Creates virtual environment, installs dependencies, runs basic tests.
- `start_api.sh`
  - Starts the Flask API with environment variables.
- `test_system.py`
  - Integration-style test script for health, generation, seat lookup, and algorithm checks.
- `evaluate_algorithm.py`
  - Evaluation script for algorithm quality metrics (balance, pairing quality, overall effectiveness).

### `api/`

- `api/app.py`
  - Main Flask app with all endpoints:
    - `GET /health`
    - `POST /generate-seating`
    - `GET /student-seat`
    - `POST /visualize`

### `src/`

- `src/seating_generator.py`
  - Core algorithm implementation and seat assignment logic.
- `src/utils.py`
  - Validation and helper functions (student data validation, marks average, grouping, categories).
- `src/__init__.py`
  - Package initializer.

### `config/`

- `config/config.py`
  - API host/port/debug and default classroom configuration.

### `dataset/`

- `dataset/student_performance_updated_1000 (1).csv`
  - Dataset source file with student performance records (used as input source/reference).

### `docs/`

- `docs/DOCUMENTATION.md`
  - This documentation file.

## 6. Request and Response Format

### `POST /generate-seating`

Request JSON:

```json
{
  "grade": "11",
  "section": "A",
  "students": [
    {
      "student_id": "S001",
      "name": "John Doe",
      "average_marks": 85.5,
      "grade": "11",
      "section": "A"
    }
  ],
  "seats_per_row": 5,
  "total_rows": 6
}
```

Success response (shortened):

```json
{
  "success": true,
  "data": {
    "grade": "11",
    "section": "A",
    "arrangement": [
      {
        "seat_number": 1,
        "seat_label": "S1",
        "row": 1,
        "column": 1,
        "student_id": "S001",
        "student_name": "John Doe",
        "average_marks": 85.5,
        "performance_level": "high"
      }
    ]
  }
}
```

## 7. Important Configuration Note

There is currently a port mismatch:

- `config/config.py` default port: `5003`
- `start_api.sh` sets port: `5001`
- `test_system.py` expects API at: `http://localhost:5001`

Recommendation:

- Standardize to one port (prefer `5001` if this is already integrated with Laravel).

## 8. How to Run

1. From `student-seating-arrangement-model/` run:

```bash
./setup.sh
```

2. Start API:

```bash
./start_api.sh
```

3. Run tests:

```bash
python test_system.py
```

## 9. Integration Role in Main System

This service is a dedicated microservice that:

- Receives student performance data from main system
- Generates optimized seat arrangement
- Returns arrangement JSON for storage/display in Laravel

It does not train an ML model. It uses a deterministic optimization strategy.
