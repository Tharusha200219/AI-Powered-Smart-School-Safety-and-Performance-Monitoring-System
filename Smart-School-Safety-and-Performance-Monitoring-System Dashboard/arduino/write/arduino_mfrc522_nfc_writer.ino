/*
 * Safe Learn Hub - RFID Student Card Enrollment Device
 *
 * Hardware:
 * - Arduino UNO R3 (ATMega328 + CH340)
 * - MFRC522 RFID reader/writer (13.56MHz)
 * - Common-cathode RGB LED
 *
 * MFRC522 wiring:
 * - SDA/SS -> D10
 * - SCK    -> D13
 * - MOSI   -> D11
 * - MISO   -> D12
 * - RST    -> D9
 * - 3.3V   -> 3.3V
 * - GND    -> GND
 *
 * RGB LED (common cathode):
 * - Red   -> D6 (with 220R)
 * - Green -> D5 (with 220R)
 * - Blue  -> D3 (with 220R)
 * - Common cathode -> GND
 *
 * Serial protocol:
 * - WRITE:student_id:full_name:grade:admission_number:nfc_tag_id
 * - READ
 *
 * Responses:
 * - SUCCESS:<UID>
 * - ERROR:<message>
 * - DATA:<UID>:<content>
 */

#include <SPI.h>
#include <MFRC522.h>

#define RST_PIN 9
#define SS_PIN 10

#define LED_RED_PIN 6
#define LED_GREEN_PIN 5
#define LED_BLUE_PIN 3

#define SERIAL_BAUD 9600
#define SERIAL_LINE_MAX 220

#define CARD_WAIT_TIMEOUT_MS 10000UL
#define TEMP_LED_HOLD_MS 3000UL

#define START_DATA_BLOCK 4
#define LAST_BLOCK 62
#define MAX_DATA_BYTES 160

MFRC522 mfrc522(SS_PIN, RST_PIN);
MFRC522::MIFARE_Key key;

enum LedMode {
  LED_PULSE_BLUE,
  LED_SOLID_GREEN,
  LED_SOLID_RED
};

LedMode ledMode = LED_PULSE_BLUE;
unsigned long tempLedUntil = 0;

char serialLine[SERIAL_LINE_MAX];
uint16_t serialPos = 0;

String lastSeenUid = "";
unsigned long lastCardScanMs = 0;

void setRgb(uint8_t r, uint8_t g, uint8_t b) {
  analogWrite(LED_RED_PIN, r);
  analogWrite(LED_GREEN_PIN, g);
  analogWrite(LED_BLUE_PIN, b);
}

void updateLed() {
  if (tempLedUntil > 0 && millis() > tempLedUntil) {
    tempLedUntil = 0;
    ledMode = LED_PULSE_BLUE;
  }

  if (ledMode == LED_SOLID_GREEN) {
    setRgb(0, 255, 0);
    return;
  }

  if (ledMode == LED_SOLID_RED) {
    setRgb(255, 0, 0);
    return;
  }

  // Blue pulse (triangle wave) while waiting for serial input
  const uint16_t period = 1600;
  uint16_t t = millis() % period;
  uint8_t brightness;

  if (t < period / 2) {
    brightness = (uint8_t)map(t, 0, period / 2, 10, 220);
  } else {
    brightness = (uint8_t)map(t, period / 2, period, 220, 10);
  }

  setRgb(0, 0, brightness);
}

void holdGreen(uint16_t ms) {
  ledMode = LED_SOLID_GREEN;
  tempLedUntil = millis() + ms;
}

void holdRed(uint16_t ms) {
  ledMode = LED_SOLID_RED;
  tempLedUntil = millis() + ms;
}

String uidToString(const MFRC522::Uid &uid) {
  String s;
  for (byte i = 0; i < uid.size; i++) {
    if (uid.uidByte[i] < 0x10) s += '0';
    s += String(uid.uidByte[i], HEX);
  }
  s.toUpperCase();
  return s;
}

bool isSectorTrailer(byte block) {
  return (block % 4) == 3;
}

byte trailerForBlock(byte block) {
  return (block / 4) * 4 + 3;
}

bool authenticateBlock(byte block, String &err) {
  MFRC522::StatusCode status = mfrc522.PCD_Authenticate(
    MFRC522::PICC_CMD_MF_AUTH_KEY_A,
    trailerForBlock(block),
    &key,
    &(mfrc522.uid)
  );

  if (status != MFRC522::STATUS_OK) {
    err = "Auth failed block " + String(block) + " (" + mfrc522.GetStatusCodeName(status) + ")";
    return false;
  }

  return true;
}

bool readBlock(byte block, byte *buffer16, String &err) {
  if (isSectorTrailer(block)) {
    err = "Attempt to read trailer block";
    return false;
  }

  if (!authenticateBlock(block, err)) {
    return false;
  }

  byte size = 18;
  MFRC522::StatusCode status = mfrc522.MIFARE_Read(block, buffer16, &size);

  if (status != MFRC522::STATUS_OK) {
    err = "Read failed block " + String(block) + " (" + mfrc522.GetStatusCodeName(status) + ")";
    return false;
  }

  return true;
}

bool writeBlock(byte block, const byte *data16, String &err) {
  if (isSectorTrailer(block)) {
    err = "Attempt to write trailer block";
    return false;
  }

  if (!authenticateBlock(block, err)) {
    return false;
  }

  MFRC522::StatusCode status = mfrc522.MIFARE_Write(block, (byte*)data16, 16);
  if (status != MFRC522::STATUS_OK) {
    err = "Write failed block " + String(block) + " (" + mfrc522.GetStatusCodeName(status) + ")";
    return false;
  }

  return true;
}

bool isBufferBlank(const byte *buf16) {
  for (byte i = 0; i < 16; i++) {
    if (buf16[i] != 0x00 && buf16[i] != 0xFF) {
      return false;
    }
  }
  return true;
}

bool waitForCard(uint32_t timeoutMs, String &uid, String &err) {
  unsigned long start = millis();

  while (millis() - start < timeoutMs) {
    updateLed();

    bool cardPresent = mfrc522.PICC_IsNewCardPresent();

    // If it's not a "new" card event, still try to wake a card that is
    // already resting on the reader so overwrite can happen without re-tap.
    if (!cardPresent) {
      byte atqa[2];
      byte atqaSize = 2;
      MFRC522::StatusCode wakeStatus = mfrc522.PICC_WakeupA(atqa, &atqaSize);
      if (wakeStatus == MFRC522::STATUS_OK || wakeStatus == MFRC522::STATUS_COLLISION) {
        cardPresent = true;
      }
    }

    if (!cardPresent || !mfrc522.PICC_ReadCardSerial()) {
      delay(20);
      continue;
    }

    uid = uidToString(mfrc522.uid);
    return true;
  }

  err = "No card detected (timeout)";
  return false;
}

void endCardSession() {
  mfrc522.PICC_HaltA();
  mfrc522.PCD_StopCrypto1();
}

byte collectDataBlocks(byte *blocks, byte maxBlocks) {
  byte count = 0;
  for (byte b = START_DATA_BLOCK; b <= LAST_BLOCK && count < maxBlocks; b++) {
    if (!isSectorTrailer(b)) {
      blocks[count++] = b;
    }
  }
  return count;
}

bool checkCardWritableAndBlank(bool &blankOrWritable, bool &hasData, String &uid, String &err) {
  if (!mfrc522.PICC_IsNewCardPresent() || !mfrc522.PICC_ReadCardSerial()) {
    return false;
  }

  uid = uidToString(mfrc522.uid);

  byte blockData[18];
  String readErr;

  if (!readBlock(START_DATA_BLOCK, blockData, readErr)) {
    blankOrWritable = false;
    hasData = false;
    err = readErr;
    endCardSession();
    return true;
  }

  bool blank = isBufferBlank(blockData);
  blankOrWritable = blank;
  hasData = !blank;
  err = "";

  endCardSession();
  return true;
}

bool wipeDataArea(String &err) {
  byte blocks[40];
  byte blockCount = collectDataBlocks(blocks, 40);

  byte clearBlock[16];
  for (byte i = 0; i < 16; i++) {
    clearBlock[i] = 0x00;
  }

  for (byte i = 0; i < blockCount; i++) {
    if (!writeBlock(blocks[i], clearBlock, err)) {
      return false;
    }
  }

  return true;
}

bool writePayloadToCard(const String &payload, String &uid, String &err) {
  if (!waitForCard(CARD_WAIT_TIMEOUT_MS, uid, err)) {
    return false;
  }

  if (payload.length() > MAX_DATA_BYTES - 1) {
    err = "Payload too long";
    endCardSession();
    return false;
  }

  byte blocks[40];
  byte blockCount = collectDataBlocks(blocks, 40);

  byte neededBlocks = (payload.length() + 1 + 15) / 16; // include null terminator
  if (neededBlocks > blockCount) {
    err = "Not enough card space";
    endCardSession();
    return false;
  }

  // Always overwrite previous card content by wiping all data blocks first.
  if (!wipeDataArea(err)) {
    err = "Clear existing data failed: " + err;
    endCardSession();
    return false;
  }

  // Write payload bytes + null terminator across safe data blocks.
  uint16_t cursor = 0;
  for (byte i = 0; i < neededBlocks; i++) {
    byte out[16];
    for (byte j = 0; j < 16; j++) {
      if (cursor < payload.length()) {
        out[j] = (byte)payload[cursor++];
      } else if (cursor == payload.length()) {
        out[j] = 0x00;
        cursor++;
      } else {
        out[j] = 0x00;
      }
    }

    if (!writeBlock(blocks[i], out, err)) {
      endCardSession();
      return false;
    }
  }

  // Clear one extra block (if available) to remove leftovers from older longer payloads.
  if (neededBlocks < blockCount) {
    byte clearBlock[16];
    for (byte i = 0; i < 16; i++) clearBlock[i] = 0x00;
    String clearErr;
    writeBlock(blocks[neededBlocks], clearBlock, clearErr);
  }

  endCardSession();
  return true;
}

bool readPayloadFromCard(String &uid, String &content, String &err) {
  if (!waitForCard(CARD_WAIT_TIMEOUT_MS, uid, err)) {
    return false;
  }

  byte blocks[40];
  byte blockCount = collectDataBlocks(blocks, 40);

  char out[MAX_DATA_BYTES];
  uint16_t outPos = 0;
  bool reachedTerminator = false;

  for (byte i = 0; i < blockCount && !reachedTerminator; i++) {
    byte blockData[18];
    if (!readBlock(blocks[i], blockData, err)) {
      endCardSession();
      return false;
    }

    for (byte j = 0; j < 16; j++) {
      byte c = blockData[j];
      if (c == 0x00 || c == 0xFF) {
        reachedTerminator = true;
        break;
      }

      if (outPos < MAX_DATA_BYTES - 1) {
        out[outPos++] = (char)c;
      }
    }
  }

  out[outPos] = '\0';
  content = String(out);

  endCardSession();
  return true;
}

bool splitWriteCommand(const String &line,
                       String &studentId,
                       String &fullName,
                       String &grade,
                       String &admissionNumber,
                       String &nfcTagId,
                       String &err) {
  if (!line.startsWith("WRITE:")) {
    err = "Invalid WRITE command";
    return false;
  }

  int p1 = line.indexOf(':');
  int p2 = line.indexOf(':', p1 + 1);
  int p3 = line.indexOf(':', p2 + 1);
  int p4 = line.indexOf(':', p3 + 1);
  int p5 = line.indexOf(':', p4 + 1);

  if (p1 < 0 || p2 < 0 || p3 < 0 || p4 < 0 || p5 < 0) {
    err = "WRITE format must be WRITE:student_id:full_name:grade:admission_number:nfc_tag_id";
    return false;
  }

  studentId = line.substring(p1 + 1, p2);
  fullName = line.substring(p2 + 1, p3);
  grade = line.substring(p3 + 1, p4);
  admissionNumber = line.substring(p4 + 1, p5);
  nfcTagId = line.substring(p5 + 1);

  studentId.trim();
  fullName.trim();
  grade.trim();
  admissionNumber.trim();
  nfcTagId.trim();

  if (studentId.length() == 0 || fullName.length() == 0 || grade.length() == 0 ||
      admissionNumber.length() == 0 || nfcTagId.length() == 0) {
    err = "WRITE fields cannot be empty";
    return false;
  }

  return true;
}

bool looksLikePipePayload(const String &line) {
  int pipeCount = 0;
  for (uint16_t i = 0; i < line.length(); i++) {
    if (line[i] == '|') {
      pipeCount++;
    }
  }

  // Current Laravel payload pattern has 6 fields => 5 pipes.
  return pipeCount >= 5;
}

bool readLineWithTimeout(String &out, uint32_t timeoutMs) {
  unsigned long start = millis();
  out = "";

  while (millis() - start < timeoutMs) {
    updateLed();
    while (Serial.available() > 0) {
      char c = (char)Serial.read();
      if (c == '\r') continue;
      if (c == '\n') {
        out.trim();
        return true;
      }
      out += c;
      if (out.length() >= SERIAL_LINE_MAX - 1) {
        out.trim();
        return true;
      }
    }
    delay(2);
  }

  out.trim();
  return out.length() > 0;
}

// Backward compatibility with previous protocol:
//   WRITE_NFC\n
//   <length>\n
//   <payload>\n
void handleLegacyWriteNfc() {
  String lenLine;
  if (!readLineWithTimeout(lenLine, 5000)) {
    Serial.println("ERROR:Missing length after WRITE_NFC");
    holdRed(TEMP_LED_HOLD_MS);
    return;
  }

  int requestedLength = lenLine.toInt();
  if (requestedLength <= 0 || requestedLength > MAX_DATA_BYTES - 1) {
    Serial.println("ERROR:Invalid legacy payload length");
    holdRed(TEMP_LED_HOLD_MS);
    return;
  }

  String payload;
  if (!readLineWithTimeout(payload, 5000)) {
    Serial.println("ERROR:Missing payload after WRITE_NFC length");
    holdRed(TEMP_LED_HOLD_MS);
    return;
  }

  if (payload.length() > requestedLength) {
    payload = payload.substring(0, requestedLength);
  }

  String uid, writeErr;
  if (writePayloadToCard(payload, uid, writeErr)) {
    Serial.print("SUCCESS:");
    Serial.println(uid);
    holdGreen(TEMP_LED_HOLD_MS);
  } else {
    Serial.print("ERROR:");
    Serial.println(writeErr);
    holdRed(TEMP_LED_HOLD_MS);
  }
}

void processCommand(const String &cmdRaw) {
  String cmd = cmdRaw;
  cmd.trim();
  String cmdUpper = cmd;
  cmdUpper.toUpperCase();

  if (cmd.length() == 0) return;

  if (cmdUpper == "READ") {
    String uid, data, err;
    if (readPayloadFromCard(uid, data, err)) {
      if (data.length() == 0) {
        Serial.print("DATA:");
        Serial.print(uid);
        Serial.println(":EMPTY");
      } else {
        Serial.print("DATA:");
        Serial.print(uid);
        Serial.print(":");
        Serial.println(data);
      }
      holdGreen(TEMP_LED_HOLD_MS);
    } else {
      Serial.print("ERROR:");
      Serial.println(err);
      holdRed(TEMP_LED_HOLD_MS);
    }
    return;
  }

  if (cmdUpper.startsWith("WRITE:")) {
    String studentId, fullName, grade, admissionNumber, nfcTagId, parseErr;
    if (!splitWriteCommand(cmd, studentId, fullName, grade, admissionNumber, nfcTagId, parseErr)) {
      Serial.print("ERROR:");
      Serial.println(parseErr);
      holdRed(TEMP_LED_HOLD_MS);
      return;
    }

    // Simple compact plain-text payload.
    String payload = "SID=" + studentId +
                     ";NAME=" + fullName +
                     ";GRADE=" + grade +
                     ";ADM=" + admissionNumber +
                     ";NFC=" + nfcTagId;

    String uid, writeErr;
    if (writePayloadToCard(payload, uid, writeErr)) {
      Serial.print("SUCCESS:");
      Serial.println(uid);
      holdGreen(TEMP_LED_HOLD_MS);
    } else {
      Serial.print("ERROR:");
      Serial.println(writeErr);
      holdRed(TEMP_LED_HOLD_MS);
    }
    return;
  }

  // Backward compatibility: raw payload (no command prefix), e.g.
  // stu-00000081|Moana|Golden|8|30|1974-08-17
  if (looksLikePipePayload(cmd)) {
    String uid, writeErr;
    if (writePayloadToCard(cmd, uid, writeErr)) {
      Serial.print("SUCCESS:");
      Serial.println(uid);
      holdGreen(TEMP_LED_HOLD_MS);
    } else {
      Serial.print("ERROR:");
      Serial.println(writeErr);
      holdRed(TEMP_LED_HOLD_MS);
    }
    return;
  }

  if (cmdUpper == "WRITE_NFC") {
    handleLegacyWriteNfc();
    return;
  }

  if (cmdUpper == "STATUS") {
    Serial.println("READY");
    return;
  }

  if (cmdUpper == "PING") {
    Serial.println("PONG");
    return;
  }

  Serial.print("ERROR:Unknown command:");
  Serial.println(cmd);
}

void pollSerial() {
  while (Serial.available() > 0) {
    char c = (char)Serial.read();

    if (c == '\r') continue;

    if (c == '\n') {
      serialLine[serialPos] = '\0';
      processCommand(String(serialLine));
      serialPos = 0;
      serialLine[0] = '\0';
      continue;
    }

    if (serialPos < SERIAL_LINE_MAX - 1) {
      serialLine[serialPos++] = c;
    } else {
      // Overflow: reset line buffer and report error once
      serialPos = 0;
      serialLine[0] = '\0';
      Serial.println("ERROR:Input line too long");
      holdRed(TEMP_LED_HOLD_MS);
    }
  }
}

void checkCardStateForLed() {
  // Avoid excessive polling chatter; inspect card every 250ms.
  if (millis() - lastCardScanMs < 250) return;
  lastCardScanMs = millis();

  bool blankOrWritable = false;
  bool hasData = false;
  String uid, err;

  if (!checkCardWritableAndBlank(blankOrWritable, hasData, uid, err)) {
    return;
  }

  if (uid == lastSeenUid && tempLedUntil == 0) {
    return;
  }

  lastSeenUid = uid;

  if (blankOrWritable && !hasData) {
    // Requirement: GREEN if new/blank (writable)
    ledMode = LED_SOLID_GREEN;
    Serial.print("CARD:");
    Serial.print(uid);
    Serial.println(":READY");
  } else {
    // Requirement: RED if already has data or cannot be written
    ledMode = LED_SOLID_RED;
    if (hasData) {
      Serial.print("CARD:");
      Serial.print(uid);
      Serial.println(":HAS_DATA");
    } else {
      Serial.print("CARD:");
      Serial.print(uid);
      Serial.print(":NOT_WRITABLE:");
      Serial.println(err);
    }
  }
}

void setup() {
  pinMode(LED_RED_PIN, OUTPUT);
  pinMode(LED_GREEN_PIN, OUTPUT);
  pinMode(LED_BLUE_PIN, OUTPUT);
  setRgb(0, 0, 0);

  Serial.begin(SERIAL_BAUD);

  SPI.begin();
  mfrc522.PCD_Init();

  for (byte i = 0; i < 6; i++) {
    key.keyByte[i] = 0xFF;
  }

  Serial.println("READY");
}

void loop() {
  updateLed();
  pollSerial();

  // Only scan ambient card state if no temporary success/failure LED hold is active.
  if (tempLedUntil == 0) {
    if (ledMode != LED_PULSE_BLUE) {
      // Keep current card-state color until another state change is detected.
    }
    checkCardStateForLed();
  }

  delay(5);
}
