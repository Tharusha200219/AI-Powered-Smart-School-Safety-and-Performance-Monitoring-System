import sounddevice as sd
import numpy as np
import socketio
from scipy.io.wavfile import write
import tensorflow as tf
import os
from utils_audio import audio_to_melspectrogram

# Connect to Flask server
sio = socketio.Client()

try:
    sio.connect("http://127.0.0.1:5000", transports=["polling"])
    print("✅ Connected to Flask-SocketIO (polling mode).")
except Exception as e:
    print("❌ Failed to connect:", e)
    exit()

# Load model
model = tf.keras.models.load_model("models/audio_cnn.h5")
label_classes = np.load("models/label_classes.npy")

SAMPLE_RATE = 22050
DURATION = 2


def record_audio():
    print("🎙 Recording 2 seconds...")
    audio = sd.rec(int(DURATION * SAMPLE_RATE), samplerate=SAMPLE_RATE, channels=1)
    sd.wait()
    audio = audio.flatten()
    path = "temp_mic.wav"
    write(path, SAMPLE_RATE, audio)
    return path


def predict_from_mic():
    file_path = record_audio()

    mel = audio_to_melspectrogram(file_path)
    mel = np.expand_dims(mel, axis=0)

    preds = model.predict(mel)
    idx = np.argmax(preds)
    conf = float(np.max(preds))
    label = label_classes[idx]

    print(f"🔊 Detected: {label} (Confidence: {round(conf, 3)})")

    sio.emit("new_alert", {
        "source": "Microphone",
        "prediction": str(label),
        "confidence": round(conf, 3)
    })


print("🎤 Microphone detection started.")
print("➡ Press ENTER to capture audio.")

while True:
    input()
    predict_from_mic()
