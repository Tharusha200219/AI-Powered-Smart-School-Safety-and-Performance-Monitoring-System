# Quick Configuration Guide - WiFi Attendance Reader

## Pre-flight Checklist

Before you begin, have these ready:

-   ✅ WiFi network name (SSID)
-   ✅ WiFi password
-   ✅ Laravel server IP address or domain
-   ✅ All hardware components
-   ✅ Arduino IDE installed

## 5-Minute Setup

### 1. Configure Arduino Code (2 minutes)

Open `arduino/attendance_reader/arduino_wifi_attendance_reader.ino` and change:

```cpp
// Line 76-77: WiFi Credentials
const char* WIFI_SSID = "YourWiFiName";      // 🔧 CHANGE THIS
const char* WIFI_PASSWORD = "YourPassword";   // 🔧 CHANGE THIS

// Line 80: Server URL
// Replace 192.168.1.100 with your server's IP
const char* SERVER_URL = "http://192.168.1.100:8000/api/attendance/rfid-scan";  // 🔧 CHANGE THIS

// Line 84: Device ID (optional - for multiple devices)
const char* DEVICE_ID = "ATTENDANCE_READER_01";  // Optional: Change for multiple devices
```

**Finding Your Server IP:**

**Windows:**

```bash
ipconfig
# Look for "IPv4 Address"
```

**Mac/Linux:**

```bash
ifconfig
# Look for "inet" under your active network
```

**Or use your domain:**

```cpp
const char* SERVER_URL = "http://yourdomain.com/api/attendance/rfid-scan";
```

### 2. Hardware Connections (2 minutes)

Quick reference (detailed in main guide):

```
RFID (MFRC522):  RST→9, SS→10, MOSI→11, MISO→12, SCK→13, 3.3V, GND
LCD (I2C):       SDA→A4, SCL→A5, 5V, GND
RTC (DS3231):    SDA→A4, SCL→A5, 5V, GND
SD Card:         CS→4, MOSI→11, MISO→12, SCK→13, 5V, GND
RGB LED:         R→6, G→5, B→3, Cathode→GND (with 220Ω resistors)
Buzzer:          +→8, -→GND
```

⚠️ **CRITICAL**: RFID uses 3.3V, not 5V!

### 3. Upload to Arduino (1 minute)

1. Connect Arduino via USB
2. Arduino IDE: `Tools > Board` → Select your board
3. Arduino IDE: `Tools > Port` → Select your port
4. Click Upload button (→)
5. Wait for "Done uploading"

### 4. Verify Operation

Open Serial Monitor (115200 baud) and check for:

```
=================================
WiFi RFID Attendance System
=================================

Initializing RFID...
✓ RFID Reader Version: 0x92

Initializing RTC...
✓ RTC Time: 2025/10/9 14:30:45

Initializing SD Card...
✓ SD Card initialized successfully

Connecting to WiFi...
✓ WiFi Connected!
IP Address: 192.168.1.150

=== System Ready ===
Place RFID card near reader...
```

**LCD should show:**

```
Ready to Scan
14:30:45 09/10
```

**LED should be:** Off (ready to scan)

## Testing

### Test 1: Enroll a Student with RFID

1. Go to your Laravel admin panel
2. Navigate to: Management → Students
3. Click "Add New Student" or edit existing
4. Fill in student details
5. Click "Create Student" button
6. Place RFID card on the writer device
7. Wait for success message

### Test 2: First Attendance Scan (Check-in)

1. Place enrolled RFID card on attendance reader
2. **Expected behavior:**
    - Yellow LED (reading)
    - Green LED + 2 beeps (success)
    - LCD shows: "Success! Welcome!"
    - Serial Monitor: "Student checked in successfully"
3. Verify in admin panel: Management → Attendance

### Test 3: Second Scan (Check-out)

1. Wait 3 seconds
2. Place same card on reader again
3. **Expected behavior:**
    - Yellow LED (reading)
    - Green LED + 2 beeps (success)
    - LCD shows: "Success! Goodbye!"
    - Serial Monitor: "Student checked out successfully"
4. Verify check-out time recorded in admin panel

## Troubleshooting Quick Fixes

### ❌ WiFi won't connect

**Problem**: `✗ WiFi Connection Failed!`

**Solutions:**

1. Double-check SSID and password (case-sensitive!)
2. Ensure 2.4GHz network (ESP8266 doesn't support 5GHz)
3. Move closer to router
4. Check if WiFi requires additional authentication (captive portal)
5. Try different network

### ❌ Server not responding

**Problem**: `✗ Failed to send to server`

**Solutions:**

1. Ensure Laravel server is running:
    ```bash
    php artisan serve --host=0.0.0.0 --port=8000
    ```
2. Check firewall isn't blocking port 8000
3. Test API manually:
    ```bash
    curl http://your-ip:8000/api/attendance/rfid-scan
    ```
4. Verify server IP in code matches actual IP
5. Check both devices on same network

### ❌ RFID not reading

**Problem**: `ERROR: RFID reader not found!`

**Solutions:**

1. Check all 7 RFID connections
2. Verify using 3.3V (NOT 5V!)
3. Try different RFID module
4. Check SPI pins not used by other devices
5. Reseat all connections

### ❌ LCD blank or gibberish

**Problem**: LCD shows nothing or random characters

**Solutions:**

1. Adjust contrast potentiometer on back of LCD
2. Check I2C address (try 0x27 or 0x3F):
    ```cpp
    LiquidCrystal_I2C lcd(0x3F, 16, 2);  // Try 0x3F instead
    ```
3. Verify I2C connections (A4/SDA, A5/SCL)
4. Run I2C scanner (see main guide)
5. Check 5V power supply

### ❌ Time incorrect

**Problem**: Wrong time on LCD

**Solution**: Sync RTC time:

```cpp
// Uncomment this line in setup():
rtc.adjust(DateTime(F(__DATE__), F(__TIME__)));
// Upload, then comment out and re-upload
```

### ❌ Student not found

**Problem**: `Student not found in database`

**Solutions:**

1. Verify student enrolled in system
2. Check student code matches RFID data
3. Re-write RFID card via student enrollment
4. Check database connection in Laravel
5. View Laravel logs: `storage/logs/laravel.log`

## Offline Mode

If server is unavailable:

1. **Device automatically saves to SD card**

    - Orange LED indicates offline mode
    - LCD shows "Offline Mode - Saved Locally"
    - Data written to `pending.csv`

2. **Manual sync (when server returns)**

    - Access device management in admin panel
    - Click "Sync Pending Records"
    - All offline scans uploaded

3. **SD Card files:**
    - `attendance.log` - Full log
    - `pending.csv` - Records to sync

## Configuration Options

### Change LCD I2C Address

If LCD doesn't work, try different address:

```cpp
// Line 67 - try 0x3F if 0x27 doesn't work
LiquidCrystal_I2C lcd(0x3F, 16, 2);
```

### Disable Buzzer

If no buzzer or too loud:

```cpp
// Comment out buzzer calls or set volume
// In beep functions, reduce duration:
tone(BUZZER_PIN, 2000, 50);  // Shorter beep
```

### Change Scan Cooldown

Prevent duplicate scans:

```cpp
// Line 88 - adjust cooldown time (milliseconds)
const int SCAN_COOLDOWN = 5000;  // 5 seconds instead of 3
```

### Adjust School Times

Late threshold configured in Laravel API:

```php
// In getDeviceConfig() method
'school_start_time' => '08:00:00',
'late_threshold' => '08:15:00',  // Students late after this time
```

## Multiple Devices

For multiple attendance readers:

1. **Clone the setup for each device**

2. **Give each unique ID:**

    ```cpp
    // Device 1
    const char* DEVICE_ID = "MAIN_ENTRANCE";

    // Device 2
    const char* DEVICE_ID = "LIBRARY_ENTRANCE";

    // Device 3
    const char* DEVICE_ID = "GYM_ENTRANCE";
    ```

3. **Track location in database** (device_id field)

4. **Monitor all devices** from admin panel

## Success Indicators

Your system is working when:

-   ✅ WiFi connects on startup
-   ✅ LCD shows time and "Ready to Scan"
-   ✅ RFID cards scan successfully
-   ✅ Green LED and beeps on success
-   ✅ Attendance appears in admin panel immediately
-   ✅ Check-in and check-out both work
-   ✅ Orange LED + SD save when offline

## Need More Help?

1. **Check Serial Monitor** - Shows detailed error messages
2. **Read Full Guide** - `WIFI_ATTENDANCE_SETUP_GUIDE.md`
3. **Check Laravel Logs** - `storage/logs/laravel.log`
4. **Test Components** - Test each part individually
5. **Hardware Issues** - Check all connections with multimeter

## Command Reference

### Start Laravel Server

```bash
cd /path/to/project
php artisan serve --host=0.0.0.0 --port=8000
```

### View Laravel Logs

```bash
tail -f storage/logs/laravel.log
```

### Test API Endpoint

```bash
curl -X POST http://your-ip:8000/api/attendance/rfid-scan \
  -H "Content-Type: application/json" \
  -d '{"student_data":"TEST","card_uid":"123","device_id":"TEST","timestamp":"2025-10-09 14:30:00"}'
```

### Find IP Address

```bash
# Mac/Linux
ifconfig | grep "inet "

# Windows
ipconfig | findstr IPv4
```

## Library Versions (if issues)

Install these specific versions if you have problems:

```
MFRC522: 1.4.10
LiquidCrystal_I2C: 1.1.2
RTClib: 2.1.1
ArduinoJson: 6.21.3
ESP8266: 3.1.2
```

---

**🎉 You're all set! Place a student's RFID card near the reader to test!**

**Next Steps:**

1. Enroll all students with RFID cards
2. Place device at entrance
3. Monitor attendance in admin panel
4. Set up additional readers if needed
