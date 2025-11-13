import os
import numpy as np
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import LabelEncoder
import tensorflow as tf
from tensorflow.keras import layers, models

PROC_DIR = "data/processed"

X = []
y = []

# Load all spectrograms
for label in os.listdir(PROC_DIR):
    class_path = os.path.join(PROC_DIR, label)
    if not os.path.isdir(class_path):
        continue

    for filename in os.listdir(class_path):
        if not filename.endswith(".npy"):
            continue

        file_path = os.path.join(class_path, filename)
        mel_db = np.load(file_path)  # shape: (n_mels, time_frames)

        # Add channel dimension for CNN: (height, width, channels)
        mel_db = np.expand_dims(mel_db, axis=-1)  # (64, T, 1)

        X.append(mel_db)
        y.append(label)

X = np.array(X)
y = np.array(y)

print("X shape:", X.shape)
print("y shape:", y.shape)

# Encode labels (text -> numbers)
le = LabelEncoder()
y_encoded = le.fit_transform(y)

# Train / test split
X_train, X_test, y_train, y_test = train_test_split(
    X, y_encoded, test_size=0.2, random_state=42, stratify=y_encoded
)

num_classes = len(le.classes_)
input_shape = X_train.shape[1:]

print("Number of classes:", num_classes)
print("Input shape:", input_shape)
