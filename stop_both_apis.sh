#!/bin/bash

# Stop both AI model APIs

echo "============================================================"
echo "Stopping AI Model APIs"
echo "============================================================"

# Find and kill processes on ports 5002 and 5001
echo "Looking for running API processes..."

PIDS=$(lsof -ti:5002,5001 2>/dev/null)

if [ -z "$PIDS" ]; then
    echo "✓ No API processes found running on ports 5001 or 5002"
    exit 0
fi

echo "Found processes: $PIDS"
echo "Stopping APIs..."

# Kill the processes
lsof -ti:5002,5001 | xargs kill -9 2>/dev/null

# Wait a moment
sleep 1

# Verify they're stopped
REMAINING=$(lsof -ti:5002,5001 2>/dev/null)

if [ -z "$REMAINING" ]; then
    echo ""
    echo "============================================================"
    echo "✅ All APIs stopped successfully!"
    echo "============================================================"
else
    echo ""
    echo "⚠️  Warning: Some processes may still be running"
    echo "Try: lsof -ti:5002,5001 | xargs kill -9"
fi
