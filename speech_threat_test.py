from speech_to_text import speech_to_text
from pretrained_text_classifier import classify_text

print("Speech → Threat Detection Test")

audio_path = input("Enter path to .wav or .mp3 file: ")

# ---- 1. Convert audio to text ----
text = speech_to_text(audio_path)

print("\n🎧 TRANSCRIPT:")
print(text)

# ---- 2. Classify text for threat/abuse/safety ----
category, score, raw_label = classify_text(text)

print("\n🚨 FINAL RESULT")
print("Transcript :", text)
print("Raw label  :", raw_label)
print("Category   :", category)
print("Confidence :", round(score, 3))
