#include <SPI.h>
#include <MFRC522.h>

#define RST_PIN         9          // Configurable, see typical pin layout above
#define SS_PIN          10         // Configurable, see typical pin layout above

#define RED_PIN         6
#define GREEN_PIN       5
#define BLUE_PIN        3

MFRC522 mfrc522(SS_PIN, RST_PIN);  // Create MFRC522 instance

// For RGB common cathode, HIGH is on, LOW is off
void setLED(bool r, bool g, bool b) {
  digitalWrite(RED_PIN, r ? HIGH : LOW);
  digitalWrite(GREEN_PIN, g ? HIGH : LOW);
  digitalWrite(BLUE_PIN, b ? HIGH : LOW);
}

void setup() {
  Serial.begin(9600);		// Initialize serial communications with the PC
  while (!Serial);		// Do nothing if no serial port is opened (added for Arduinos based on ATMEGA32U4)
  SPI.begin();			// Init SPI bus
  mfrc522.PCD_Init();		// Init MFRC522
  delay(4);				// Optional delay. Some board do need more time after init to be ready, see Readme

  pinMode(RED_PIN, OUTPUT);
  pinMode(GREEN_PIN, OUTPUT);
  pinMode(BLUE_PIN, OUTPUT);

  setLED(false, false, false); // All off
  Serial.println("INFO: RFID Reader Ready.");
}

bool waitingForCard = false;
unsigned long pulseTimer = 0;
bool pulseState = false;

void loop() {
  
  // Handle serial commands
  if (Serial.available() > 0) {
    String cmd = Serial.readStringUntil('\n');
    cmd.trim();
    if (cmd == "PING") {
      Serial.println("PONG");
    } else if (cmd == "READ_UID") {
      waitingForCard = true;
      Serial.println("INFO: Waiting for card to read UID...");
    } else {
      Serial.println("ERROR: Unknown command.");
    }
  }

  // Handle pulsing LED when waiting for a card
  if (waitingForCard) {
    if (millis() - pulseTimer > 500) {
      pulseTimer = millis();
      pulseState = !pulseState;
      setLED(false, false, pulseState); // Pulsing Blue
    }
  } else {
    setLED(false, false, false); // Default state off (to not be annoying)
  }

  // Look for new cards
  if (!mfrc522.PICC_IsNewCardPresent()) {
    return;
  }

  // Select one of the cards
  if (!mfrc522.PICC_ReadCardSerial()) {
    return;
  }

  // We found a card!
  String uidStr = "";
  for (byte i = 0; i < mfrc522.uid.size; i++) {
    uidStr += String(mfrc522.uid.uidByte[i] < 0x10 ? "0" : "");
    uidStr += String(mfrc522.uid.uidByte[i], HEX);
  }
  uidStr.toUpperCase();

  mfrc522.PICC_HaltA(); // Halt PICC
  mfrc522.PCD_StopCrypto1(); // Stop encryption on PCD

  if (waitingForCard) {
    // Explicit read command was sent
    waitingForCard = false;
    setLED(false, true, false); // Green flash for success
    Serial.println("DATA:UID:" + uidStr);
    delay(1000);
    setLED(false, false, false);
  } else {
    // Continuous polling/scanning logic for attendance kiosk
    // Always dump UID if tap occurs without waiting for a command
    setLED(false, true, false); // Green flash for success
    Serial.println("DATA:UID:" + uidStr);
    delay(1000); // Debounce to prevent multiple reads immediately
    setLED(false, false, false);
  }
}
