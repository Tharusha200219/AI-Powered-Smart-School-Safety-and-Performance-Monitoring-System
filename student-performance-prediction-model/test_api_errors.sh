#!/bin/bash
# Test script for API error handling

API_URL="http://localhost:5002"

echo "========================================="
echo "Testing Performance Prediction API Errors"
echo "========================================="
echo ""

# Test 1: No data
echo "Test 1: No data provided"
curl -X POST "$API_URL/predict" -H "Content-Type: application/json" 2>/dev/null | python3 -m json.tool
echo ""
echo "---"
echo ""

# Test 2: Missing subjects field
echo "Test 2: Missing 'subjects' field"
curl -X POST "$API_URL/predict" \
  -H "Content-Type: application/json" \
  -d '{"student_id": 1, "age": 15}' 2>/dev/null | python3 -m json.tool
echo ""
echo "---"
echo ""

# Test 3: Empty subjects array
echo "Test 3: Empty subjects array"
curl -X POST "$API_URL/predict" \
  -H "Content-Type: application/json" \
  -d '{"student_id": 1, "subjects": []}' 2>/dev/null | python3 -m json.tool
echo ""
echo "---"
echo ""

# Test 4: Missing subject_name
echo "Test 4: Missing 'subject_name' field"
curl -X POST "$API_URL/predict" \
  -H "Content-Type: application/json" \
  -d '{"student_id": 1, "subjects": [{"attendance": 85, "marks": 78}]}' 2>/dev/null | python3 -m json.tool
echo ""
echo "---"
echo ""

# Test 5: Invalid JSON
echo "Test 5: Invalid JSON format"
curl -X POST "$API_URL/predict" \
  -H "Content-Type: application/json" \
  -d '{invalid json}' 2>/dev/null | python3 -m json.tool
echo ""
echo "---"
echo ""

# Test 6: Correct format (should work)
echo "Test 6: Correct format (should succeed)"
curl -X POST "$API_URL/predict" \
  -H "Content-Type: application/json" \
  -d '{
    "student_id": 123,
    "age": 15,
    "grade": 10,
    "subjects": [
      {"subject_name": "Mathematics", "attendance": 85.5, "marks": 78.0}
    ]
  }' 2>/dev/null | python3 -m json.tool
echo ""
echo "---"
echo ""

echo "========================================="
echo "Testing /example endpoint"
echo "========================================="
curl -s "$API_URL/example" | python3 -m json.tool
echo ""
