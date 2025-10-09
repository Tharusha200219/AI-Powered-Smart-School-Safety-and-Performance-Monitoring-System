# Arduino MFRC522/RC522 RFID Writer Setup Guide

## � Overview

This guide shows you how to set up your **Arduino Uno** with **MFRC522 (RC522) RFID module** to write student data to NFC tags/cards for the Smart School Safety System.

## 📦 Hardware Setup

### Components

-   **Arduino Uno**
-   **MFRC522 RFID Reader Module**
-   **RFID Tags/Cards** (Mifare Classic 1K recommended)
-   **USB Cable**

### Wiring Connections

```
MFRC522 Pin    Arduino Pin
-----------    -----------
SDA (SS)   →   Digital 10
SCK        →   Digital 13
MOSI       →   Digital 11
MISO       →   Digital 12
IRQ        →   Not connected
GND        →   GND
RST        →   Digital 9
3.3V       →   3.3V (NOT 5V!)
```

⚠️ **IMPORTANT**: MFRC522 operates at 3.3V. DO NOT connect to 5V or you may damage it!

## 🚀 Quick Setup Steps

### Step 1: Install Arduino Library

1. Open Arduino IDE
2. Go to **Sketch** → **Include Library** → **Manage Libraries**
3. Search for **"MFRC522"**
4. Install **"MFRC522 by GithubCommunity"**

### Step 2: Upload New Arduino Code

1. Open `arduino_mfrc522_nfc_writer.ino` in Arduino IDE
2. Select your board: **Tools** → **Board** → **Arduino Uno**
3. Select your port: **Tools** → **Port** → **/dev/cu.usbserial-110**
4. Click **Upload** button
5. Wait for "Done uploading" message

### Step 3: Verify Arduino is Working

1. Open **Tools** → **Serial Monitor**
2. Set baud rate to **9600**
3. You should see: `READY`
4. Type `PING` and press Enter
5. You should see: `PONG`

### Step 4: Configure Laravel

Your `.env` is already updated with:

```env
ARDUINO_SERIAL_PORT=/dev/cu.usbserial-110
ARDUINO_BAUD_RATE=9600
ARDUINO_TIMEOUT=30
```

### Step 5: Test the Connection

Run the test script:

```bash
./test_arduino_connection.sh
```

## 🧪 Testing the System

### Method 1: Command Line Test

```bash
php artisan tinker
```

Then in tinker:

```php
$service = new \App\Services\ArduinoNFCService();
$result = $service->testConnection();
print_r($result);
```

### Method 2: Create a Student

1. Go to **Management** → **Students** → **Create Student**
2. Fill in student details
3. When you click **Create Student**, place RFID tag on reader
4. Wait for success/error message

## 📝 What Changed in the New Arduino Code

### Improvements:

1. **Better Response Protocol**

    - `READY` - Arduino is ready
    - `PONG` - Response to PING
    - `INFO: message` - Progress updates
    - `SUCCESS` - Write completed
    - `ERROR: message` - Specific error
    - `TIMEOUT: message` - Tag not detected

2. **Proper MIFARE Authentication**

    - Uses correct key structure
    - Authenticates before each block write
    - Handles trailer blocks correctly

3. **Smart Block Management**

    - Skips reserved blocks (0-3)
    - Skips trailer blocks (every 4th block)
    - Uses blocks 4-63 for data storage

4. **Better Error Handling**
    - Specific error messages
    - Connection status monitoring
    - Timeout detection

## 🐛 Troubleshooting

### Arduino Doesn't Respond

**Check:**

1. USB cable is connected
2. Arduino has power (LED should be on)
3. Correct port in `.env`
4. No other program using the serial port (close Arduino IDE Serial Monitor)

**Try:**

```bash
# List all USB devices
ls -la /dev/cu.*

# Test manual connection
screen /dev/cu.usbserial-110 9600
# Type: PING
# Should see: PONG
# Exit: Ctrl+A, then K, then Y
```

### "Maximum execution time exceeded"

This means Arduino isn't responding. Check:

1. **Arduino IDE Serial Monitor is closed** (can't have two programs on one port)
2. **Correct Arduino sketch uploaded**
3. **Serial port is correct**

Check logs:

```bash
tail -f storage/logs/laravel.log
```

### "No tag detected within timeout period"

1. **Place RFID tag on reader** - Must be close (within 3cm)
2. **Use compatible tag** - Mifare Classic 1K works best
3. **Tag isn't damaged** - Try a different tag
4. **Wiring is correct** - Check all connections

### "Write failed for block X"

1. **Tag is write-protected** - Try a different tag
2. **Tag moved during write** - Hold tag steady
3. **Poor connection** - Check wiring

## 📊 Reading the Logs

Check Laravel logs for debugging:

```bash
tail -f storage/logs/laravel.log
```

You'll see messages like:

-   `Arduino NFC Service - Waiting for response`
-   `Arduino NFC Service - Received: INFO: Waiting for RFID tag...`
-   `Arduino NFC Service - Received: INFO: Tag detected, writing data...`
-   `Arduino NFC Service - Success response received`

## 🔍 Testing Individual Components

### Test 1: Arduino Serial Communication

```bash
# Install screen if needed: brew install screen
screen /dev/cu.usbserial-110 9600
```

Type: `PING`
Expected: `PONG`

### Test 2: RFID Tag Detection

In Arduino IDE Serial Monitor:

1. Type: `STATUS`
2. Place RFID tag on reader
3. Should see activity

### Test 3: Laravel Service

```bash
php artisan tinker
```

```php
$service = new \App\Services\ArduinoNFCService();
$result = $service->testConnection();
dd($result);
```

## 📚 Additional Notes

### Supported RFID Tags

-   ✅ Mifare Classic 1K (recommended)
-   ✅ Mifare Classic 4K
-   ✅ NTAG213/215/216 (may need code modification)
-   ❌ Mifare Ultralight (different write command needed)
-   ❌ Mifare DESFire (not supported by MFRC522)

### Data Storage

-   Each student's data is stored across multiple 16-byte blocks
-   Format: JSON string with student details
-   Blocks 4-63 are available (236 bytes usable space)

### Security Notes

-   Default MIFARE key is used (0xFF × 6)
-   For production, consider implementing custom keys
-   Data on tags is NOT encrypted

## 🎯 Next Steps

1. **Upload the new Arduino sketch** (`arduino_mfrc522_nfc_writer.ino`)
2. **Run the test script** (`./test_arduino_connection.sh`)
3. **Test creating a student** in the web interface
4. **Check the logs** if any issues occur

## 📞 Still Having Issues?

1. Check all wiring connections
2. Verify Arduino sketch is uploaded correctly
3. Make sure Serial Monitor is closed
4. Try a different USB port
5. Check the logs: `storage/logs/laravel.log`

The new code includes extensive logging, so you'll be able to see exactly where the process fails.
