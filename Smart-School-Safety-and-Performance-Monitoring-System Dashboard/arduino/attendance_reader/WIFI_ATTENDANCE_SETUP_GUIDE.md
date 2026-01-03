# WiFi RFID Attendance System - Complete Setup Guide

## Overview

This guide will help you set up a standalone WiFi-enabled RFID attendance device that wirelessly communicates with your Laravel school management system. The device reads RFID tags, displays information on an LCD screen, provides visual/audio feedback, and logs attendance with timestamps.

## Hardware Requirements

### Components List

1. **UNO+WiFi R3 ATmega328P+ESP8266** (32Mb flash, USB-TTL CH340G, Micro-USB)
2. **MFRC522 RFID Reader Module** (RC522 13.56MHz) with RFID tags
3. **LCD1602 I2C Display Module** (Blue/Green Screen, 5V PCF8574 IIC Adapter)
4. **DS3231 Real Time Clock Module** (RTC for accurate timestamps)
5. **Micro SD Card Module** (TF Card Adapter for offline logging)
6. **5mm Diffused RGB LED** (Common Cathode for status indication)
7. **Buzzer** (optional, for audio feedback)
8. **Resistors**: 3x 220Ω (for RGB LED), 1x 10kΩ (pull-up if needed)
9. **Breadboard and jumper wires**
10. **5V Power Supply** (USB power adapter or external power)
11. **Micro SD Card** (4GB-32GB, formatted FAT32)

### Estimated Cost

-   Total hardware cost: ~$30-40 USD

## Hardware Connections

### MFRC522 RFID Reader

| MFRC522 Pin | Arduino Pin | Notes               |
| ----------- | ----------- | ------------------- |
| RST/Reset   | Digital 9   | Reset pin           |
| SPI SS      | Digital 10  | Slave Select        |
| SPI MOSI    | Digital 11  | Master Out Slave In |
| SPI MISO    | Digital 12  | Master In Slave Out |
| SPI SCK     | Digital 13  | Serial Clock        |
| 3.3V        | 3.3V        | Power (3.3V only!)  |
| GND         | GND         | Ground              |

⚠️ **Important**: MFRC522 operates at 3.3V, NOT 5V!

### LCD1602 I2C Display

| LCD Pin | Arduino Pin | Notes     |
| ------- | ----------- | --------- |
| SDA     | A4          | I2C Data  |
| SCL     | A5          | I2C Clock |
| VCC     | 5V          | Power     |
| GND     | GND         | Ground    |

### DS3231 RTC Module

| RTC Pin | Arduino Pin | Notes                       |
| ------- | ----------- | --------------------------- |
| SDA     | A4          | I2C Data (shared with LCD)  |
| SCL     | A5          | I2C Clock (shared with LCD) |
| VCC     | 5V          | Power                       |
| GND     | GND         | Ground                      |

### SD Card Module

| SD Pin | Arduino Pin | Notes            |
| ------ | ----------- | ---------------- |
| CS     | Digital 4   | Chip Select      |
| MOSI   | Digital 11  | Shared with RFID |
| MISO   | Digital 12  | Shared with RFID |
| SCK    | Digital 13  | Shared with RFID |
| VCC    | 5V          | Power            |
| GND    | GND         | Ground           |

### RGB LED (Common Cathode)

| LED Pin | Arduino Pin | Notes                 |
| ------- | ----------- | --------------------- |
| Red     | Digital 6   | Through 220Ω resistor |
| Green   | Digital 5   | Through 220Ω resistor |
| Blue    | Digital 3   | Through 220Ω resistor |
| Cathode | GND         | Common ground         |

### Buzzer (Optional)

| Buzzer Pin | Arduino Pin | Notes      |
| ---------- | ----------- | ---------- |
| Positive   | Digital 8   | Signal pin |
| Negative   | GND         | Ground     |

### Connection Diagram

```
                    UNO+WiFi R3 Board
                    +-----------------+
                    |                 |
    RFID RC522 ---->| D9-D13, 3.3V   |
    LCD1602 ------->| A4, A5, 5V     |----> WiFi (ESP8266)
    DS3231 RTC ---->| A4, A5, 5V     |
    SD Card ------->| D4, D11-D13    |
    RGB LED ------->| D3, D5, D6     |
    Buzzer -------->| D8             |
                    |                 |
                    +-----------------+
```

## Software Requirements

### Arduino IDE Setup

1. **Install Arduino IDE** (version 1.8.19 or later)

    - Download from: https://www.arduino.cc/en/software

2. **Install ESP8266 Board Support**

    - Open Arduino IDE
    - Go to `File > Preferences`
    - Add to "Additional Board Manager URLs":
        ```
        http://arduino.esp8266.com/stable/package_esp8266com_index.json
        ```
    - Go to `Tools > Board > Boards Manager`
    - Search for "esp8266"
    - Install "esp8266 by ESP8266 Community"

3. **Install Required Libraries**

    Go to `Sketch > Include Library > Manage Libraries` and install:

    - **MFRC522** by GithubCommunity (v1.4.10 or later)
    - **LiquidCrystal_I2C** by Frank de Brabander (v1.1.2 or later)
    - **RTClib** by Adafruit (v2.1.1 or later)
    - **ArduinoJson** by Benoit Blanchon (v6.21.3 or later)
    - **SD** (built-in, no installation needed)
    - **SPI** (built-in, no installation needed)
    - **Wire** (built-in, no installation needed)
    - **ESP8266WiFi** (included with ESP8266 board support)
    - **ESP8266HTTPClient** (included with ESP8266 board support)

4. **Select Board**

    - Go to `Tools > Board`
    - Select "Generic ESP8266 Module" or your specific board model

5. **Configure Board Settings**
    - `Tools > Flash Size`: "4MB (FS:2MB OTA:~1019KB)"
    - `Tools > Upload Speed`: "115200"
    - `Tools > CPU Frequency`: "80 MHz"

## Step-by-Step Installation

### Step 1: Assemble Hardware

1. **Connect all components** following the connection diagram above
2. **Double-check all connections** - especially MFRC522 voltage (3.3V)
3. **Insert formatted SD card** into SD card module
4. **Connect Arduino to computer** via USB

### Step 2: Configure the Arduino Sketch

1. **Open the sketch**:

    - Navigate to: `arduino/attendance_reader/arduino_wifi_attendance_reader.ino`
    - Open in Arduino IDE

2. **Configure WiFi credentials** (lines 76-77):

    ```cpp
    const char* WIFI_SSID = "YOUR_WIFI_SSID";        // Replace with your WiFi name
    const char* WIFI_PASSWORD = "YOUR_WIFI_PASSWORD"; // Replace with your WiFi password
    ```

3. **Configure server URL** (line 80):

    ```cpp
    const char* SERVER_URL = "http://192.168.1.100:8000/api/attendance/rfid-scan";
    ```

    Replace with your Laravel server's IP address and URL:

    - If on same network: `http://192.168.1.100:8000/api/attendance/rfid-scan`
    - If using domain: `http://yourdomain.com/api/attendance/rfid-scan`
    - For HTTPS: `https://yourdomain.com/api/attendance/rfid-scan`

4. **Configure device ID** (line 84):

    ```cpp
    const char* DEVICE_ID = "ATTENDANCE_READER_01";
    ```

    Use unique IDs for multiple devices: `READER_01`, `READER_02`, etc.

5. **Optional: Configure API token** (line 81):
    ```cpp
    const char* API_TOKEN = "YOUR_API_TOKEN";  // Leave empty if not using authentication
    ```

### Step 3: Upload to Arduino

1. **Select correct port**:

    - `Tools > Port` > Select your Arduino's COM port (e.g., COM3, /dev/cu.usbserial)

2. **Upload the sketch**:

    - Click the "Upload" button (→) in Arduino IDE
    - Wait for "Done uploading" message

3. **Monitor serial output**:
    - Open `Tools > Serial Monitor`
    - Set baud rate to `115200`
    - You should see initialization messages

### Step 4: Test Hardware Components

Watch the serial monitor for initialization messages:

```
=================================
WiFi RFID Attendance System
=================================

Initializing RFID...
RFID Reader Version: 0x92
Initializing RTC...
RTC Time: 2025/10/9 14:30:45
Initializing SD Card...
SD Card initialized successfully
Connecting to WiFi...
✓ WiFi Connected!
IP Address: 192.168.1.150

=== System Ready ===
Place RFID card near reader...
```

**Troubleshooting initialization:**

-   **RFID Error**: Check connections, ensure 3.3V power
-   **RTC Warning**: RTC will use default time, update manually
-   **SD Card Warning**: Check card formatting (FAT32), connections
-   **WiFi Failed**: Verify SSID/password, check network availability

### Step 5: Configure Laravel Backend

1. **Ensure API routes are registered**:

    File: `routes/api.php` should contain:

    ```php
    Route::post('/attendance/rfid-scan', [AttendanceApiController::class, 'rfidScan']);
    ```

2. **Configure CORS** (if needed):

    File: `config/cors.php`:

    ```php
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_origins' => ['*'],  // Or specify your device IPs
    'allowed_methods' => ['*'],
    'allowed_headers' => ['*'],
    ```

3. **Test API endpoint**:

    ```bash
    curl -X POST http://your-server:8000/api/attendance/rfid-scan \
      -H "Content-Type: application/json" \
      -d '{"student_data":"TEST001|John|Doe|10|A|2025-09-01","card_uid":"TEST","device_id":"TEST","timestamp":"2025-10-09 14:30:00"}'
    ```

4. **Start Laravel server**:
    ```bash
    php artisan serve --host=0.0.0.0 --port=8000
    ```

## Operation Guide

### Normal Operation

1. **Power on the device**

    - LCD shows "Ready to Scan"
    - Blue LED indicates ready status
    - Current time displayed on second line

2. **Student scans RFID card**

    - Yellow LED: Reading card
    - LCD shows "Reading Card..."
    - Device reads student data from card

3. **Check-in (First scan of the day)**

    - Green LED: Success
    - Two beeps (high-pitched)
    - LCD shows "Success! Welcome!"
    - Data sent to server

4. **Check-out (Second scan of the day)**
    - Green LED: Success
    - Two beeps (high-pitched)
    - LCD shows "Success! Goodbye!"
    - Check-out time recorded

### Error States

**Red LED + Low beeps**:

-   Invalid card data
-   Card read error
-   Student not found in database
-   LCD shows specific error message

**Orange LED + Single beep**:

-   Offline mode (no WiFi)
-   Data saved to SD card for later sync
-   LCD shows "Offline Mode - Saved Locally"

### LED Color Meanings

| Color  | Meaning                      |
| ------ | ---------------------------- |
| Blue   | System ready                 |
| Yellow | Processing card              |
| Green  | Success (check-in/out)       |
| Orange | Offline mode (saved locally) |
| Red    | Error                        |

### LCD Display States

```
Line 1: Status message
Line 2: Time and date

Examples:
┌────────────────┐
│Ready to Scan   │
│14:30:45 09/10  │
└────────────────┘

┌────────────────┐
│Student: STU001 │
│Processing...   │
└────────────────┘

┌────────────────┐
│Success!        │
│Welcome!        │
└────────────────┘
```

## Offline Mode & Data Sync

### Automatic Offline Handling

When WiFi connection is lost:

1. Device automatically switches to offline mode
2. Orange LED indicates offline status
3. Attendance records saved to SD card
4. Files created:
    - `attendance.log` - Full log with timestamps
    - `pending.csv` - Records waiting to sync

### Manual Sync via Web Interface

_(To be implemented)_

1. Access device management page in Laravel admin panel
2. Click "Sync Pending Records"
3. Device uploads all pending records from SD card
4. Conflicts resolved automatically
5. SD card cleared after successful sync

### SD Card File Format

`pending.csv` format:

```csv
2025-10-09 08:15:30,STU001|John|Doe|10|A|2025-09-01,A1B2C3D4
2025-10-09 08:16:45,STU002|Jane|Smith|9|B|2025-09-01,E5F6G7H8
```

## Maintenance

### Regular Maintenance

**Daily:**

-   Verify device is online (check LCD)
-   Test with sample card

**Weekly:**

-   Check SD card storage (should auto-rotate logs)
-   Verify time accuracy on RTC

**Monthly:**

-   Clean RFID reader surface
-   Check all cable connections
-   Verify battery backup on RTC

### Time Synchronization

The RTC module maintains accurate time even when powered off (has battery backup). To update time:

1. Uncomment this line in the code:

    ```cpp
    rtc.adjust(DateTime(F(__DATE__), F(__TIME__)));
    ```

2. Upload sketch (will set time to compilation time)

3. Comment out the line again and re-upload

Or use a time sync function (can be added):

```cpp
// Get time from NTP server via WiFi
configTime(0, 0, "pool.ntp.org", "time.nist.gov");
```

### Troubleshooting

#### Device not connecting to WiFi

1. Check SSID and password
2. Ensure 2.4GHz network (ESP8266 doesn't support 5GHz)
3. Check signal strength (move closer to router)
4. Restart device
5. Check serial monitor for error messages

#### RFID cards not reading

1. Check if RFID module LED blinks when card is near
2. Verify 3.3V power supply
3. Check SPI connections
4. Try different card (ensure it's 13.56MHz MIFARE)
5. Clean reader surface

#### LCD not displaying

1. Check I2C connections (A4/SDA, A5/SCL)
2. Verify I2C address (default 0x27, may be 0x3F)
    - Use I2C scanner to find address
3. Adjust contrast potentiometer on back of LCD

#### Time incorrect

1. Replace RTC battery (CR2032)
2. Re-sync time using method above
3. Check RTC connections

#### SD card not working

1. Format as FAT32
2. Check connections
3. Try different card (max 32GB)
4. Ensure card is fully inserted

#### Server communication errors

1. Check server is running and accessible
2. Verify API URL is correct
3. Check firewall settings
4. Test API endpoint manually
5. Check Laravel logs: `storage/logs/laravel.log`

## API Reference

### Endpoint: POST /api/attendance/rfid-scan

**Request:**

```json
{
    "student_data": "STU001|John|Doe|10|A|2025-09-01",
    "card_uid": "A1B2C3D4",
    "device_id": "ATTENDANCE_READER_01",
    "timestamp": "2025-10-09 14:30:45",
    "device_time": "2025-10-09 14:30:45"
}
```

**Success Response (Check-in):**

```json
{
    "success": true,
    "action": "check_in",
    "message": "Student checked in successfully",
    "data": {
        "student_id": 1,
        "student_code": "STU001",
        "student_name": "John Doe",
        "grade": "10",
        "class": "A",
        "time": "14:30:45",
        "date": "2025-10-09",
        "is_late": false,
        "status": "present"
    }
}
```

**Success Response (Check-out):**

```json
{
    "success": true,
    "action": "check_out",
    "message": "Student checked out successfully",
    "data": {
        "student_id": 1,
        "student_code": "STU001",
        "student_name": "John Doe",
        "grade": "10",
        "class": "A",
        "check_in": "08:15:00",
        "check_out": "14:30:45",
        "date": "2025-10-09",
        "duration": "6h 15m",
        "status": "present"
    }
}
```

**Error Response:**

```json
{
    "success": false,
    "message": "Student not found in database",
    "student_code": "STU999"
}
```

## Security Considerations

### Network Security

1. **Use HTTPS** for production (requires SSL certificate):

    ```cpp
    const char* SERVER_URL = "https://yourdomain.com/api/attendance/rfid-scan";
    ```

2. **API Authentication** (recommended):

    - Generate API token in Laravel
    - Add to Arduino config:
        ```cpp
        const char* API_TOKEN = "your-secret-token-here";
        ```

3. **Network Isolation**:
    - Consider dedicated VLAN for devices
    - Firewall rules to limit access

### Physical Security

1. Mount device in secure enclosure
2. Restrict physical access to device
3. Lock SD card slot
4. Use tamper-evident seals

### Data Privacy

1. Minimal data on RFID tags (only student code + basic info)
2. Encrypted communication (HTTPS)
3. SD card data encrypted (future enhancement)
4. Regular log rotation

## Advanced Configuration

### Multiple Devices

For multiple attendance points:

1. **Unique device IDs**:

    ```cpp
    // Device at main entrance
    const char* DEVICE_ID = "MAIN_ENTRANCE";

    // Device at library
    const char* DEVICE_ID = "LIBRARY_ENTRANCE";
    ```

2. **Different locations tracked** in database

3. **Centralized monitoring** via admin panel

### Custom School Times

Adjust these in `getDeviceConfig` API response:

```php
'school_start_time' => '08:00:00',
'school_end_time' => '15:00:00',
'late_threshold' => '08:15:00',
```

### Auto-sync Schedule

Add to Arduino code:

```cpp
// Sync every 5 minutes
if (millis() - lastSyncTime > 300000) {
    syncPendingRecords();
    lastSyncTime = millis();
}
```

## Support & Resources

### Useful Links

-   **Arduino Documentation**: https://www.arduino.cc/reference
-   **MFRC522 Library**: https://github.com/miguelbalboa/rfid
-   **ESP8266 Documentation**: https://arduino-esp8266.readthedocs.io
-   **Laravel API Docs**: https://laravel.com/docs/routing#api-routes

### Getting Help

1. Check serial monitor for error messages
2. Review Laravel logs: `storage/logs/laravel.log`
3. Test components individually
4. Check GitHub issues for similar problems

## Appendix

### I2C Scanner Code

If LCD doesn't work, use this to find I2C address:

```cpp
#include <Wire.h>

void setup() {
  Wire.begin();
  Serial.begin(115200);
  Serial.println("\nI2C Scanner");
}

void loop() {
  byte error, address;
  int nDevices = 0;

  Serial.println("Scanning...");
  for(address = 1; address < 127; address++) {
    Wire.beginTransmission(address);
    error = Wire.endTransmission();

    if (error == 0) {
      Serial.print("I2C device found at address 0x");
      if (address<16) Serial.print("0");
      Serial.println(address,HEX);
      nDevices++;
    }
  }

  if (nDevices == 0)
    Serial.println("No I2C devices found\n");
  else
    Serial.println("done\n");

  delay(5000);
}
```

### Component Datasheets

-   MFRC522: https://www.nxp.com/docs/en/data-sheet/MFRC522.pdf
-   DS3231: https://datasheets.maximintegrated.com/en/ds/DS3231.pdf
-   ESP8266: https://www.espressif.com/sites/default/files/documentation/0a-esp8266ex_datasheet_en.pdf

---

## Quick Start Checklist

-   [ ] All hardware components acquired
-   [ ] All connections made according to diagram
-   [ ] Arduino IDE installed with all libraries
-   [ ] WiFi credentials configured in sketch
-   [ ] Server URL configured
-   [ ] Device ID set
-   [ ] Code uploaded successfully
-   [ ] All components initialized (check serial monitor)
-   [ ] WiFi connected
-   [ ] Test RFID card enrolled (via web interface)
-   [ ] First test scan successful
-   [ ] Attendance recorded in database

**Congratulations! Your WiFi RFID Attendance System is now operational! 🎉**
