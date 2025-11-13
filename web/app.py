from flask import Flask, render_template, request, jsonify
import os
import sys
import numpy as np
import tensorflow as tf

# Ensure repository root is on sys.path so imports work when running from the `web` folder
# (allows importing `utils_audio` which lives in the parent directory)
sys.path.insert(0, os.path.abspath(os.path.join(os.path.dirname(__file__), "..")))

from utils_audio import audio_to_melspectrogram

app = Flask(__name__, template_folder="templates", static_folder="static")

# Load model
model = tf.keras.models.load_model("../models/audio_cnn.h5")
label_classes = np.load("../models/label_classes.npy")

UPLOAD_FOLDER = "uploads"
os.makedirs(UPLOAD_FOLDER, exist_ok=True)

@app.route("/")
def index():
    return render_template("index.html")

@app.route("/predict", methods=["POST"])
def predict():
    if "file" not in request.files:
        return jsonify({"error": "No file uploaded"})

    audio_file = request.files["file"]
    file_path = os.path.join(UPLOAD_FOLDER, audio_file.filename)
    audio_file.save(file_path)

    mel = audio_to_melspectrogram(file_path)

    if mel is None:
        return jsonify({"error": "Audio processing failed"})

    mel = np.expand_dims(mel, axis=0)

    predictions = model.predict(mel)
    class_index = np.argmax(predictions)
    confidence = float(np.max(predictions))

    predicted_label = label_classes[class_index]

    result = {
        "prediction": predicted_label,
        "confidence": round(confidence, 3)
    }

    return jsonify(result)

if __name__ == "__main__":
    app.run(debug=True)
