# Arduino MFRC522 RFID Writer - Complete Guide

## 📋 Overview

This system writes student data to RFID cards/tags using **Arduino Uno** and **MFRC522 (RC522) RFID module** for the Smart School Safety System.

---

## 📦 What You Need

### Hardware
- **Arduino Uno**
- **MFRC522 RFID Reader Module (RC522)**
- **MIFARE Classic 1KB RFID Cards/Tags**
- **USB Cable** (Type A to Type B)

### Software
- **Arduino IDE** (1.8.x or 2.x)
- **MFRC522 Library** (by GithubCommunity)
- **Laravel Application** (already installed)

---

## 🔌 Hardware Setup

### MFRC522 to Arduino Uno Connections

| MFRC522 Pin | Arduino Pin | Description |
|-------------|-------------|-------------|
| SDA (SS)    | Digital 10  | Chip Select |
| SCK         | Digital 13  | SPI Clock   |
| MOSI        | Digital 11  | SPI Data In |
| MISO        | Digital 12  | SPI Data Out|
| IRQ         | Not Connected | Interrupt (optional) |
| GND         | GND         | Ground      |
| RST         | Digital 9   | Reset       |
| 3.3V        | 3.3V        | Power       |

⚠️ **CRITICAL**: MFRC522 operates at **3.3V**. DO NOT connect to 5V or you will damage the module!

### Wiring Photo Reference
```
Arduino Uno          MFRC522 Module
   3.3V  ──────────── 3.3V (VCC)
   GND   ──────────── GND
   Pin 9 ──────────── RST
   Pin 10 ─────────── SDA (SS)
   Pin 11 ─────────── MOSI
   Pin 12 ─────────── MISO
   Pin 13 ─────────── SCK
```

---

## 🚀 Software Setup

### Step 1: Install Arduino IDE

Download from: https://www.arduino.cc/en/software

### Step 2: Install MFRC522 Library

1. Open Arduino IDE
2. Go to **Sketch** → **Include Library** → **Manage Libraries**
3. Search for **"MFRC522"**
4. Install **"MFRC522 by GithubCommunity"** (by Miguel Balboa)
5. Click **Install**

### Step 3: Upload Arduino Sketch

1. Open `arduino_mfrc522_nfc_writer.ino` in Arduino IDE
2. Select Board: **Tools** → **Board** → **Arduino Uno**
3. Select Port: **Tools** → **Port** → Select your Arduino's port
   - **macOS**: `/dev/cu.usbserial-*` or `/dev/cu.usbmodem*`
   - **Linux**: `/dev/ttyUSB0` or `/dev/ttyACM0`
   - **Windows**: `COM3`, `COM4`, etc.
4. Click **Upload** button (→)
5. Wait for "Done uploading" message

### Step 4: Verify Arduino

1. Open **Tools** → **Serial Monitor**
2. Set baud rate to **9600**
3. You should see: `READY`
4. Type `PING` and press Enter
5. Arduino should respond: `PONG`
6. **Close Serial Monitor** before continuing

### Step 5: Configure Laravel

Update your `.env` file:

```env
# Arduino MFRC522 Configuration
ARDUINO_SERIAL_PORT=/dev/cu.usbserial-110  # Change to your port
ARDUINO_BAUD_RATE=9600
ARDUINO_TIMEOUT=30
```

**Find Your Serial Port:**
- **macOS**: `ls /dev/cu.*`
- **Linux**: `ls /dev/ttyUSB* /dev/ttyACM*`
- **Windows**: Check Device Manager → Ports (COM & LPT)

**Set Permissions (Linux/macOS):**
```bash
sudo chmod 666 /dev/cu.usbserial-110  # Use your actual port
```

---

## ✅ Testing

### Test 1: Arduino Connection

Run the verification script:
```bash
./verify_arduino_writer.sh
```

Expected output:
```
✅ Port exists
✅ Connection Test: PASSED
   Port: /dev/cu.usbserial-110
   Status: Arduino connected successfully
```

### Test 2: PHP Connection

```bash
php test_arduino.php
```

Expected output:
```
✅ Serial port exists
✅ Port is readable and writable
✅ Port opened successfully
✅ Arduino responded: PONG
✅ Test Complete!
```

### Test 3: Write Student Data

1. Go to **Admin Panel** → **Management** → **Students**
2. Click **Create Student**
3. Fill in student details
4. Click **Create Student** button
5. When NFC modal appears, place RFID card on reader
6. Wait 2-3 seconds
7. Should see: ✅ **Student data successfully written to RFID tag!**

---

## 📖 How It Works

### Arduino Protocol

The Arduino sketch responds to these commands:

| Command | Purpose | Response |
|---------|---------|----------|
| `WRITE_NFC` | Write student data to tag | `SUCCESS` or `ERROR` |
| `PING` | Test connection | `PONG` |
| `STATUS` | Check Arduino status | `READY` |

### Write Sequence

```
Laravel → Arduino: "WRITE_NFC\n"
Laravel → Arduino: "56\n"  (data length)
Laravel → Arduino: "STU-00001|John|Doe|5|5A|2024-01-01\n"
Arduino → Reader: Wait for tag...
Arduino → Reader: Authenticate with default key (0xFF x 6)
Arduino → Reader: Write data to blocks 4-63
Arduino → Laravel: "INFO: Waiting for RFID tag..."
Arduino → Laravel: "INFO: Tag detected, writing data..."
Arduino → Laravel: "SUCCESS"
```

### Data Format

Student data is pipe-delimited:
```
STUDENT_CODE|FIRST_NAME|LAST_NAME|GRADE|CLASS|ENROLLMENT_DATE
```

Example:
```
stu-00000001|John|Doe|10|A|2024-01-15
```

Maximum length: 256 characters

### Storage Structure

MIFARE Classic 1KB cards have 16 sectors × 4 blocks:
- **Blocks 0-3**: Reserved (manufacturer data, sector trailer)
- **Blocks 4-63**: Available for data storage (used by this system)
- **Block size**: 16 bytes per block

The Arduino writes data sequentially across blocks, padding with zeros.

---

## 🐛 Troubleshooting

### Problem: Timeout Error

**Symptoms:**
- "Communication timeout. Please ensure Arduino is connected..."
- Modal stays on "Waiting..." for 10 seconds

**Solutions:**
1. Check if Arduino Serial Monitor is open (close it)
2. Verify Arduino is showing "READY" message
3. Ensure correct sketch is uploaded (not a reader sketch)
4. Check serial port in `.env` matches Arduino IDE port
5. Try unplugging and replugging Arduino
6. Run `./verify_arduino_writer.sh` to diagnose

### Problem: Port Not Found

**Symptoms:**
- "Serial port does not exist"
- Laravel can't connect to Arduino

**Solutions:**
1. Check USB cable connection
2. Try a different USB port
3. Install Arduino drivers (Windows)
4. Check port permissions (Linux/macOS):
   ```bash
   ls -la /dev/cu.usbserial-110
   sudo chmod 666 /dev/cu.usbserial-110
   ```
5. Update `.env` with correct port

### Problem: No Tag Detected

**Symptoms:**
- "No tag detected within timeout period"
- Arduino waits but never writes

**Solutions:**
1. Place card/tag 1-3 cm from reader antenna
2. Try different RFID card (must be MIFARE Classic 1KB)
3. Check MFRC522 wiring (especially SDA and SCK)
4. Verify 3.3V power supply is stable
5. Try another MFRC522 module (may be defective)

### Problem: Write Failed

**Symptoms:**
- "Auth failed for block X"
- "Write failed for block X"

**Solutions:**
1. Card may have non-standard keys (not 0xFF x 6)
2. Try a new blank card
3. Card may be write-protected
4. Check card is actually MIFARE Classic (not Ultralight/DESFire)

### Problem: Arduino Not Responding

**Symptoms:**
- No "READY" in Serial Monitor
- No "PONG" response to PING

**Solutions:**
1. Re-upload the sketch
2. Select correct board (Arduino Uno)
3. Select correct port
4. Check USB cable (try different cable)
5. Press Arduino reset button after upload

### Problem: Serial Monitor Shows Gibberish

**Solutions:**
1. Set baud rate to **9600** (not 115200)
2. Check "Newline" option in Serial Monitor
3. Re-upload sketch

---

## 📁 Core Files

### Essential Files (DO NOT DELETE)

| File | Purpose |
|------|---------|
| `arduino_mfrc522_nfc_writer.ino` | Arduino sketch for writing |
| `app/Services/ArduinoNFCService.php` | Laravel serial communication |
| `MFRC522_SETUP_GUIDE.md` | Detailed setup instructions |
| `ARDUINO_WRITER_SETUP.md` | Quick setup guide |
| `SYSTEM_ARCHITECTURE.md` | System flow diagrams |
| `test_arduino.php` | Connection test script |
| `verify_arduino_writer.sh` | Verification script |
| `check_port.sh` | Port availability checker |
| `.env.arduino.example` | Configuration example |

---

## 🎯 Quick Command Reference

```bash
# Check available ports
ls /dev/cu.*                              # macOS
ls /dev/ttyUSB* /dev/ttyACM*             # Linux

# Set port permissions
sudo chmod 666 /dev/cu.usbserial-110     # Your actual port

# Test connection
./verify_arduino_writer.sh

# Test PHP connection
php test_arduino.php

# Check port usage
./check_port.sh

# View logs
tail -f storage/logs/laravel.log
```

---

## 📞 Support

If you encounter issues not covered here:

1. Check `storage/logs/laravel.log` for error details
2. Open Arduino IDE Serial Monitor to see Arduino output
3. Verify all wiring connections
4. Try with a known-working RFID card
5. Test with Arduino examples first (File → Examples → MFRC522)

---

## 🎓 Learning Resources

- **MFRC522 Library Documentation**: https://github.com/miguelbalboa/rfid
- **MIFARE Classic Documentation**: NXP Semiconductors website
- **Arduino Serial Communication**: https://www.arduino.cc/reference/en/language/functions/communication/serial/
- **SPI Protocol**: https://www.arduino.cc/en/reference/SPI

---

**Last Updated**: October 9, 2025  
**System Version**: 1.0  
**Compatible Hardware**: Arduino Uno, MFRC522 RC522 Module, MIFARE Classic 1KB Cards
