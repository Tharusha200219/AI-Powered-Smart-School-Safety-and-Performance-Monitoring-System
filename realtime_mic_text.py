import sounddevice as sd
from scipy.io.wavfile import write
import numpy as np
import socketio
from speech_to_text import speech_to_text
from pretrained_text_classifier import classify_text

# Connect to your Flask server
sio = socketio.Client()

try:
    sio.connect("http://127.0.0.1:5000", transports=["polling"])
    print("✅ Connected to Flask (text threat detection active)")
except Exception as e:
    print("❌ Could not connect:", e)
    exit()

SAMPLE_RATE = 16000
DURATION = 3  # record 3 seconds


def record_audio():
    print("🎤 Listening for speech...")
    audio = sd.rec(int(DURATION * SAMPLE_RATE), samplerate=SAMPLE_RATE, channels=1)
    sd.wait()
    audio = audio.flatten()
    path = "temp_speech.wav"
    write(path, SAMPLE_RATE, audio)
    return path


def analyze_speech():
    # 1. Record audio
    file_path = record_audio()

    # 2. Speech → Text
    text = speech_to_text(file_path)
    print("\n📝 Transcript:", text)

    if len(text.strip()) < 2:
        print("⚠ No speech detected.")
        return

    # 3. Text → Threat/abuse/safe
    category, score, raw_label = classify_text(text)

    # 4. Send to dashboard
    sio.emit("new_alert", {
        "source": "Mic-Speech",
        "prediction": category,
        "confidence": round(score, 3),
        "text": text
    })


print("\n==== LIVE SPEECH THREAT DETECTION ====")
print("Press ENTER to capture speech")

while True:
    input()
    analyze_speech()
