/*
 * RFID Attendance Reader — Arduino UNO R3 + RC522 + LCD + RGB LED
 * ──────────────────────────────────────────────────────────
 * Hardware wiring (USER: please verify carefully!)
 * ────────────────
 *  RC522 RFID Reader (3.3V ONLY - DO NOT USE 5V!):
 *    VCC  → 3.3V (Red wire)
 *    GND  → GND  (Black wire)
 *    RST  → D9   (Orange wire)
 *    SDA  → D10  (Yellow wire - SPI SS)
 *    MOSI → D11  (Green wire - SPI MOSI)
 *    MISO → D12  (Green wire - SPI MISO)
 *    SCK  → D13  (Green wire - SPI SCK)
 *
 *  I2C LCD (16x2):
 *    VCC  → 5V
 *    GND  → GND
 *    SDA  → A4
 *    SCL  → A5
 *
 *  RGB LED (Common Cathode):
 *    Red   → D6
 *    Green → D5
 *    Blue  → D3
 */

#include <SPI.h>
#include <MFRC522.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>

// ── Pin definitions ────────────────────────────────────────────────────────
#define RC522_RST_PIN 9
#define RC522_SS_PIN 10
#define LED_RED 6
#define LED_GREEN 5
#define LED_BLUE 3

// ── Configuration ──────────────────────────────────────────────────────────
#define DEVICE_ID "DOOR_01"
#define SERIAL_BAUD 115200  
#define SAME_CARD_MS 2000   
#define LED_FLASH_MS 100    
#define LCD_ADDR 0x27       // Common addresses: 0x27, 0x3F

MFRC522 mfrc522(RC522_SS_PIN, RC522_RST_PIN);
LiquidCrystal_I2C lcd(LCD_ADDR, 16, 2);

char lastUID[20] = "";
uint32_t lastScanTime = 0;

void setup() {
    Serial.begin(SERIAL_BAUD);
    while (!Serial);
    delay(500); // Give serial time to settle

    Serial.println(F("\n--- RFID System Starting ---"));

    // 1. I2C Scan (Internal Diagnosis)
    Wire.begin();
    Wire.beginTransmission(LCD_ADDR);
    if (Wire.endTransmission() != 0) {
        Serial.println(F("DEBUG: LCD not found at 0x27. Trying 0x3F..."));
        // This is a simple diagnostic, we don't change the global lcd object address here
        // but we'll print it to the serial log so we know why it's blank.
    } else {
        Serial.println(F("DEBUG: LCD found at 0x27"));
    }

    // 2. LCD Init
    lcd.init();
    lcd.backlight();
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print(F(" SYSTEM STARTING "));
    lcd.setCursor(0, 1);
    lcd.print(F("PLEASE WAIT...  "));

    // 3. RFID Init
    SPI.begin();
    mfrc522.PCD_Init();
    delay(50);
    
    // Self-test for RFID
    Serial.println(F("DEBUG: Testing RFID Reader..."));
    mfrc522.PCD_DumpVersionToSerial();
    // Note: PCD_DumpVersionToSerial returns void. 
    // If chip is connected, it prints version info. If not, it prints 0x00 or 0xFF.

    mfrc522.PCD_SetAntennaGain(mfrc522.RxGain_max);

    // 4. LED Pins
    pinMode(LED_RED, OUTPUT);
    pinMode(LED_GREEN, OUTPUT);
    pinMode(LED_BLUE, OUTPUT);
    setRGB(0, 0, 0);

    delay(1000);
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print(F(" SCAN WRISTBAND "));
    
    // Send standard ready message for bridge
    Serial.println(F("{\"status\":\"ready\",\"device_id\":\"" DEVICE_ID "\"}"));
}

void loop() {
    // Detect a card
    if (!mfrc522.PICC_IsNewCardPresent()) {
        byte atqa[2];
        byte atqaSize = sizeof(atqa);
        if (mfrc522.PICC_WakeupA(atqa, &atqaSize) != MFRC522::STATUS_OK) {
            return;
        }
    }
    
    if (!mfrc522.PICC_ReadCardSerial()) return;

    // Build UID string
    char uid[20];
    buildUIDString(mfrc522.uid.uidByte, mfrc522.uid.size, uid);
    mfrc522.PICC_HaltA();
    mfrc522.PCD_StopCrypto1();

    uint32_t now = millis();

    // Debounce
    if (strcmp(uid, lastUID) == 0 && (now - lastScanTime) < SAME_CARD_MS) {
        return;
    }

    strncpy(lastUID, uid, sizeof(lastUID) - 1);
    lastScanTime = now;

    // Send JSON to bridge
    Serial.print(F("{\"uid\":\""));
    Serial.print(uid);
    Serial.print(F("\",\"device_id\":\"" DEVICE_ID "\"}"));
    Serial.println();

    // Feedback
    lcd.setCursor(0, 0);
    lcd.print(F(" SCAN SUCCESS!  "));
    lcd.setCursor(0, 1);
    lcd.print(F("UID: "));
    lcd.print(uid);
    
    flashGreen(2);
    
    delay(1000);
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd.print(F(" SCAN WRISTBAND "));
}

void buildUIDString(byte *buffer, byte length, char *out) {
    char *p = out;
    for (byte i = 0; i < length; i++) {
        sprintf(p, "%02X", buffer[i]);
        p += 2;
    }
    *p = '\0';
}

void setRGB(uint8_t r, uint8_t g, uint8_t b) {
    analogWrite(LED_RED, r);
    analogWrite(LED_GREEN, g);
    analogWrite(LED_BLUE, b);
}

void flashGreen(uint8_t count) {
    for (uint8_t i = 0; i < count; i++) {
        setRGB(0, 255, 0);
        delay(LED_FLASH_MS);
        setRGB(0, 0, 0);
        delay(LED_FLASH_MS);
    }
}
