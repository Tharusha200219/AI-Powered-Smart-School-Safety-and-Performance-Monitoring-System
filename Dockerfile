# Use Python 3.11 slim image
FROM python:3.11-slim

# Set working directory
WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y \
    gcc \
    && rm -rf /var/lib/apt/lists/*

# Copy requirements first for better caching
COPY requirements.txt .

# Install Python dependencies
RUN pip install --no-cache-dir -r requirements.txt

# Copy application files
COPY train_model.py .
COPY predict_api.py .
COPY predict_simple.py .
COPY data/ ./data/

# Train the model
RUN python train_model.py

# Expose Flask port
EXPOSE 5000

# Run the API
CMD ["python", "predict_api.py"]
