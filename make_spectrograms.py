import os
import librosa
import numpy as np
import matplotlib.pyplot as plt

RAW_DIR = "data/raw"
PROC_DIR = "data/processed"

os.makedirs(PROC_DIR, exist_ok=True)

# Go through each class folder in data/raw
for label in os.listdir(RAW_DIR):
    class_raw_path = os.path.join(RAW_DIR, label)
    class_proc_path = os.path.join(PROC_DIR, label)

    if not os.path.isdir(class_raw_path):
        continue  # skip files that are not folders

    os.makedirs(class_proc_path, exist_ok=True)

    print(f"Processing class: {label}")

    for filename in os.listdir(class_raw_path):
        if not (filename.lower().endswith(".mp3") or filename.lower().endswith(".wav")):
            print("Skipped (not audio):", filename)
            continue

        file_path = os.path.join(class_raw_path, filename)

        try:
            # Load audio
            y, sr = librosa.load(file_path, sr=22050)  # 22.05kHz sample rate

            # Make it ~2 seconds (for consistency)
            target_length = 2 * sr
            if len(y) < target_length:
                # Pad with zeros (silence)
                padding = target_length - len(y)
                y = np.pad(y, (0, padding))
            else:
                # Cut extra part
                y = y[:target_length]

            # Create Mel spectrogram
            mel_spec = librosa.feature.melspectrogram(y=y, sr=sr, n_mels=64)
            mel_db = librosa.power_to_db(mel_spec, ref=np.max)

            # Save as numpy array
            base_name = os.path.splitext(filename)[0]
            out_path = os.path.join(class_proc_path, base_name + ".npy")
            np.save(out_path, mel_db)

        except Exception as e:
            print(f"Error with {file_path}: {e}")
