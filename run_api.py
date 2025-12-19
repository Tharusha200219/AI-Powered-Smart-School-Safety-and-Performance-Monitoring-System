#!/usr/bin/env python3
"""
Script to run the Student Performance Prediction API server.

This script starts the Flask API server for real-time predictions
based on student attendance and marks data.
"""

import os
import sys
import argparse
from pathlib import Path

# Add the src directory to Python path
project_root = Path(__file__).parent
src_dir = project_root / 'src'
sys.path.insert(0, str(src_dir))

def check_requirements():
    """Check if all required packages are installed."""
    required_packages = [
        'flask', 'flask_cors', 'pandas', 'numpy', 'sklearn'
    ]

    missing_packages = []
    for package in required_packages:
        try:
            __import__(package)
        except ImportError:
            missing_packages.append(package)

    if missing_packages:
        print("❌ Missing required packages. Please install them:")
        print(f"pip install {' '.join(missing_packages)}")
        return False

    return True

def check_model_files():
    """Check if trained model files exist."""
    model_dir = project_root / 'models'
    required_files = [
        'education_model.pkl',
        'label_encoder.pkl'
    ]
    optional_files = [
        'scaler.pkl'
    ]

    missing_files = []
    for file in required_files:
        if not (model_dir / file).exists():
            missing_files.append(file)

    if missing_files:
        print("❌ Missing trained model files. Please train the model first:")
        print(f"Missing files: {', '.join(missing_files)}")
        print("Run: python src/main.py --train")
        return False

    # Check optional files and warn if missing
    for file in optional_files:
        if not (model_dir / file).exists():
            print(f"⚠️  Optional file '{file}' not found. Using default preprocessing.")

    return True

def main():
    """Main function to start the API server."""
    parser = argparse.ArgumentParser(description='Student Performance Prediction API Server')
    parser.add_argument('--host', default='0.0.0.0', help='Host to bind to (default: 0.0.0.0)')
    parser.add_argument('--port', type=int, default=5000, help='Port to bind to (default: 5000)')
    parser.add_argument('--debug', action='store_true', help='Run in debug mode')

    args = parser.parse_args()

    print("🚀 Starting Student Performance Prediction API Server")
    print("=" * 60)

    # Check requirements
    if not check_requirements():
        sys.exit(1)

    # Check model files
    if not check_model_files():
        sys.exit(1)

    print("✅ All checks passed. Starting server...")

    # Set environment variables
    os.environ['FLASK_APP'] = 'src/api.py'
    os.environ['FLASK_ENV'] = 'development' if args.debug else 'production'

    # Import and run the API
    try:
        from src.api import app

        print(f"🌐 Server will be available at: http://{args.host}:{args.port}")
        print("📊 Health check endpoint: http://localhost:5000/health")
        print("🔮 Prediction endpoint: http://localhost:5000/predict")
        print("📈 Batch prediction endpoint: http://localhost:5000/predict/batch")
        print("\nPress Ctrl+C to stop the server\n")

        app.run(
            host=args.host,
            port=args.port,
            debug=args.debug,
            threaded=True
        )

    except Exception as e:
        print(f"❌ Failed to start server: {str(e)}")
        sys.exit(1)

if __name__ == '__main__':
    main()