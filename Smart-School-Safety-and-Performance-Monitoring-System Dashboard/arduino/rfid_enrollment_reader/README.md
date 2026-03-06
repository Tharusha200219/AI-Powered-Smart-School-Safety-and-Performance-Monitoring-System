# RFID Enrollment & Attendance Reader

This directory contains the Arduino sketch required to run the MFRC522 RFID scanner for the **Safe Learn Hub** attendance and registration system.

The Arduino board functions as a serial peripheral. It scans for RFID cards/wristbands and dumps their unique hexadecimal ID (UID) over the serial connection to the PHP Laravel application.

## 1. Hardware Requirements

- **Arduino UNO R3** (or compatible board with ATmega328P and CH340).
- **MFRC522 RFID Reader** Module (13.56MHz).
- **RGB LED** (Common Cathode recommended). Optional but provides visual feedback during scanning.
- **Jumper Wires** (Male-to-Female to connect the reader to the Arduino).

## 2. Wiring Diagram

### MFRC522 Pin Connections

| RC522 Pin | Arduino UNO Pin | Description                         |
| --------- | --------------- | ----------------------------------- |
| 3.3V      | 3.3V            | **CRITICAL:** Do NOT connect to 5V. |
| RST       | Pin 9           | Reset                               |
| GND       | GND             | Ground                              |
| IRQ       | --              | Unconnected                         |
| MISO      | Pin 12          | Master In Slave Out (SPI)           |
| MOSI      | Pin 11          | Master Out Slave In (SPI)           |
| SCK       | Pin 13          | Serial Clock (SPI)                  |
| SDA (SS)  | Pin 10          | Slave Select                        |

### RGB LED Pin Connections

_Assuming a Common Cathode RGB LED._

| LED Leg    | Arduino UNO Pin |
| ---------- | --------------- |
| Red (R)    | Pin 6           |
| Ground (-) | GND             |
| Green (G)  | Pin 5           |
| Blue (B)   | Pin 3           |

> **Note:** We recommend placing 220-ohm resistors in series with the R, G, and B legs to protect the LEDs and the Arduino pins if your RGB LED module doesn't have them built-in.

## 3. Software Setup & Code Upload

To flash the `rfid_enrollment_reader.ino` sketch onto your Arduino UNO, follow these steps:

1. **Install Arduino IDE**: Download and install the [Arduino IDE](https://www.arduino.cc/en/software) if you don't already have it installed.
2. **Install MFRC522 Library**:
    - Open Arduino IDE.
    - Go to **Sketch** -> **Include Library** -> **Manage Libraries...**
    - Search for **"MFRC522"**.
    - Find the library by **GithubCommunity** (often listed as authored by _miguelbalboa_) and click **Install**.
3. **Open the Sketch**:
    - Open the file `rfid_enrollment_reader.ino` from this folder in the Arduino IDE.
4. **Select your Board and Port**:
    - Go to **Tools** -> **Board** -> **Arduino AVR Boards** -> **Arduino UNO**.
    - Go to **Tools** -> **Port** and select the port your Arduino is connected to (it might say something like `/dev/cu.usbserial-1410` on Mac or `COM3` on Windows).
5. **Upload**:
    - Click the **Upload** arrow button in the top left corner of the Arduino IDE.
    - Wait for the "Done uploading" message at the bottom.

## 4. Testing the Reader

Once the code is uploaded successfully, you can test it directly from the Arduino IDE:

1. Open the **Serial Monitor** (magnifying glass icon in the top right).
2. Set the baud rate in the bottom right corner of the Serial Monitor to **9600 baud**.
3. You should see `INFO: RFID Reader Ready.` printed.
4. Take an RFID card or wristband and tap it against the MFRC522 reader.
5. The RGB LED should briefly flash **Green**, and the Serial Monitor should output:
   `DATA:UID:<hex_value_here>`
6. You can type `PING` into the Serial Monitor send box and hit enter, the board should reply with `PONG`.

If everything works as described above, your hardware is successfully configured to communicate with the Safe Learn Hub application!
