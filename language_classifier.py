from transformers import pipeline

print("Loading text classifier model... (first time can be slow)")
classifier = pipeline(
    "zero-shot-classification",
    model="joeddav/xlm-roberta-large-xnli"  # multilingual model
)

# Our categories
LABELS = [
    "threat",
    "violence",
    "abusive language",
    "bullying",
    "insult",
    "self harm",
    "safe"
]

def classify_text(text: str):
    """
    Classify text into one of the LABELS.
    Works with Sinhala + English.
    """
    text = text.strip()
    if not text:
        return "safe", 0.0

    print("\n🔍 Analysing text for harmful language...")
    result = classifier(text, candidate_labels=LABELS)
    best_label = result["labels"][0]
    best_score = float(result["scores"][0])

    print("Text       :", text)
    print("Predicted  :", best_label)
    print("Confidence :", best_score)

    return best_label, best_score

# Quick manual test
if __name__ == "__main__":
    while True:
        sentence = input("\nType a sentence (or 'q' to quit): ")
        if sentence.lower() == "q":
            break
        classify_text(sentence)
