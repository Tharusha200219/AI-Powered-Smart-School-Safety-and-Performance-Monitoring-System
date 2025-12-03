from speech_to_text import speech_to_text
from language_classifier import classify_text

if __name__ == "__main__":
    audio_path = input("Enter path to .wav or .mp3 file: ")

    # 1) Speech → Text
    text = speech_to_text(audio_path)

    # 2) Text → Threat / Abuse / Safe
    label, score = classify_text(text)

    print("\n✅ FINAL RESULT")
    print("Transcript :", text)
    print("Category   :", label)
    print("Confidence :", round(score, 3))
