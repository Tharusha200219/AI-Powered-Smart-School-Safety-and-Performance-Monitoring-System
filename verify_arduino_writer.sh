#!/bin/bash

# Arduino Writer Sketch Verification Script
# Run this after uploading the writer sketch to verify it's working correctly

echo "=========================================="
echo "Arduino Writer Sketch Verification"
echo "=========================================="
echo ""

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Get serial port from .env
SERIAL_PORT=$(grep -E "^ARDUINO_SERIAL_PORT=" .env | cut -d '=' -f2)

if [ -z "$SERIAL_PORT" ]; then
    echo -e "${RED}❌ Error: ARDUINO_SERIAL_PORT not found in .env file${NC}"
    echo ""
    echo "Please add this to your .env file:"
    echo "ARDUINO_SERIAL_PORT=/dev/cu.usbserial-110"
    echo "(adjust the port to match your Arduino)"
    exit 1
fi

echo -e "${BLUE}📍 Using port: $SERIAL_PORT${NC}"
echo ""

# Check if port exists
if [ ! -e "$SERIAL_PORT" ]; then
    echo -e "${RED}❌ Error: Port $SERIAL_PORT not found${NC}"
    echo ""
    echo "Available ports:"
    ls -la /dev/cu.* 2>/dev/null || ls -la /dev/ttyUSB* /dev/ttyACM* 2>/dev/null
    echo ""
    echo "Update your .env file with the correct port."
    exit 1
fi

echo -e "${GREEN}✅ Port exists${NC}"
echo ""

# Check if port is in use
if lsof "$SERIAL_PORT" 2>/dev/null; then
    echo -e "${YELLOW}⚠️  Warning: Port is currently in use${NC}"
    echo ""
    echo "Please close:"
    echo "- Arduino IDE Serial Monitor"
    echo "- Any other programs using the Arduino"
    echo ""
    read -p "Press Enter after closing these programs..."
    echo ""
fi

# Test connection using PHP script
echo -e "${BLUE}🔌 Testing connection...${NC}"
echo ""

php test_arduino.php

echo ""
echo "=========================================="
echo "Next Steps"
echo "=========================================="
echo ""
echo -e "${YELLOW}If the test PASSED:${NC}"
echo "✅ Your Arduino has the correct writer sketch"
echo "✅ You can now create students with NFC enrollment"
echo ""
echo -e "${YELLOW}If the test FAILED:${NC}"
echo "❌ Upload arduino_mfrc522_nfc_writer.ino using Arduino IDE"
echo "❌ Follow the steps in ARDUINO_WRITER_SETUP.md"
echo ""
echo "=========================================="
