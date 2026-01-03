# Student Seating Arrangement Model

This directory contains the AI model for generating optimal seating arrangements based on student performance marks.

## 📚 Documentation

- **[QUICKSTART.md](QUICKSTART.md)** - Get started in 5 minutes
- **[METHODOLOGY.md](METHODOLOGY.md)** - Complete implementation guide, methodology, and API docs

## Directory Structure

```
student seating arrangement model/
├── api/                    # Flask API for seating arrangement generation
│   └── app.py             # Main API endpoint
├── src/                   # Core model implementation
│   ├── __init__.py
│   ├── seating_generator.py    # Main seating arrangement algorithm
│   └── utils.py           # Helper functions
├── config/                # Configuration files
│   └── config.py         # API and model configuration
├── dataset/              # Training/reference dataset
├── requirements.txt      # Python dependencies
├── start_api.sh         # Startup script
├── test_system.py       # Test suite
├── README.md            # This file
├── QUICKSTART.md        # Quick start guide
└── METHODOLOGY.md       # Detailed methodology and implementation
```

## Features

- **Performance-Based Arrangement**: Places high-performing students next to lower-performing students to encourage peer learning
- **Grade-Level Support**: Handles multiple grade levels (e.g., 11-A, 11-B, 11-C)
- **Term-Based Analysis**: Uses latest term marks for arrangement decisions
- **Fair Distribution**: Ensures balanced seating across the classroom
- **Simple API Integration**: Easy to integrate with Laravel backend
- **Visual Classroom Layout**: Clear representation of seating arrangement
- **Student Access**: Students can view their assigned seats
- **Caching System**: Efficient caching to reduce API calls

## Quick Start

### 1. Install Dependencies

```bash
pip install -r requirements.txt
```

### 2. Start the API

```bash
chmod +x start_api.sh
./start_api.sh
```

Or manually:

```bash
cd api
python app.py
```

### 3. Test the System

```bash
python test_system.py
```

### 4. Configure Laravel

Add to your Laravel `.env`:

```env
SEATING_API_URL=http://localhost:5001
```

## How It Works

### Algorithm Overview

The system uses a **high-low pairing strategy**:

1. **Sort Students**: Order students by average marks (highest to lowest)
2. **Zigzag Pairing**: Alternate between high and low performers
   - Seat 1: Highest performer
   - Seat 2: Lowest performer
   - Seat 3: 2nd highest
   - Seat 4: 2nd lowest
   - Continue...
3. **Grid Assignment**: Map linear seats to row/column positions

### Example

Class with marks: [92, 88, 78, 58, 52, 45, 41]

**Seating Order**: 92, 41, 88, 45, 78, 52, 58

This ensures high and low performers sit together!

## API Endpoints

- `GET /health` - Health check
- `POST /generate-seating` - Generate seating arrangement
- `GET /student-seat` - Get individual student seat
- `POST /visualize` - Get text visualization

## Laravel Integration

### Routes Available

- `GET /admin/seating` - Admin dashboard
- `POST /admin/seating/generate` - Generate arrangement
- `GET /admin/seating/show/{grade}/{section}` - View arrangement
- `GET /admin/seating/my-seat` - Student's seat view

### Service Usage

```php
use App\Services\SeatingArrangementService;

$service = new SeatingArrangementService();
$arrangement = $service->generateSeatingArrangement('11', 'A');
$seat = $service->getStudentSeat($studentId);
```

## Performance

- **Small Classes** (1-30 students): <1 second
- **Medium Classes** (31-50 students): 1-2 seconds
- **Large Classes** (51+ students): 2-5 seconds

## Technology Stack

- **Python 3.8+**: Core implementation
- **Flask**: RESTful API
- **Laravel 10+**: Backend integration
- **Bootstrap 4**: Frontend UI

## Configuration

Edit `config/config.py`:

```python
API_PORT = 5001              # API port
DEFAULT_SEATS_PER_ROW = 5    # Classroom layout
DEFAULT_ROWS = 6             # Number of rows
```

## Testing

Run the test suite:

```bash
python test_system.py
```

This will verify:

- ✅ API connectivity
- ✅ Seating generation
- ✅ Student seat lookup
- ✅ Algorithm correctness

## Troubleshooting

### API Won't Start

```bash
# Check port availability
lsof -i :5001

# Install dependencies
pip install -r requirements.txt
```

### Laravel Connection Issues

```bash
# Test API
curl http://localhost:5001/health

# Clear Laravel cache
php artisan config:clear
php artisan cache:clear
```

## Documentation

For detailed information, see:

- **[QUICKSTART.md](QUICKSTART.md)** - 5-minute setup guide
- **[METHODOLOGY.md](METHODOLOGY.md)** - Full documentation including:
  - Educational methodology
  - Algorithm design
  - Implementation details
  - API documentation
  - Laravel integration guide
  - Troubleshooting
  - Architecture diagrams

## Support

- Check logs: `storage/logs/laravel.log`
- API logs: Console output or configure file logging
- Test connectivity: `python test_system.py`

## License

Part of AI-Powered Smart School Safety and Performance Monitoring System

---

**Ready to create optimal seating arrangements? Start with [QUICKSTART.md](QUICKSTART.md)!**
