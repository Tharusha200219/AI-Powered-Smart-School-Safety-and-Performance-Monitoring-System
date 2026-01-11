#!/bin/bash

# Stop all AI model APIs

echo "============================================================"
echo "🛑 Stopping All AI Model APIs"
echo "============================================================"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Find and kill processes on AI API ports: 5002, 5003, 5004
echo "Looking for running AI APIs..."

PIDS=$(lsof -ti:5002,5003,5004 2>/dev/null)

if [ -z "$PIDS" ]; then
    echo -e "${GREEN}✓ No AI APIs found running on ports 5002, 5003, or 5004${NC}"
    exit 0
fi

echo "Found processes: $PIDS"
echo "Stopping all AI APIs..."

# Kill the processes
lsof -ti:5002,5003,5004 | xargs kill -9 2>/dev/null

# Wait a moment
sleep 2

# Verify they're stopped
REMAINING=$(lsof -ti:5002,5003,5004 2>/dev/null)

if [ -z "$REMAINING" ]; then
    echo ""
    echo "============================================================"
    echo -e "${GREEN}✅ All AI APIs stopped successfully!${NC}"
    echo "============================================================"
    echo ""
    echo "Stopped APIs:"
    echo "   📊 Performance Prediction API (Port 5002)"
    echo "   🪑 Seating Arrangement API (Port 5003)"
    echo "   👤 Face Recognition API (Port 5004)"
    echo ""
else
    echo ""
    echo -e "${YELLOW}⚠️  Warning: Some processes may still be running${NC}"
    echo "Try: lsof -ti:5002,5003,5004 | xargs kill -9"
fi
