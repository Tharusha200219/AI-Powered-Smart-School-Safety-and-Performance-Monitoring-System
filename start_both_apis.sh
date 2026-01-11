#!/bin/bash

# Start all AI model APIs for the Smart School Safety System
# Note: Run Laravel Dashboard separately with: cd "Smart-School-Safety-and-Performance-Monitoring-System Dashboard" && php artisan serve

echo "============================================================"
echo "🚀 Starting AI Model APIs"
echo "============================================================"

# Get the absolute path to the project root
PROJECT_ROOT="$(cd "$(dirname "$0")" && pwd)"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to check if port is in use
check_port() {
    local port=$1
    local name=$2
    if lsof -Pi :$port -sTCP:LISTEN -t >/dev/null ; then
        echo -e "${YELLOW}⚠️  Port $port ($name) is already in use${NC}"
        return 1
    else
        echo -e "${GREEN}✅ Port $port ($name) is available${NC}"
        return 0
    fi
}

# Function to kill process on port
kill_port() {
    local port=$1
    local name=$2
    echo -e "${BLUE}🔄 Killing existing process on port $port ($name)...${NC}"
    lsof -ti:$port | xargs kill -9 2>/dev/null || true
    sleep 2
}

# Function to start service
start_service() {
    local name=$1
    local command=$2
    local log_file=$3

    echo -e "${BLUE}🚀 Starting $name...${NC}"
    eval "$command > \"$log_file\" 2>&1 &"
    local pid=$!
    echo -e "${GREEN}✅ $name started with PID: $pid${NC}"
    sleep 3

    # Check if process is still running
    if kill -0 $pid 2>/dev/null; then
        echo -e "${GREEN}✅ $name is running${NC}"
    else
        echo -e "${RED}❌ $name failed to start. Check logs: $log_file${NC}"
        return 1
    fi
}

echo ""
echo "🔍 Checking port availability..."

# Check ports (AI APIs only)
check_port 5002 "Performance Prediction API"
check_port 5003 "Seating Arrangement API"
check_port 5004 "Face Recognition API"

echo ""
echo "🧹 Cleaning up existing processes..."

# Kill existing processes on AI API ports
kill_port 5002 "Performance Prediction API"
kill_port 5003 "Seating Arrangement API"
kill_port 5004 "Face Recognition API"

echo ""
echo "📦 Starting AI Model APIs..."

# Start Performance Prediction API (Port 5002)
echo -e "${BLUE}Starting Performance Prediction API...${NC}"
cd "$PROJECT_ROOT/student-performance-prediction-model"
if [ -d "venv" ]; then
    source venv/bin/activate
    start_service "Performance Prediction API (Port 5002)" "python api/app.py" "/tmp/performance_api.log"
    PERF_PID=$!
else
    echo -e "${RED}❌ Virtual environment not found for Performance Prediction API${NC}"
fi

# Start Seating Arrangement API (Port 5003)
echo -e "${BLUE}Starting Seating Arrangement API...${NC}"
cd "$PROJECT_ROOT/student-seating-arrangement-model"
if [ -d "venv" ]; then
    source venv/bin/activate
    start_service "Seating Arrangement API (Port 5003)" "python api/app.py" "/tmp/seating_api.log"
    SEAT_PID=$!
else
    echo -e "${RED}❌ Virtual environment not found for Seating Arrangement API${NC}"
fi

# Start Facial Recognition API (Port 5004)
echo -e "${BLUE}Starting Facial Recognition API...${NC}"
cd "$PROJECT_ROOT/Facial Recognition Attendance Systems"
if [ -d "venv" ]; then
    source venv/bin/activate
    export FACE_RECOGNITION_API_KEY="sk-secure-face-api-key-2024"
    start_service "Facial Recognition API (Port 5004)" "python app.py" "/tmp/face_api.log"
    FACE_PID=$!
else
    echo -e "${RED}❌ Virtual environment not found for Facial Recognition API${NC}"
fi

echo ""
echo "⏳ Waiting for all services to initialize..."
sleep 5

echo ""
echo "🔍 Verifying services..."

# Test services
services_ok=true

# Test Performance Prediction API
if curl -s http://localhost:5002/health 2>/dev/null | grep -q "healthy"; then
    echo -e "${GREEN}✅ Performance Prediction API is responding${NC}"
else
    echo -e "${RED}❌ Performance Prediction API is not responding${NC}"
    services_ok=false
fi

# Test Seating Arrangement API
if curl -s http://localhost:5003/health 2>/dev/null | grep -q "healthy"; then
    echo -e "${GREEN}✅ Seating Arrangement API is responding${NC}"
else
    echo -e "${RED}❌ Seating Arrangement API is not responding${NC}"
    services_ok=false
fi

# Test Face Recognition API
if curl -s http://localhost:5004/api/health 2>/dev/null | grep -q "healthy"; then
    echo -e "${GREEN}✅ Face Recognition API is responding${NC}"
else
    echo -e "${RED}❌ Face Recognition API is not responding${NC}"
    services_ok=false
fi

echo ""
if $services_ok; then
    echo -e "${GREEN}🎉 All AI APIs started successfully!${NC}"
else
    echo -e "${YELLOW}⚠️  Some services may need more time to start. Check logs.${NC}"
fi

echo ""
echo "============================================================"
echo -e "${BLUE}🌐 API URLs:${NC}"
echo "============================================================"
echo -e "   📊 Performance Prediction API: ${GREEN}http://localhost:5002${NC}"
echo -e "   🪑 Seating Arrangement API:    ${GREEN}http://localhost:5003${NC}"
echo -e "   👤 Face Recognition API:       ${GREEN}http://localhost:5004${NC}"
echo ""
echo -e "${YELLOW}📝 Log files:${NC}"
echo "   /tmp/performance_api.log"
echo "   /tmp/seating_api.log"
echo "   /tmp/face_api.log"
echo ""
echo -e "${YELLOW}💡 To start Laravel Dashboard separately:${NC}"
echo "   cd \"Smart-School-Safety-and-Performance-Monitoring-System Dashboard\""
echo "   php artisan serve --host=0.0.0.0 --port=8000"
echo ""
echo -e "${BLUE}🛑 To stop all APIs:${NC} ./stop_both_apis.sh"
echo "============================================================"
