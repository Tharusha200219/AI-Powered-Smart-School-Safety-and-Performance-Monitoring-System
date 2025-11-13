import os
import numpy as np
import tensorflow as tf
from utils_audio import audio_to_melspectrogram

model = tf.keras.models.load_model("models/audio_cnn.h5")
label_classes = np.load("models/label_classes.npy")

def predict_audio(file_path):
    print("Processing:", file_path)

    mel = audio_to_melspectrogram(file_path)

    if mel is None:
        print("Audio processing failed.")
        return

    mel = np.expand_dims(mel, axis=0)

    predictions = model.predict(mel)
    class_index = np.argmax(predictions)
    confidence = np.max(predictions)

    predicted_label = label_classes[class_index]

    print("\n🎯 PREDICTION RESULT")
    print("----------------------")
    print("Label:", predicted_label)
    print("Confidence:", round(float(confidence), 3))

    return predicted_label, confidence

if __name__ == "__main__":
    file_path = input("Enter the path to a .wav or .mp3 file: ")
    predict_audio(file_path)
