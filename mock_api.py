#!/usr/bin/env python3
"""
Mock Student Performance Prediction API Server.

This is a temporary mock server that provides realistic predictions
for testing the Laravel integration when the actual ML model training
is not feasible due to limited dataset size.
"""

import os
import sys
import json
import argparse
from datetime import datetime
from flask import Flask, request, jsonify
from flask_cors import CORS

app = Flask(__name__)
CORS(app)

def get_grade_from_mark(mark):
    """Convert numerical mark to letter grade."""
    if mark >= 85:
        return 'A'
    elif mark >= 75:
        return 'B'
    elif mark >= 65:
        return 'C'
    elif mark >= 55:
        return 'D'
    elif mark >= 45:
        return 'E'
    else:
        return 'F'

def get_mock_prediction(student_data, school_data):
    """Generate detailed mock predictions with subject analysis and effort requirements."""

    # Extract detailed data
    attendance_percentage = float(school_data.get('attendance_percentage', 75))
    grade_level = int(student_data.get('grade_level', 10))
    marks_records = school_data.get('marks_records', [])

    # Calculate average marks from marks_records if available
    average_marks = float(school_data.get('average_marks', 0))
    if marks_records and len(marks_records) > 0:
        total_percentage = sum(float(record.get('mark', record.get('percentage', 0))) for record in marks_records)
        average_marks = total_percentage / len(marks_records)

    # Calculate performance score
    performance_score = (attendance_percentage * 0.3) + (average_marks * 0.7)

    # Analyze individual subjects if marks records are available
    subject_analysis = []
    focus_areas = []
    effort_requirements = []
    total_subjects_analyzed = 0

    if marks_records and len(marks_records) > 0:
        # Group marks by subject
        subject_marks = {}
        for record in marks_records:
            subject = record.get('subject', 'Unknown Subject')
            mark = record.get('mark', record.get('percentage', 0))
            if subject not in subject_marks:
                subject_marks[subject] = []
            subject_marks[subject].append(mark)

        # Analyze each subject
        total_subjects_analyzed = len(subject_marks)
        for subject, marks in subject_marks.items():
            # Convert all marks to float
            marks = [float(mark) for mark in marks]
            avg_mark = sum(marks) / len(marks)
            grade = get_grade_from_mark(avg_mark)

            # Determine subject performance level with more detailed analysis
            if avg_mark >= 85:
                performance_level = "Excellent"
                focus = "Maintain high performance and explore advanced topics"
                effort = "Minimal effort needed - focus on enrichment activities"
                status = "good"
            elif avg_mark >= 75:
                performance_level = "Good"
                focus = "Strengthen understanding of core concepts and practice regularly"
                effort = "Moderate study time required - 1-2 hours daily"
                status = "good"
            elif avg_mark >= 65:
                performance_level = "Satisfactory"
                focus = "Improve understanding of key topics and seek help when needed"
                effort = "Regular study sessions needed - 2-3 hours daily"
                status = "average"
            elif avg_mark >= 55:
                performance_level = "Needs Improvement"
                focus = "Focus on fundamental concepts and practice basic skills"
                effort = "Significant study time and tutoring recommended - 3-4 hours daily"
                status = "bad"
            elif avg_mark >= 45:
                performance_level = "Poor"
                focus = "Immediate attention required - basic concepts need reinforcement"
                effort = "Intensive study program and extra help essential - 4+ hours daily"
                status = "bad"
            else:
                performance_level = "Critical"
                focus = "Urgent intervention needed - core fundamentals missing"
                effort = "Comprehensive remedial program required - full-time support"
                status = "bad"

            subject_analysis.append({
                'subject': subject,
                'average_mark': round(avg_mark, 2),
                'grade': grade,
                'performance_level': performance_level,
                'status': status,
                'focus_area': focus,
                'effort_required': effort,
                'mark_count': len(marks)
            })

            # Add to focus areas if needs attention
            if avg_mark < 65:
                focus_areas.append(f"{subject} ({grade}) - {focus}")
                effort_requirements.append(f"{subject}: {effort}")

    # Determine education track based on performance with more dynamic confidence
    if performance_score >= 85 and grade_level >= 10:
        predicted_track = 'Advanced Level Stream'
        base_confidence = 0.88
        track_description = "Suitable for advanced academic studies and university entrance"
    elif performance_score >= 75:
        predicted_track = 'Technology Stream'
        base_confidence = 0.82
        track_description = "Good foundation for technical and engineering fields"
    elif performance_score >= 65:
        predicted_track = 'Commerce Stream'
        base_confidence = 0.75
        track_description = "Appropriate for business and commerce related studies"
    elif performance_score >= 55:
        predicted_track = 'Arts Stream'
        base_confidence = 0.68
        track_description = "Suitable for arts, humanities, and social sciences"
    elif performance_score >= 45:
        predicted_track = 'Vocational Training'
        base_confidence = 0.62
        track_description = "Focus on practical skills and vocational education"
    else:
        predicted_track = 'Needs Extra Support'
        base_confidence = 0.55
        track_description = "Requires additional academic support and intervention"

    # Adjust confidence based on subject analysis
    if total_subjects_analyzed > 0:
        bad_subjects = sum(1 for subj in subject_analysis if subj['status'] == 'bad')
        good_subjects = sum(1 for subj in subject_analysis if subj['status'] == 'good')

        # Reduce confidence if many subjects are poor
        if bad_subjects > good_subjects:
            confidence = max(0.4, base_confidence - 0.2)
        # Increase confidence if most subjects are good
        elif good_subjects > bad_subjects:
            confidence = min(0.95, base_confidence + 0.1)
        else:
            confidence = base_confidence
    else:
        confidence = base_confidence

    # Generate alternative options
    tracks = ['Advanced Level Stream', 'Technology Stream', 'Commerce Stream',
             'Arts Stream', 'Vocational Training', 'Needs Extra Support']

    class_probabilities = {}
    remaining_prob = 1 - confidence

    for track in tracks:
        if track == predicted_track:
            class_probabilities[track] = confidence
        else:
            class_probabilities[track] = remaining_prob / (len(tracks) - 1)

    # Calculate improvement targets
    improvement_targets = []
    if average_marks < 75 and total_subjects_analyzed > 0:
        target_improvement = min(75 - average_marks, 20)  # Max 20 point improvement target
        improvement_targets.append({
            'target_average': round(average_marks + target_improvement, 1),
            'effort_level': 'High' if target_improvement > 15 else 'Moderate' if target_improvement > 10 else 'Moderate-Low',
            'timeframe': 'Next 2-3 months',
            'description': f"Target improvement of {target_improvement:.1f} points to reach satisfactory performance level"
        })

    # Overall prediction summary
    overall_prediction = "Based on current performance, "
    if performance_score >= 75:
        overall_prediction += f"the student shows strong potential for {predicted_track.lower()} with {confidence*100:.1f}% confidence. "
    elif performance_score >= 55:
        overall_prediction += f"the student may benefit from {predicted_track.lower()} with {confidence*100:.1f}% confidence, but needs improvement in weak subjects. "
    else:
        overall_prediction += f"the student requires significant support and intervention. Current trajectory suggests {predicted_track.lower()} with {confidence*100:.1f}% confidence. "

    if focus_areas:
        overall_prediction += f"Special attention needed for: {', '.join([area.split(' - ')[0] for area in focus_areas[:3]])}."

    return {
        'prediction': {
            'predicted_track': predicted_track,
            'confidence': confidence,
            'track_description': track_description,
            'overall_prediction': overall_prediction,
            'class_probabilities': class_probabilities
        },
        'subject_analysis': subject_analysis,
        'focus_areas': focus_areas,
        'effort_requirements': effort_requirements,
        'improvement_targets': improvement_targets,
        'features_used': {
            'attendance_percentage': attendance_percentage,
            'average_marks': round(average_marks, 2),
            'grade_level': grade_level,
            'performance_score': round(performance_score, 2),
            'total_subjects_analyzed': total_subjects_analyzed
        },
        'model_info': {
            'model_type': 'enhanced_mock_prediction_engine',
            'version': '2.1.0',
            'status': 'mock_mode_with_detailed_analysis'
        }
    }

@app.route('/health', methods=['GET'])
def health_check():
    """Health check endpoint."""
    return jsonify({
        'status': 'healthy',
        'timestamp': datetime.now().isoformat(),
        'service': 'mock_student_performance_prediction_api'
    })

@app.route('/predict', methods=['POST'])
def predict():
    """Single student prediction endpoint."""
    try:
        data = request.get_json()

        if not data or 'student_data' not in data or 'school_data' not in data:
            return jsonify({
                'error': 'Invalid request data. Required: student_data and school_data'
            }), 400

        prediction = get_mock_prediction(data['student_data'], data['school_data'])

        return jsonify(prediction)

    except Exception as e:
        return jsonify({
            'error': f'Prediction failed: {str(e)}'
        }), 500

@app.route('/predict/batch', methods=['POST'])
def predict_batch():
    """Batch prediction endpoint."""
    try:
        data = request.get_json()

        if not data or 'students' not in data:
            return jsonify({
                'error': 'Invalid request data. Required: students array'
            }), 400

        predictions = []
        total_processed = 0
        total_errors = 0

        for student_data in data['students']:
            try:
                prediction = get_mock_prediction(
                    student_data['student_data'],
                    student_data['school_data']
                )
                predictions.append(prediction)
                total_processed += 1
            except Exception as e:
                predictions.append({
                    'error': f'Failed to process student: {str(e)}'
                })
                total_errors += 1

        return jsonify({
            'predictions': predictions,
            'total_processed': total_processed,
            'total_errors': total_errors,
            'model_info': {
                'model_type': 'mock_prediction_engine',
                'version': '1.0.0',
                'status': 'mock_mode'
            }
        })

    except Exception as e:
        return jsonify({
            'error': f'Batch prediction failed: {str(e)}'
        }), 500

@app.route('/class', methods=['GET'])
def get_class_predictions():
    """Mock class predictions endpoint."""
    return jsonify({
        'message': 'Mock class predictions endpoint - not implemented',
        'status': 'mock_mode'
    })

def main():
    parser = argparse.ArgumentParser(description='Mock Student Performance Prediction API Server')
    parser.add_argument('--host', default='0.0.0.0', help='Host to bind to')
    parser.add_argument('--port', type=int, default=5000, help='Port to bind to')
    parser.add_argument('--debug', action='store_true', help='Enable debug mode')

    args = parser.parse_args()

    print("🚀 Starting Mock Student Performance Prediction API Server")
    print("=" * 60)
    print("⚠️  This is a MOCK server for testing purposes only!")
    print("📊 Mock predictions based on attendance and marks data")
    print(f"🌐 Server will be available at: http://{args.host}:{args.port}")
    print(f"📊 Health check endpoint: http://localhost:{args.port}/health")
    print(f"🔮 Prediction endpoint: http://localhost:{args.port}/predict")
    print(f"📈 Batch prediction endpoint: http://localhost:{args.port}/predict/batch")
    print("\nPress Ctrl+C to stop the server\n")

    app.run(host=args.host, port=args.port, debug=args.debug)

if __name__ == '__main__':
    main()