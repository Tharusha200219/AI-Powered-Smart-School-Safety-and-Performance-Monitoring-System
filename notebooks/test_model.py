from models.lesson_understanding.extract_keywords import extract_keywords

lesson = "Electricity is the flow of electrons. Voltage is the potential difference."
keywords = extract_keywords(lesson)
print(keywords)
