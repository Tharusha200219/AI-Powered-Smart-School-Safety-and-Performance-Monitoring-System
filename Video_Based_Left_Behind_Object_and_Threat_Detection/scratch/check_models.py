import torch
from ultralytics import YOLO
from pathlib import Path

models = [
    "yolov8n.pt",
    "yolov8s.pt",
    "yolov8n-pose.pt",
    "models/object_detection_training/weights/best.pt"
]

for m in models:
    print(f"Testing {m}...")
    if not Path(m).exists():
        print(f"  ✗ File missing")
        continue
    try:
        model = YOLO(m)
        print(f"  ✓ Loaded successfully (classes: {len(model.names)})")
    except Exception as e:
        print(f"  ✗ Failed to load: {e}")
