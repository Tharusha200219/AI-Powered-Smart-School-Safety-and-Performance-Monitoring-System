from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity

def grade_subjective(student, expected):
    vec = TfidfVectorizer().fit_transform([student, expected])
    score = cosine_similarity(vec[0:1], vec[1:2])[0][0]
    return round(score * 10, 2)
