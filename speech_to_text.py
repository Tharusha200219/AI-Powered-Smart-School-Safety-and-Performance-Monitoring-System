import os
import whisper

# 🔧 Make sure this Python process can see ffmpeg
os.environ["PATH"] += os.pathsep + r"C:\ffmpeg-2025-12-01-git-7043522fe0-full_build\bin"

print("Loading Whisper model... (this can take a bit)")
model = whisper.load_model("small")  # you can use "base" or "medium" later

def speech_to_text(audio_path: str) -> str:
    """
    Convert a speech audio file into text using Whisper.
    Works with Sinhala + English.
    """
    print(f"\n🎧 Transcribing audio: {audio_path}")
    result = model.transcribe(audio_path)
    text = result.get("text", "").strip()
    print("📝 Transcript:", text)
    return text

if __name__ == "__main__":
    path = input("Enter path to .wav or .mp3 file: ")
    speech_to_text(path)
