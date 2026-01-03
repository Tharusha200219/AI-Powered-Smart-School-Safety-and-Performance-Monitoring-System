# WiFi RFID Attendance Reader - Wiring Diagram

## 📐 Visual Connection Guide

This document provides clear wiring diagrams for connecting all components to your UNO+WiFi R3 board.

## ⚠️ CRITICAL WARNINGS

1. **MFRC522 RFID operates at 3.3V ONLY** - connecting to 5V will damage it!
2. **Always disconnect power before making changes**
3. **Double-check polarity on LEDs and electrolytic capacitors**
4. **Use 220Ω resistors with RGB LED**

## 🔌 Complete Pin Assignment Table

| Component          | Pin Name    | Arduino Pin | Notes                      |
| ------------------ | ----------- | ----------- | -------------------------- |
| **MFRC522 RFID**   |             |             |                            |
|                    | RST         | Digital 9   | Reset                      |
|                    | SS          | Digital 10  | Slave Select               |
|                    | MOSI        | Digital 11  | Shared with SD             |
|                    | MISO        | Digital 12  | Shared with SD             |
|                    | SCK         | Digital 13  | Shared with SD             |
|                    | 3.3V        | 3.3V        | **NOT 5V!**                |
|                    | GND         | GND         |                            |
| **LCD1602 I2C**    |             |             |                            |
|                    | SDA         | A4          | I2C Data, shared with RTC  |
|                    | SCL         | A5          | I2C Clock, shared with RTC |
|                    | VCC         | 5V          |                            |
|                    | GND         | GND         |                            |
| **DS3231 RTC**     |             |             |                            |
|                    | SDA         | A4          | I2C Data, shared with LCD  |
|                    | SCL         | A5          | I2C Clock, shared with LCD |
|                    | VCC         | 5V          |                            |
|                    | GND         | GND         |                            |
| **SD Card Module** |             |             |                            |
|                    | CS          | Digital 4   | Chip Select                |
|                    | MOSI        | Digital 11  | Shared with RFID           |
|                    | MISO        | Digital 12  | Shared with RFID           |
|                    | SCK         | Digital 13  | Shared with RFID           |
|                    | VCC         | 5V          |                            |
|                    | GND         | GND         |                            |
| **RGB LED**        |             |             |                            |
|                    | Red Anode   | Digital 6   | Through 220Ω               |
|                    | Green Anode | Digital 5   | Through 220Ω               |
|                    | Blue Anode  | Digital 3   | Through 220Ω               |
|                    | Cathode     | GND         | Longest pin                |
| **Buzzer**         |             |             |                            |
|                    | Positive    | Digital 8   |                            |
|                    | Negative    | GND         |                            |

## 🎨 ASCII Wiring Diagram

```
                        UNO+WiFi R3 ATmega328P+ESP8266
                    ╔═══════════════════════════════════╗
                    ║                                   ║
                    ║  ┌─────────────────────────────┐ ║
                    ║  │    USB Programming Port     │ ║
                    ║  └─────────────────────────────┘ ║
                    ║                                   ║
    A5/SCL ◄────────╫─────┐                             ║
    A4/SDA ◄────────╫─────┼─────┐                       ║
                    ║     │     │                       ║
                    ║   I2C   I2C                       ║
                    ║   LCD   RTC                       ║
                    ║                                   ║
    D13/SCK ◄───────╫─────┬─────┬───────────────┐       ║
    D12/MISO ◄──────╫─────┼─────┼───────────┐   │       ║
    D11/MOSI ◄──────╫─────┼─────┼───────┐   │   │       ║
    D10/SS ◄────────╫─────┤     │       │   │   │       ║
    D9/RST ◄────────╫───┐ │     │       │   │   │       ║
                    ║   │ │     │       │   │   │       ║
                    ║ RFID   SD Card    │   │   │       ║
                    ║ RC522  Module     │   │   │       ║
                    ║                   │   │   │       ║
    D8 ─────────────╫───────────────────┼───┼───┼─ Buzzer
    D6 ─────────────╫───[220Ω]─ Red LED │   │   │       ║
    D5 ─────────────╫───[220Ω]─ Green LED   │   │       ║
    D4 ─────────────╫─────────────────────┐ │   │       ║
    D3 ─────────────╫───[220Ω]─ Blue LED  │ │   │       ║
                    ║                     │ │   │       ║
                    ║                    SD CS  │       ║
                    ║                           │       ║
    3.3V ───────────╫───────────────────────────┼─ RFID ║
    5V ─────────────╫─── LCD ─── RTC ─── SD ────┘       ║
    GND ────────────╫─── All Components                 ║
                    ║                                   ║
                    ╚═══════════════════════════════════╝
```

## 📊 Breadboard Layout

```
Power Rails:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
(+) 5V  ●─●─●─●─●─●─●─●─●─●─●─●─●─●─●─●─●─●─●  (From Arduino 5V)
(-) GND ●─●─●─●─●─●─●─●─●─●─●─●─●─●─●─●─●─●─●  (From Arduino GND)

3.3V Rail (for RFID only):
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
(+) 3.3V ●─●─●─●─●  (From Arduino 3.3V)

Component Placement:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

[LCD1602 I2C Module]
 GND VCC SDA SCL
  │   │   │   │
  └───┼───┼───┼───────── To Power Rails
      │   │   └─────────── A5
      │   └─────────────── A4
      └─────────────────── 5V

[MFRC522 RFID]
 RST SS MOSI MISO SCK 3.3V GND
  │   │   │    │   │   │   │
  9  10  11   12  13  └───┴─── To Power Rails (3.3V!)

[DS3231 RTC]
 VCC GND SCL SDA
  │   │   │   │
  5V GND A5  A4 ──────────── Shared I2C with LCD

[SD Card Module]
 VCC GND MOSI MISO SCK CS
  │   │   │    │   │   │
  5V GND 11   12  13   4

[RGB LED] (Common Cathode)

    R   G   B   (-)
    │   │   │   │
   220Ω 220Ω 220Ω │
    │   │   │   │
    6   5   3  GND

[Buzzer]
 (+) (-)
  │   │
  8  GND
```

## 🔍 Connection Details by Component

### 1. MFRC522 RFID Reader Module

**Connection Steps:**

1. Identify 3.3V rail on breadboard (separate from 5V!)
2. Connect RFID 3.3V pin to 3.3V rail
3. Connect RFID GND to GND rail
4. Connect SPI pins:
    - RST → D9
    - SS → D10
    - MOSI → D11 (will be shared with SD)
    - MISO → D12 (will be shared with SD)
    - SCK → D13 (will be shared with SD)

**Testing:**

```cpp
// In setup(), should see version
Serial.println(mfrc522.PCD_ReadRegister(mfrc522.VersionReg), HEX);
// Should print: 0x92 (or 0x91 for older versions)
```

### 2. LCD1602 I2C Display

**Connection Steps:**

1. Connect LCD VCC to 5V rail
2. Connect LCD GND to GND rail
3. Connect LCD SDA to A4
4. Connect LCD SCL to A5
5. Adjust contrast using potentiometer on back (use small screwdriver)

**Finding I2C Address:**
If LCD doesn't work, run I2C scanner:

```cpp
// Common addresses: 0x27 or 0x3F
Wire.beginTransmission(address);
if (Wire.endTransmission() == 0) {
  Serial.print("Found at 0x");
  Serial.println(address, HEX);
}
```

### 3. DS3231 RTC Module

**Connection Steps:**

1. Insert CR2032 battery (flat side visible)
2. Connect RTC VCC to 5V rail
3. Connect RTC GND to GND rail
4. Connect RTC SDA to A4 (shared with LCD)
5. Connect RTC SCL to A5 (shared with LCD)

**Note:** RTC and LCD share the I2C bus - this is normal!

### 4. SD Card Module

**Connection Steps:**

1. Format SD card as FAT32 on computer
2. Insert SD card into module
3. Connect module VCC to 5V rail
4. Connect module GND to GND rail
5. Connect SPI pins:
    - MOSI → D11 (shared with RFID)
    - MISO → D12 (shared with RFID)
    - SCK → D13 (shared with RFID)
    - CS → D4 (unique to SD card)

**Note:** SD and RFID share SPI bus, CS pin selects which device

### 5. RGB LED (Common Cathode)

**LED Pinout:**

```
Looking at LED from bottom:
Longest pin = Cathode (Ground)
Three shorter pins = R, G, B anodes

     ┌─┐
    ╱   ╲
   │  ◉  │  ← LED lens
    ╲   ╱
     └┬┘
  R G(-) B
  │ │ │  │
```

**Connection Steps:**

1. Identify cathode (longest pin) and connect to GND
2. Connect each anode through 220Ω resistor:
    - Red → 220Ω → D6
    - Green → 220Ω → D5
    - Blue → 220Ω → D3

**Testing Colors:**

```cpp
// Red
digitalWrite(6, HIGH); digitalWrite(5, LOW); digitalWrite(3, LOW);
// Green
digitalWrite(6, LOW); digitalWrite(5, HIGH); digitalWrite(3, LOW);
// Blue
digitalWrite(6, LOW); digitalWrite(5, LOW); digitalWrite(3, HIGH);
```

### 6. Buzzer

**Connection Steps:**

1. Identify polarity (+ marking or longer pin)
2. Connect positive to D8
3. Connect negative to GND

**Testing:**

```cpp
tone(8, 1000, 500);  // 1kHz for 500ms
```

## 🔋 Power Considerations

### Current Draw (Approximate)

| Component                 | Current | Voltage |
| ------------------------- | ------- | ------- |
| UNO+WiFi (ESP8266 active) | ~200mA  | 5V      |
| MFRC522 RFID              | ~50mA   | 3.3V    |
| LCD1602 (with backlight)  | ~80mA   | 5V      |
| DS3231 RTC                | ~1mA    | 5V      |
| SD Card (active)          | ~50mA   | 5V      |
| RGB LED (all colors)      | ~60mA   | 5V      |
| Buzzer                    | ~30mA   | 5V      |
| **Total Peak**            | ~470mA  | 5V      |

**Recommended:** 5V 2A power supply for reliable operation

### Power Supply Options

**Option 1: USB Power (Programming/Testing)**

-   Use USB cable from computer
-   Sufficient for testing
-   Not recommended for permanent installation

**Option 2: Wall Adapter (Production)**

-   5V 2A USB wall adapter
-   Reliable for 24/7 operation
-   Recommended for deployment

**Option 3: Power Bank (Portable)**

-   5V 2A power bank
-   Good for temporary/mobile setups
-   Ensure auto-shutoff disabled

## 🧪 Testing Each Component

### Test Sequence

**1. Power Test**

```
Upload: Blink sketch
Check: LED blinks
Result: Power OK
```

**2. RFID Test**

```
Upload: RFID version check
Check: Serial shows 0x92
Result: RFID working
```

**3. LCD Test**

```
Upload: LCD "Hello World"
Check: Text visible
Adjust: Contrast if needed
Result: LCD working
```

**4. RTC Test**

```
Upload: RTC time print
Check: Serial shows time
Set: Time if needed
Result: RTC working
```

**5. SD Card Test**

```
Upload: SD card info
Check: Serial shows card size
Format: If not recognized
Result: SD working
```

**6. LED Test**

```
Upload: RGB color cycle
Check: All colors show
Result: LED working
```

**7. Buzzer Test**

```
Upload: Tone test
Check: Beep heard
Result: Buzzer working
```

**8. WiFi Test**

```
Upload: WiFi connection
Check: Serial shows IP
Result: WiFi working
```

**9. Full System Test**

```
Upload: Attendance reader
Check: All components initialize
Test: Scan RFID card
Result: System operational
```

## 🔧 Troubleshooting Connection Issues

### RFID Not Working

-   [ ] Verify 3.3V connection (use multimeter)
-   [ ] Check RST is connected to D9
-   [ ] Verify SS is D10
-   [ ] Check SPI connections (11, 12, 13)
-   [ ] Try different RFID module
-   [ ] Clean contacts with isopropyl alcohol

### LCD Not Showing Text

-   [ ] Adjust contrast potentiometer
-   [ ] Verify I2C address (0x27 or 0x3F)
-   [ ] Check SDA (A4) and SCL (A5) connections
-   [ ] Try different LCD module
-   [ ] Run I2C scanner

### RTC Wrong Time

-   [ ] Replace CR2032 battery
-   [ ] Re-sync time in setup code
-   [ ] Check I2C connections (shared with LCD)

### SD Card Not Detected

-   [ ] Format card as FAT32
-   [ ] Try different SD card (max 32GB)
-   [ ] Check CS connection to D4
-   [ ] Verify SPI connections
-   [ ] Ensure card fully inserted

### LED Not Lighting

-   [ ] Check cathode to GND
-   [ ] Verify 220Ω resistors installed
-   [ ] Test each color individually
-   [ ] Check pin connections (3, 5, 6)
-   [ ] Try different LED

### Buzzer Not Beeping

-   [ ] Check polarity (+/-)
-   [ ] Verify D8 connection
-   [ ] Try different buzzer
-   [ ] Check code has tone() calls

### WiFi Not Connecting

-   [ ] Verify SSID and password in code
-   [ ] Check 2.4GHz network (ESP8266 limitation)
-   [ ] Move closer to router
-   [ ] Check serial monitor for errors

## 📸 Photos Reference Points

When assembling, take photos at these stages:

1. Empty breadboard with power rails connected
2. After placing each component
3. After all connections complete (for reference)
4. Close-up of each module's connections
5. Overall view of complete system

## ✅ Final Connection Checklist

Before powering on:

-   [ ] All components firmly seated in breadboard
-   [ ] No loose wires
-   [ ] RFID connected to 3.3V (NOT 5V!)
-   [ ] All GND connections to GND rail
-   [ ] All 5V connections to 5V rail (except RFID)
-   [ ] Resistors on LED pins (220Ω)
-   [ ] SD card formatted and inserted
-   [ ] RTC battery installed
-   [ ] No visible shorts or crossed wires
-   [ ] All pin numbers double-checked
-   [ ] Power supply rated for 2A

**Only power on after completing this checklist!**

---

## 🎯 Pin Usage Summary

```
Digital Pins:
D0  - (Reserved for Serial RX)
D1  - (Reserved for Serial TX)
D2  - [Available]
D3  - RGB LED Blue (PWM)
D4  - SD Card CS
D5  - RGB LED Green (PWM)
D6  - RGB LED Red (PWM)
D7  - [Available]
D8  - Buzzer
D9  - RFID Reset
D10 - RFID SS
D11 - SPI MOSI (RFID + SD)
D12 - SPI MISO (RFID + SD)
D13 - SPI SCK (RFID + SD)

Analog Pins:
A0  - [Available]
A1  - [Available]
A2  - [Available]
A3  - [Available]
A4  - I2C SDA (LCD + RTC)
A5  - I2C SCL (LCD + RTC)

Power:
3.3V - RFID ONLY
5V   - All other components
GND  - All components
```

**Available Pins for Expansion:** D2, D7, A0-A3

---

**Need help?** Refer to WIFI_ATTENDANCE_SETUP_GUIDE.md for detailed troubleshooting!
