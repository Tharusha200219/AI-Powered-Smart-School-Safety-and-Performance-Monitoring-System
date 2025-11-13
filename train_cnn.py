import os
import numpy as np
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import LabelEncoder
import tensorflow as tf
from tensorflow.keras import layers, models

# ----------------------------
# PATHS
# ----------------------------
PROC_DIR = "data/processed"

# ----------------------------
# LOAD SPECTROGRAM DATA
# ----------------------------
X = []
y = []

print("Loading spectrograms from:", PROC_DIR)

for label in os.listdir(PROC_DIR):
    class_path = os.path.join(PROC_DIR, label)
    if not os.path.isdir(class_path):
        continue

    print(f"Found class: {label}")

    for filename in os.listdir(class_path):
        if not filename.endswith(".npy"):
            continue

        file_path = os.path.join(class_path, filename)

        try:
            mel_db = np.load(file_path)

            # Add channel dimension for CNN
            mel_db = np.expand_dims(mel_db, axis=-1)  # shape: (64, time_steps, 1)

            X.append(mel_db)
            y.append(label)

        except Exception as e:
            print("Error loading:", file_path, e)

X = np.array(X)
y = np.array(y)

print("\nLoaded:")
print("X shape:", X.shape)
print("y shape:", y.shape)
print("Classes:", np.unique(y))

# ----------------------------
# LABEL ENCODING
# ----------------------------
le = LabelEncoder()
y_encoded = le.fit_transform(y)

num_classes = len(le.classes_)

print("\nEncoded classes:")
for label, idx in zip(le.classes_, range(num_classes)):
    print(f"{label}: {idx}")

# ----------------------------
# SPLIT INTO TRAIN & TEST
# ----------------------------
X_train, X_test, y_train, y_test = train_test_split(
    X, y_encoded,
    test_size=0.2,
    random_state=42,
    stratify=y_encoded
)

input_shape = X_train.shape[1:]

print("\nFinal shapes:")
print("X_train:", X_train.shape)
print("X_test:", X_test.shape)
print("y_train:", y_train.shape)
print("y_test:", y_test.shape)

# ----------------------------
# BUILD CNN MODEL
# ----------------------------
model = models.Sequential([
    layers.Conv2D(16, (3, 3), activation="relu", input_shape=input_shape),
    layers.MaxPooling2D((2, 2)),

    layers.Conv2D(32, (3, 3), activation="relu"),
    layers.MaxPooling2D((2, 2)),

    layers.Conv2D(64, (3, 3), activation="relu"),
    layers.MaxPooling2D((2, 2)),

    layers.Flatten(),
    layers.Dense(64, activation="relu"),
    layers.Dropout(0.3),
    layers.Dense(num_classes, activation="softmax")
])

model.compile(
    optimizer="adam",
    loss="sparse_categorical_crossentropy",
    metrics=["accuracy"]
)

print("\nModel Summary:")
model.summary()

# ----------------------------
# TRAINING
# ----------------------------
history = model.fit(
    X_train, y_train,
    epochs=15,
    batch_size=32,
    validation_split=0.2
)

# ----------------------------
# TEST ACCURACY
# ----------------------------
test_loss, test_acc = model.evaluate(X_test, y_test)
print("\nTEST ACCURACY:", test_acc)

# ----------------------------
# SAVE MODEL & LABELS
# ----------------------------
os.makedirs("models", exist_ok=True)
model.save("models/audio_cnn.h5")
np.save("models/label_classes.npy", le.classes_)

print("\nSaved model to models/audio_cnn.h5")
print("Saved label classes to models/label_classes.npy")
