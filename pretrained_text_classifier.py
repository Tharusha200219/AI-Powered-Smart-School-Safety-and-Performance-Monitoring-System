from transformers import pipeline

# ---- 1. Use a real, public, available model ----
MODEL_NAME = "unitary/toxic-bert"

print("Loading pretrained threat/abuse model... (first time can be slow)")
clf = pipeline("text-classification", model=MODEL_NAME)

def classify_text(text: str):
    result = clf(text)[0]  
    raw_label = result["label"]
    score = float(result["score"])

    # Map model output to your categories
    label_lower = raw_label.lower()

    if "threat" in label_lower:
        category = "threat"
    elif any(word in label_lower for word in [
        "toxic", "hate", "insult", "obscene", "abuse", "offensive"
    ]):
        category = "abusive"
    else:
        category = "safe"

    print("\n🔍 ANALYSIS")
    print("Text        :", text)
    print("Raw label   :", raw_label)
    print("Our label   :", category)
    print("Confidence  :", round(score, 3))

    return category, score, raw_label


# Test directly
if __name__ == "__main__":
    while True:
        text = input("\nEnter text (or 'q'): ")
        if text.lower() == "q":
            break
        classify_text(text)
