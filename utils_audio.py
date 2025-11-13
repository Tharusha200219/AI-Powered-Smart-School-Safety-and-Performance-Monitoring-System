import librosa
import numpy as np

def audio_to_melspectrogram(file_path, sr=22050, duration=2, n_mels=64):
    try:
        y, _ = librosa.load(file_path, sr=sr)

        target_length = duration * sr

        if len(y) < target_length:
            padding = target_length - len(y)
            y = np.pad(y, (0, padding))
        else:
            y = y[:target_length]

        mel_spec = librosa.feature.melspectrogram(y=y, sr=sr, n_mels=n_mels)
        mel_db = librosa.power_to_db(mel_spec, ref=np.max)

        mel_db = np.expand_dims(mel_db, axis=-1)

        return mel_db

    except Exception as e:
        print("Error processing audio:", e)
        return None
