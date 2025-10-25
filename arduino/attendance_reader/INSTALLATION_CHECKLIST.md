# Installation Checklist - WiFi Attendance System

Print this checklist and check off each item as you complete it.

## 📦 Phase 1: Hardware Acquisition

### WiFi Attendance Reader Components

-   [ ] UNO+WiFi R3 ATmega328P+ESP8266 (32Mb flash, USB-TTL CH340G)
-   [ ] MFRC522 RFID Reader Module (RC522 13.56MHz)
-   [ ] RFID tags/cards (pack of 10-50)
-   [ ] LCD1602 I2C Display Module (Blue or Green Screen, PCF8574)
-   [ ] DS3231 Real Time Clock Module (with CR2032 battery)
-   [ ] Micro SD Card Module TF Card Adapter
-   [ ] Micro SD Card (4GB-32GB, Class 10)
-   [ ] 5mm RGB LED (Common Cathode)
-   [ ] Buzzer module (5V)
-   [ ] 3x 220Ω resistors (for RGB LED)
-   [ ] Breadboard (full size)
-   [ ] Jumper wires (male-to-male, male-to-female)
-   [ ] USB cable (Micro-USB for Arduino)
-   [ ] 5V power supply (2A recommended)

### RFID Writer Components (If not already setup)

-   [ ] Arduino UNO (or compatible)
-   [ ] MFRC522 RFID Reader Module
-   [ ] USB cable
-   [ ] Breadboard and jumper wires

**Total Estimated Cost**: $30-40 USD

## 💻 Phase 2: Software Installation

### On Your Computer

-   [ ] Arduino IDE installed (v1.8.19 or later)
-   [ ] ESP8266 board support installed
-   [ ] CH340G USB driver installed (if needed for your OS)
-   [ ] Git installed (for cloning repository)
-   [ ] Text editor (VS Code, Sublime, etc.)

### Arduino Libraries

-   [ ] MFRC522 library (v1.4.10+)
-   [ ] LiquidCrystal_I2C library (v1.1.2+)
-   [ ] RTClib by Adafruit (v2.1.1+)
-   [ ] ArduinoJson library (v6.21.3+)
-   [ ] SD library (built-in)
-   [ ] SPI library (built-in)
-   [ ] Wire library (built-in)
-   [ ] ESP8266WiFi (with ESP8266 board support)
-   [ ] ESP8266HTTPClient (with ESP8266 board support)

### Server Requirements

-   [ ] PHP 8.1 or higher installed
-   [ ] Composer installed
-   [ ] MySQL/MariaDB installed and running
-   [ ] Laravel project setup completed
-   [ ] Database migrated
-   [ ] Web server accessible on local network

## 🔌 Phase 3: Hardware Assembly

### Power Check

-   [ ] Verified 5V power supply working
-   [ ] Verified 3.3V rail on breadboard
-   [ ] Tested with multimeter

### RFID Module

-   [ ] Connected RST → Pin 9
-   [ ] Connected SS → Pin 10
-   [ ] Connected MOSI → Pin 11
-   [ ] Connected MISO → Pin 12
-   [ ] Connected SCK → Pin 13
-   [ ] Connected 3.3V → 3.3V (⚠️ NOT 5V!)
-   [ ] Connected GND → GND

### LCD Display

-   [ ] Connected SDA → A4
-   [ ] Connected SCL → A5
-   [ ] Connected VCC → 5V
-   [ ] Connected GND → GND
-   [ ] Adjusted contrast potentiometer

### RTC Module

-   [ ] Inserted CR2032 battery
-   [ ] Connected SDA → A4 (shared with LCD)
-   [ ] Connected SCL → A5 (shared with LCD)
-   [ ] Connected VCC → 5V
-   [ ] Connected GND → GND

### SD Card Module

-   [ ] Formatted SD card as FAT32
-   [ ] Connected CS → Pin 4
-   [ ] Connected MOSI → Pin 11 (shared with RFID)
-   [ ] Connected MISO → Pin 12 (shared with RFID)
-   [ ] Connected SCK → Pin 13 (shared with RFID)
-   [ ] Connected VCC → 5V
-   [ ] Connected GND → GND
-   [ ] Inserted SD card into module

### RGB LED

-   [ ] Connected Red pin → Pin 6 (with 220Ω resistor)
-   [ ] Connected Green pin → Pin 5 (with 220Ω resistor)
-   [ ] Connected Blue pin → Pin 3 (with 220Ω resistor)
-   [ ] Connected Cathode (longest pin) → GND
-   [ ] Verified common cathode type

### Buzzer

-   [ ] Connected Positive → Pin 8
-   [ ] Connected Negative → GND

### Final Checks

-   [ ] All connections secure
-   [ ] No loose wires
-   [ ] No short circuits visible
-   [ ] Power rails correctly connected
-   [ ] Documented any pin changes

## ⚙️ Phase 4: Software Configuration

### Network Information Gathering

-   [ ] WiFi SSID written down: ************\_\_\_************
-   [ ] WiFi password written down: ************\_\_\_************
-   [ ] Server IP address found: ************\_\_\_************
-   [ ] Server port noted (usually 8000): ************\_\_\_************

### Arduino Code Configuration

-   [ ] Opened `arduino_wifi_attendance_reader.ino`
-   [ ] Changed WIFI_SSID (line 76)
-   [ ] Changed WIFI_PASSWORD (line 77)
-   [ ] Changed SERVER_URL (line 80)
-   [ ] Changed DEVICE_ID (line 84)
-   [ ] Saved file

### Board Configuration

-   [ ] Selected correct board in Arduino IDE
-   [ ] Selected correct COM port
-   [ ] Set flash size: 4MB (FS:2MB OTA:~1019KB)
-   [ ] Set upload speed: 115200
-   [ ] Set CPU frequency: 80 MHz

### Code Upload

-   [ ] Connected Arduino via USB
-   [ ] Clicked Upload button
-   [ ] Waited for "Done uploading"
-   [ ] No errors in console

## 🧪 Phase 5: Testing

### Hardware Tests

-   [ ] Opened Serial Monitor (115200 baud)
-   [ ] Saw "WiFi RFID Attendance System" banner
-   [ ] ✅ RFID initialized (version shown)
-   [ ] ✅ RTC initialized (time shown)
-   [ ] ✅ SD Card initialized
-   [ ] ✅ WiFi connected (IP address shown)
-   [ ] ✅ "System Ready" message displayed

### LCD Display Test

-   [ ] LCD backlight on
-   [ ] Line 1 shows "Ready to Scan"
-   [ ] Line 2 shows current time
-   [ ] Time updating every second
-   [ ] Text clear and readable

### LED Test

-   [ ] Blue LED lit briefly on startup
-   [ ] All colors work (red, green, blue)
-   [ ] LED turns off when ready

### RFID Test

-   [ ] Placed test card near reader
-   [ ] Yellow LED lit (processing)
-   [ ] LCD showed "Reading Card..."
-   [ ] Serial monitor showed card UID

### Network Test

-   [ ] Pinged server from Arduino's IP
-   [ ] Server responded to ping
-   [ ] Tested API endpoint with curl/Postman

## 🎓 Phase 6: Student Enrollment

### Writer Device Setup

-   [ ] Writer device connected via USB
-   [ ] Laravel server running
-   [ ] Admin panel accessible

### Test Enrollment

-   [ ] Created test student in admin panel
-   [ ] Clicked "Create Student" button
-   [ ] Placed RFID card on writer
-   [ ] Saw success message
-   [ ] Card data written successfully

### Verification

-   [ ] Test card recorded in database
-   [ ] Student code matches
-   [ ] Student data correct

## ✅ Phase 7: Attendance Testing

### First Scan (Check-in)

-   [ ] Placed enrolled card on WiFi reader
-   [ ] Yellow LED → Green LED
-   [ ] Two beeps heard
-   [ ] LCD showed "Success! Welcome!"
-   [ ] Serial monitor: "Student checked in"
-   [ ] Admin panel shows check-in time
-   [ ] Check-in time accurate

### Second Scan (Check-out)

-   [ ] Waited 3+ seconds
-   [ ] Placed same card again
-   [ ] Green LED and beeps
-   [ ] LCD showed "Success! Goodbye!"
-   [ ] Serial monitor: "Student checked out"
-   [ ] Admin panel shows check-out time
-   [ ] Duration calculated correctly

### Error Handling

-   [ ] Tried unregistered card → Red LED + error message
-   [ ] Tried duplicate scan within 3 seconds → Prevented
-   [ ] Tried third scan same day → "Already checked out"

## 🌐 Phase 8: Offline Mode Testing

### Disconnect Network

-   [ ] Unplugged WiFi router / disabled network
-   [ ] Scanned student card
-   [ ] Orange LED displayed
-   [ ] LCD showed "Offline Mode - Saved Locally"
-   [ ] Serial monitor: "No WiFi connection"

### Check SD Card

-   [ ] Removed SD card from device
-   [ ] Inserted into computer
-   [ ] Opened `attendance.log` file
-   [ ] Verified scan recorded with timestamp
-   [ ] Opened `pending.csv` file
-   [ ] Verified scan in sync queue

### Reconnect and Sync

-   [ ] Reconnected network
-   [ ] Device reconnected to WiFi
-   [ ] Uploaded pending records via API
-   [ ] Verified records in database
-   [ ] SD card pending file cleared

## 🔒 Phase 9: Production Deployment

### Physical Installation

-   [ ] Mounted device in secure location
-   [ ] At appropriate height for scanning
-   [ ] Protected from weather (if outdoor)
-   [ ] Power cable secured
-   [ ] SD card slot accessible but secure

### Server Configuration

-   [ ] Changed to production domain/IP
-   [ ] HTTPS configured (if required)
-   [ ] API authentication enabled (if required)
-   [ ] Firewall rules configured
-   [ ] CORS settings correct

### Documentation

-   [ ] Printed this checklist for reference
-   [ ] Saved device configuration details
-   [ ] Documented any custom changes
-   [ ] Created device label with ID and location

### User Training

-   [ ] Admin trained on enrollment process
-   [ ] Staff trained on manual attendance
-   [ ] Students briefed on how to use system
-   [ ] Troubleshooting guide provided

## 📋 Phase 10: Final Verification

### 24-Hour Test

-   [ ] System running for 24 hours
-   [ ] No disconnections
-   [ ] Time accurate
-   [ ] All scans recorded
-   [ ] Dashboard updated correctly

### Load Testing

-   [ ] Tested with multiple students
-   [ ] Peak time performance acceptable
-   [ ] No duplicate entries
-   [ ] All scans within 2 seconds

### Backup & Recovery

-   [ ] Database backed up
-   [ ] SD card data backed up
-   [ ] Spare RFID cards available
-   [ ] Spare hardware components on hand

## 🎉 Completion

**System Status**:

-   [ ] ✅ Fully Operational
-   [ ] ⚠️ Operational with minor issues
-   [ ] ❌ Not operational (see issues below)

**Issues Found**:

---

---

---

**Date Completed**: ********\_\_\_\_********

**Installed By**: ********\_\_\_\_********

**Sign-off**: ********\_\_\_\_********

---

## 📞 Emergency Contacts

**System Administrator**: ********\_\_\_\_********  
**Phone**: ********\_\_\_\_********  
**Email**: ********\_\_\_\_********

**Technical Support**: ********\_\_\_\_********  
**Phone**: ********\_\_\_\_********  
**Email**: ********\_\_\_\_********

## 🔧 Maintenance Schedule

**Daily Check**: ********\_\_\_\_********  
**Weekly Maintenance**: ********\_\_\_\_********  
**Monthly Review**: ********\_\_\_\_********

---

**🎊 Congratulations! Your WiFi RFID Attendance System is now live!**

Keep this checklist for future reference and troubleshooting.
