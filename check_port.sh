#!/bin/bash

echo "Checking if Arduino serial port is in use..."

# Check for macOS
if lsof /dev/cu.usbserial-110 2>/dev/null; then
    echo ""
    echo "⚠️  The serial port is currently in use by another program!"
    echo ""
    echo "This is usually Arduino IDE Serial Monitor."
    echo ""
    echo "Options:"
    echo "1. Close Arduino IDE Serial Monitor manually"
    echo "2. Kill the process (press 'y' to kill it now)"
    echo ""
    read -p "Kill the process? (y/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        PID=$(lsof -t /dev/cu.usbserial-110)
        kill $PID 2>/dev/null && echo "✅ Process killed" || echo "❌ Failed to kill process"
    fi
else
    echo "✅ Port is available"
fi
