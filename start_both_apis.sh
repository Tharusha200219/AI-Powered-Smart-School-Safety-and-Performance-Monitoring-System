#!/bin/bash

# Start both AI model APIs for Laravel integration

echo "============================================================"
echo "Starting AI Model APIs"
echo "============================================================"

# Get the absolute path to the project root
PROJECT_ROOT="$(cd "$(dirname "$0")" && pwd)"

# Kill any existing processes on ports 5002 and 5001
echo "Cleaning up existing processes..."
lsof -ti:5002,5001 | xargs kill -9 2>/dev/null || true
sleep 2

# Start Performance Prediction API (Port 5002)
echo ""
echo "Starting Performance Prediction API on port 5002..."
cd "$PROJECT_ROOT/student-performance-prediction-model"
source venv/bin/activate
python api/app.py > /tmp/performance_api.log 2>&1 &
PERF_PID=$!
echo "Performance API started with PID: $PERF_PID"

# Wait a bit
sleep 3

# Start Seating Arrangement API (Port 5001)
echo ""
echo "Starting Seating Arrangement API on port 5001..."
cd "$PROJECT_ROOT/student-seating-arrangement-model"
source venv/bin/activate
python api/app.py > /tmp/seating_api.log 2>&1 &
SEAT_PID=$!
echo "Seating API started with PID: $SEAT_PID"

# Wait for APIs to start
sleep 3

echo ""
echo "============================================================"
echo "API Status Check"
echo "============================================================"

# Check Performance API
if curl -s http://localhost:5002/health > /dev/null 2>&1; then
    echo "✓ Performance Prediction API is running on http://localhost:5002"
    echo "  Health: http://localhost:5002/health"
    echo "  Logs: tail -f /tmp/performance_api.log"
else
    echo "✗ Performance Prediction API failed to start"
    echo "  Check logs: cat /tmp/performance_api.log"
fi

# Check Seating API
if curl -s http://localhost:5001/health > /dev/null 2>&1; then
    echo "✓ Seating Arrangement API is running on http://localhost:5001"
    echo "  Health: http://localhost:5001/health"
    echo "  Logs: tail -f /tmp/seating_api.log"
else
    echo "✗ Seating Arrangement API failed to start"
    echo "  Check logs: cat /tmp/seating_api.log"
fi

echo ""
echo "============================================================"
echo "PIDs saved:"
echo "  Performance API: $PERF_PID"
echo "  Seating API: $SEAT_PID"
echo ""
echo "To stop the APIs, run:"
echo "  kill $PERF_PID $SEAT_PID"
echo "Or kill all: lsof -ti:5002,5001 | xargs kill -9"
echo "============================================================"
