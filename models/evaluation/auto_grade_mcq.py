def grade_mcq(student_answer, correct_answer):
    return 1 if student_answer.strip().lower() == correct_answer.strip().lower() else 0
