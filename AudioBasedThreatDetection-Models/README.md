🎧 Audio-Based Threat Detection System

Application Domain: Smart School Safety and Performance Monitoring

📌 Introduction

The Audio-Based Threat Detection System is a real-time audio intelligence solution designed to enhance school safety. The system continuously analyzes live audio streams to detect potentially dangerous situations using machine learning and signal processing techniques.

It focuses on both non-speech audio threats (such as screaming or glass breaking) and speech-based threats, while strictly preserving user privacy.

🎯 Project Objectives

🔊 Detect abnormal non-speech sounds such as screaming, crying, shouting, and glass breaking

🗣️ Identify threatening speech using speech-to-text and keyword analysis

⚖️ Reduce false positives using adaptive thresholds and calibration

🔒 Preserve privacy by avoiding audio storage

🖥️ Integrate seamlessly with a web-based administrative dashboard

🧠 System Overview

The system operates using a real-time, three-layer architecture:

Frontend (Browser)
Captures microphone audio using the Web Audio API

Backend (Python + Flask)
Processes audio, extracts features, and performs threat detection

Dashboard (Web Interface)
Displays alerts, detection results, and system controls

Audio is analyzed in short segments and discarded immediately after processing.

⚙️ Core Components
🎤 Audio Capture and Preprocessing

Audio captured via browser microphone

Resampled and normalized to a standard format

Sent to the backend in short time intervals

🔍 Non-Speech Threat Detection

Implemented using a 1D CNN + Bidirectional LSTM

Extracts MFCC and spectral features

Classifies audio into predefined categories

Detected classes include:

crying

screaming

shouting

glass breaking

normal ambient sound

🗣️ Speech-Based Threat Detection

Converts speech to text using a speech recognition engine

Analyzes text using keyword-based threat detection

Supports English and Sinhala

🎛️ Threat Aggregation and Calibration

Combines speech and non-speech results

Uses noise calibration to adapt to environments

Adjusts sensitivity to reduce false positives

🔐 Privacy and Ethical Considerations

Privacy is a core design principle of this system:

❌ No audio recordings are stored

🧠 Audio is processed only in memory

🗑️ Raw audio discarded after feature extraction

📊 Only minimal metadata is retained

✅ Microphone access requires user consent

🧩 Implementation Summary

Backend: Python, Flask, PyTorch

Audio Processing: MFCC and spectral analysis

Frontend Communication: REST APIs

Model Inference: Real-time deep learning

The system is optimized for low latency and reliable detection.

🛠️ Setup Overview

Basic backend setup steps:

Create and activate a virtual environment

Install dependencies using requirements.txt

Run the Flask server using app.py

The backend server runs locally at:
http://127.0.0.1:5002

🌐 API Capabilities

The backend exposes endpoints for:

✅ Health checks

🎧 Audio analysis

🎚️ Sensitivity adjustment

📡 Noise calibration

🔄 Session management

These endpoints allow seamless interaction with the dashboard.

📈 System Performance

⏱️ Average latency: under 3 seconds

🎵 Audio chunk duration: 4 seconds

📡 Sample rate: 16 kHz

🎯 Reduced false positives via calibration

🎓 Academic Context

This project was developed as part of an academic research study focusing on:

Real-time audio intelligence

Machine learning-based threat detection

Privacy-aware system design

All components were implemented, tested, and evaluated through practical experimentation.

⚠️ Usage Note

This system is intended for academic and research purposes only. Any real-world deployment must comply with legal, ethical, and institutional regulations.