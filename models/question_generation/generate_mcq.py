def generate_mcq(model, tokenizer, topic):
    prompt = f"Generate 4 MCQs with answers for: {topic}"
    inputs = tokenizer(prompt, return_tensors="pt")
    output = model.generate(**inputs, max_length=300)
    return tokenizer.decode(output[0])
