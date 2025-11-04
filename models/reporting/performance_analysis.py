import pandas as pd

def analyze_performance(scores):
    df = pd.DataFrame(scores)
    return {
        "average_scores": df.mean().to_dict(),
        "weak_areas": df.mean().nsmallest(3).index.tolist(),
        "strong_areas": df.mean().nlargest(3).index.tolist()
    }
