# Arduino NFC System Architecture

## Component Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                        Web Browser                               │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │              Student Creation Form                        │   │
│  │  - Student Code, Name, Grade, Class, etc.                │   │
│  │  - [Create Student] Button                               │   │
│  └─────────────────────────────────────────────────────────┘   │
│                              │                                   │
│                              ▼                                   │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │              NFC Modal Popup                             │   │
│  │  "Put NFC Wristband to Copy Student Data"               │   │
│  │  - Animated NFC Icon                                     │   │
│  │  - Status Messages                                       │   │
│  │  - [Skip] [Cancel] [Continue] Buttons                    │   │
│  └─────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
                              │
                              │ AJAX POST /write-nfc
                              │ (JSON: student data)
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                     Laravel Backend (PHP)                        │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │           StudentController::writeToNFC()                │   │
│  │  - Validates request data                                │   │
│  │  - Calls ArduinoNFCService                               │   │
│  └─────────────────────────────────────────────────────────┘   │
│                              │                                   │
│                              ▼                                   │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │           ArduinoNFCService                              │   │
│  │  - Opens serial port                                     │   │
│  │  - Formats student data                                  │   │
│  │  - Sends commands to Arduino                             │   │
│  │  - Waits for response                                    │   │
│  │  - Returns success/error                                 │   │
│  └─────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
                              │
                              │ Serial Communication
                              │ (UART: 9600 baud)
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                  Arduino Uno + MFRC522 RFID Module               │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │         arduino_mfrc522_nfc_writer.ino                   │   │
│  │  1. Receives "WRITE_NFC" command                         │   │
│  │  2. Receives data length                                 │   │
│  │  3. Receives student data                                │   │
│  │  4. Waits for NFC tag (30 sec timeout)                   │   │
│  │  5. Writes NDEF message to tag                           │   │
│  │  6. Sends SUCCESS or ERROR response                      │   │
│  └─────────────────────────────────────────────────────────┘   │
│                              │                                   │
│                              ▼                                   │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │           MFRC522 RFID Module                            │   │
│  │  - Detects RFID tag presence                             │   │
│  │  - Writes data to MIFARE Classic blocks                  │   │
│  │  - Verifies write operation                              │   │
│  └─────────────────────────────────────────────────────────┘   │
│                                                                   │
│  Hardware:                                                        │
│  - Arduino Uno                                                    │
│  - MFRC522 RFID Reader Module (RC522)                            │
│  - USB Cable                                                      │
│                                                                   │
│  Connections:                                                     │
│  - MFRC522 SDA  → Arduino Pin 10                                 │
│  - MFRC522 SCK  → Arduino Pin 13                                 │
│  - MFRC522 MOSI → Arduino Pin 11                                 │
│  - MFRC522 MISO → Arduino Pin 12                                 │
│  - MFRC522 RST  → Arduino Pin 9                                  │
│  - MFRC522 3.3V → Arduino 3.3V (NOT 5V!)                         │
│  - MFRC522 GND  → Arduino GND                                    │
└─────────────────────────────────────────────────────────────────┘
                              │
                              │ RF Communication (13.56 MHz)
                              ▼
                    ┌──────────────────┐
                    │   RFID Card/Tag  │
                    │  (MIFARE 1KB)    │
                    │                  │
                    │  Student Data:   │
                    │  STU-00001|John| │
                    │  Doe|5|5A|Date   │
                    └──────────────────┘
```

## Data Flow Sequence

```
User        Browser         Laravel          Arduino       MFRC522      RFID Tag
 │             │               │                │              │              │
 │  Fill Form  │               │                │              │              │
 │───────────>│               │                │              │              │
 │             │               │                │              │              │
 │  Click Create               │                │              │              │
 │───────────>│               │                │              │              │
 │             │               │                │              │              │
 │             │ Show NFC Modal                 │              │              │
 │<────────────│               │                │              │              │
 │             │               │                │              │              │
 │             │ POST /write-nfc                │              │              │
 │             │──────────────>│                │              │              │
 │             │               │                │              │              │
 │             │               │ Open Serial    │              │              │
 │             │               │───────────────>│              │              │
 │             │               │                │              │              │
 │             │               │ WRITE_NFC\n    │              │              │
 │             │               │───────────────>│              │              │
 │             │               │                │              │              │
 │             │               │ Data Length\n  │              │              │
 │             │               │───────────────>│              │              │
 │             │               │                │              │              │
 │             │               │ Student Data\n │              │              │
 │             │               │───────────────>│              │              │
 │             │               │                │              │              │
 │             │               │                │ Wait for Tag │              │
 │             │               │                │─────────────>│              │
 │             │               │                │              │              │
 │             │               │                │              │ Tag Detected │
 │             │               │                │              │<─────────────│
 │             │               │                │              │              │
 │             │               │                │ Authenticate │              │
 │             │               │                │─────────────>│              │
 │             │               │                │              │              │
 │             │               │                │              │ Write Data   │
 │             │               │                │              │─────────────>│
 │             │               │                │              │              │
 │             │               │                │              │ Write OK     │
 │             │               │                │              │<─────────────│
 │             │               │                │              │              │
 │             │               │                │ SUCCESS      │              │
 │             │               │                │<─────────────│              │
 │             │               │                │              │              │
 │             │               │ Close Serial   │              │              │
 │             │               │<───────────────│              │              │
 │             │               │                │              │              │
 │             │ JSON Response │                │              │              │
 │             │<──────────────│                │              │              │
 │             │               │                │              │              │
 │  Show Success               │                │              │              │
 │<────────────│               │                │              │              │
 │             │               │                │              │              │
 │  Submit Form                │                │              │              │
 │─────────────────────────────>                │              │              │
```

## File Structure

```
project-root/
│
├── app/
│   ├── Http/Controllers/Admin/Management/
│   │   └── StudentController.php           # Added: writeToNFC(), testArduino()
│   │
│   └── Services/
│       └── ArduinoNFCService.php          # NEW: Serial communication service
│
├── resources/
│   ├── js/admin/
│   │   └── student-form.js                # Modified: AJAX instead of Web NFC
│   │
│   └── views/admin/pages/management/students/
│       └── form.blade.php                 # Added: NFC modal
│
├── routes/
│   └── web.php                            # Added: write-nfc, test-arduino routes
│
├── arduino_nfc_writer.ino                 # NEW: Arduino sketch
│
├── ARDUINO_NFC_SETUP.md                   # NEW: Full documentation
├── QUICK_START_ARDUINO.md                 # NEW: Quick start guide
├── ARDUINO_IMPLEMENTATION_SUMMARY.md      # NEW: Technical summary
├── README_ARDUINO_NFC.md                  # NEW: Feature README
│
├── test-arduino-setup.sh                  # NEW: Test script
└── .env.arduino.example                   # NEW: Config example
```

## Communication Protocol

### Commands (PHP → Arduino)

1. **PING** - Test connection

    ```
    Request:  PING\n
    Response: PONG\n
    ```

2. **WRITE_NFC** - Write data to tag

    ```
    Request:  WRITE_NFC\n
             [data_length]\n
             [student_data]\n

    Response: SUCCESS: Data written to NFC tag\n
             or
             ERROR: [error message]\n
             or
             TIMEOUT: No NFC tag detected\n
    ```

### Data Format

**Student Data String:**

```
Format: FIELD1|FIELD2|FIELD3|FIELD4|FIELD5|FIELD6
Example: STU-00001|John|Doe|5|5A|2025-10-07

Fields:
1. Student Code
2. First Name
3. Last Name
4. Grade Level
5. Class ID
6. Enrollment Date
```

**NDEF Message Structure:**

```
[0x03]              # NDEF Message TLV
[length]            # Total length
[0xD1]              # NDEF Record Header
[0x01]              # Type Length
[payload_length]    # Payload Length
[0x54]              # Type: "T" (Text)
[0x02]              # Status: UTF-8, lang=2
[0x65, 0x6E]        # Language: "en"
[...data...]        # Actual student data
[0xFE]              # Terminator TLV
```

## Hardware Wiring

### I2C Configuration (Recommended)

```
  Arduino Uno/Nano          PN532 Module
  ┌──────────────┐         ┌────────────┐
  │              │         │            │
  │    5V    ●───┼────────┼──● VCC     │
  │    GND   ●───┼────────┼──● GND     │
  │    A4    ●───┼────────┼──● SDA     │
  │    A5    ●───┼────────┼──● SCL     │
  │              │         │            │
  └──────────────┘         └────────────┘
```

### SPI Configuration (Alternative)

```
  Arduino Uno/Nano          PN532 Module
  ┌──────────────┐         ┌────────────┐
  │              │         │            │
  │    5V    ●───┼────────┼──● VCC     │
  │    GND   ●───┼────────┼──● GND     │
  │    D13   ●───┼────────┼──● SCK     │
  │    D12   ●───┼────────┼──● MISO    │
  │    D11   ●───┼────────┼──● MOSI    │
  │    D10   ●───┼────────┼──● SS      │
  │              │         │            │
  └──────────────┘         └────────────┘
```

## Error Handling Flow

```
                    ┌─────────────┐
                    │  Write NFC  │
                    └──────┬──────┘
                           │
                           ▼
                  ┌────────────────┐
                  │ Open Serial    │
                  │ Port           │
                  └────┬───────────┘
                       │
              ┌────────┴────────┐
              │                 │
         Success            Failure
              │                 │
              ▼                 ▼
      ┌───────────────┐  ┌──────────────┐
      │ Send Command  │  │ Return Error │
      └───────┬───────┘  │ "Cannot      │
              │          │  connect"    │
              ▼          └──────────────┘
      ┌───────────────┐
      │ Wait for Tag  │
      └───────┬───────┘
              │
     ┌────────┴─────────┐
     │                  │
  Found            Timeout
     │                  │
     ▼                  ▼
┌──────────┐    ┌──────────────┐
│ Write    │    │ Return Error │
│ Data     │    │ "Tag not     │
└────┬─────┘    │  detected"   │
     │          └──────────────┘
┌────┴─────┐
│          │
Success  Failure
│          │
▼          ▼
┌────────────┐  ┌──────────────┐
│ Return     │  │ Return Error │
│ Success    │  │ "Write       │
└────────────┘  │  failed"     │
                └──────────────┘
```

## System Requirements

### Server Side

-   PHP 8.2+
-   Laravel 10+
-   Serial port access
-   Read/write permissions

### Hardware

-   Arduino (Uno/Mega/Nano)
-   PN532 NFC Module
-   USB cable
-   NFC tags (NTAG213/215/216)

### Client Side

-   Modern web browser
-   JavaScript enabled
-   Network connection to server
