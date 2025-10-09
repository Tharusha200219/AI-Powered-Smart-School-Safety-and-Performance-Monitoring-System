# Arduino Writer Setup Guide

## Problem
Your Arduino is currently running a **reader sketch** (reads and displays student data), but the Laravel app needs a **writer sketch** (writes student data to NFC tags).

## Solution: Upload the Writer Sketch

### Step 1: Close Any Serial Connections
Before uploading, ensure nothing is using the Arduino's serial port:
- **Close Arduino IDE Serial Monitor** if it's open
- **Stop any running Laravel processes** that might be connected to the Arduino
- Run this command to check if the port is in use:
  ```bash
  ./check_port.sh
  ```

### Step 2: Upload the Writer Sketch

1. **Open Arduino IDE**

2. **Open the writer sketch**:
   - Go to **File → Open**
   - Navigate to your project folder
   - Select `arduino_mfrc522_nfc_writer.ino`

3. **Verify the sketch compiles**:
   - Click the **✓ Verify** button
   - Wait for "Done compiling" message

4. **Select your board and port**:
   - Go to **Tools → Board** → Select **Arduino Uno** (or your board model)
   - Go to **Tools → Port** → Select your Arduino port:
     - macOS: `/dev/cu.usbserial-110` or `/dev/cu.usbmodem*`
     - Linux: `/dev/ttyUSB0` or `/dev/ttyACM0`
     - Windows: `COM3`, `COM4`, etc.

5. **Upload the sketch**:
   - Click the **→ Upload** button
   - Wait for "Done uploading" message

### Step 3: Verify the Upload

After uploading, verify the sketch is working:

1. **Open Serial Monitor** (Tools → Serial Monitor)
2. Set baud rate to **9600**
3. You should see:
   ```
   READY
   ```

4. **Close Serial Monitor** before testing with Laravel

### Step 4: Test with Laravel

Run the test script:
```bash
php test_arduino.php
```

You should see:
```
✅ Connection Test: PASSED
   Port: /dev/cu.usbserial-110
   Status: Arduino connected successfully
```

### Step 5: Test Student Creation

1. Go to **Admin → Management → Students → Create Student**
2. Fill in student details
3. Click **Create Student**
4. When the NFC modal appears, **place your NFC tag on the reader**
5. Wait for the success message (should appear within 2-3 seconds)

## Troubleshooting

### Port Access Denied
```bash
# macOS/Linux - Give yourself permission
sudo chmod 666 /dev/cu.usbserial-110
```

### Wrong Port in .env
Check your `.env` file:
```bash
# Should match the port in Arduino IDE
ARDUINO_SERIAL_PORT=/dev/cu.usbserial-110
ARDUINO_BAUD_RATE=9600
ARDUINO_TIMEOUT=30
```

### Still Getting Timeout
1. Check the Arduino IDE Serial Monitor - do you see "READY"?
2. If yes, close the Serial Monitor and try again
3. If no, re-upload the sketch

### Need to Switch Back to Reader
If you want to use this Arduino as a reader later:
1. Upload your reader sketch instead
2. Reader is for attendance kiosks (automatic check-in/out)
3. Writer is for student enrollment (writing data to new tags)

## Expected Behavior

### Writer Sketch (Current Need)
- **Purpose**: Write student data to new NFC tags during enrollment
- **Commands**: `WRITE_NFC`, `PING`, `STATUS`
- **Response**: `INFO: Waiting for RFID tag...` → `INFO: Tag detected, writing data...` → `SUCCESS`

### Reader Sketch (For Later)
- **Purpose**: Read student data for attendance check-in/check-out
- **Commands**: Continuous reading mode
- **Response**: Shows student data with fancy formatting

## Next Steps

After successfully uploading the writer sketch:
1. ✅ Create students with NFC tag enrollment
2. ✅ Verify tag data is written correctly
3. 🔄 For a second device, keep this as reader for attendance
4. 🔄 Deploy reader Arduino at school entrance for automatic attendance

---

## Quick Reference

| Sketch Type | Use Case | Port Setting | Commands |
|-------------|----------|--------------|----------|
| **Writer** | Student enrollment | One-time connection | `WRITE_NFC`, `PING` |
| **Reader** | Attendance kiosk | Continuous connection | Auto-read loop |

**Current Status**: You need the WRITER sketch for creating students.
