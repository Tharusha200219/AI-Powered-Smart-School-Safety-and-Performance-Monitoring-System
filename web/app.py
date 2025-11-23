import eventlet
eventlet.monkey_patch()

from flask import Flask, render_template, request, jsonify
from flask_socketio import SocketIO, emit
import os
import sys
import numpy as np
import tensorflow as tf

# allow importing utils_audio
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), "..")))
from utils_audio import audio_to_melspectrogram

app = Flask(__name__, template_folder="templates", static_folder="static")
socketio = SocketIO(app, cors_allowed_origins="*", async_mode="eventlet")

# Load model
model = tf.keras.models.load_model("../models/audio_cnn.h5")
label_classes = np.load("../models/label_classes.npy")

UPLOAD_FOLDER = "uploads"
os.makedirs(UPLOAD_FOLDER, exist_ok=True)


@app.route("/")
def index():
    return render_template("index.html")


@app.route("/dashboard")
def dashboard():
    return render_template("dashboard.html")


# ----------------------------
# 🔥 RECEIVE EVENTS FROM MIC
# ----------------------------
@socketio.on("new_alert")
def handle_new_alert(data):
    print("🔥 SERVER RECEIVED ALERT:", data)

    # re-broadcast to dashboard
    socketio.emit("new_alert", data)
    print("📡 BROADCASTED TO DASHBOARD")


# ----------------------------
# 🔥 Upload → predict audio
# ----------------------------
@app.route("/predict", methods=["POST"])
def predict():
    if "file" not in request.files:
        return jsonify({"error": "No file uploaded"})

    audio_file = request.files["file"]
    file_path = os.path.join(UPLOAD_FOLDER, audio_file.filename)
    audio_file.save(file_path)

    mel = audio_to_melspectrogram(file_path)
    mel = np.expand_dims(mel, axis=0)

    predictions = model.predict(mel)
    idx = np.argmax(predictions)
    conf = float(np.max(predictions))
    label = label_classes[idx]

    event_data = {
        "source": "Upload",
        "prediction": str(label),
        "confidence": round(conf, 3)
    }

    socketio.emit("new_alert", event_data)
    print("📡 SENT UPLOAD ALERT:", event_data)

    return jsonify(event_data)


if __name__ == "__main__":
    print("🚀 Flask-SocketIO server running...")
    socketio.run(app, host="127.0.0.1", port=5000, debug=True)
