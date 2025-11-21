import sounddevice as sd
import numpy as np
from scipy.io.wavfile import write
import tensorflow as tf
import os

# Import the audio processing helper
from utils_audio import audio_to_melspectrogram

# Load your trained model
model = tf.keras.models.load_model("models/audio_cnn.h5")
label_classes = np.load("models/label_classes.npy")

# Microphone settings
SAMPLE_RATE = 22050   # same as training
DURATION = 2          # seconds to record per prediction

def record_audio():
    print("\n🎙️ Recording audio for 2 seconds...")
    audio = sd.rec(int(DURATION * SAMPLE_RATE), samplerate=SAMPLE_RATE, channels=1)
    sd.wait()  # wait until recording finishes

    # Convert to 1D
    audio = audio.flatten()

    # Save temporary file (for librosa)
    temp_path = "temp_mic.wav"
    write(temp_path, SAMPLE_RATE, audio)
    return temp_path



  