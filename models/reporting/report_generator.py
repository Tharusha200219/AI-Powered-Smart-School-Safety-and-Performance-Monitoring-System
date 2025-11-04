def generate_report(data):
    return f"""
Monthly Performance Report
--------------------------
Average Scores:
{data['average_scores']}

Weak Areas:
{data['weak_areas']}

Strong Areas:
{data['strong_areas']}
"""
