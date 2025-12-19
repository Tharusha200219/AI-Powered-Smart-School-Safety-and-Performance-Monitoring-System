<?php

/**
 * Test script for AI Prediction Integration
 *
 * Run this script to test the integration between the school management system
 * and the student performance prediction API.
 *
 * Usage: php test_prediction_integration.php
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\PredictionService;
use App\Models\Student;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "🧪 Testing AI Prediction Integration\n";
echo "=====================================\n\n";

// Test 1: Check API Health
echo "1. Testing API Health Check...\n";
$predictionService = app(PredictionService::class);
$isHealthy = $predictionService->isApiHealthy();

if ($isHealthy) {
    echo "✅ Prediction API is healthy\n\n";
} else {
    echo "❌ Prediction API is not responding\n";
    echo "   Make sure the API server is running: python ../student-performance-prediction-model/run_api.py\n\n";
    exit(1);
}

// Test 2: Get a sample student
echo "2. Getting sample student data...\n";
$student = Student::where('is_active', true)->first();

if (!$student) {
    echo "❌ No active students found in database\n";
    echo "   Please add some students first\n\n";
    exit(1);
}

echo "✅ Found student: {$student->full_name} (ID: {$student->student_id})\n\n";

// Test 3: Prepare data for prediction
echo "3. Preparing prediction data...\n";
$studentData = $predictionService->prepareStudentData($student);
$schoolData = $predictionService->prepareSchoolData($student);

echo "   Student Data: " . json_encode($studentData, JSON_PRETTY_PRINT) . "\n";
echo "   School Data Keys: " . implode(', ', array_keys($schoolData)) . "\n\n";

// Test 4: Make prediction
echo "4. Making prediction...\n";
$prediction = $predictionService->getStudentPrediction($studentData, $schoolData);

if ($prediction) {
    echo "✅ Prediction successful!\n";
    echo "   Predicted Track: {$prediction['prediction']['predicted_track']}\n";
    echo "   Confidence: " . number_format($prediction['prediction']['confidence'] * 100, 1) . "%\n";

    if (isset($prediction['prediction']['class_probabilities'])) {
        echo "   All Probabilities:\n";
        foreach ($prediction['prediction']['class_probabilities'] as $track => $prob) {
            echo "     - $track: " . number_format($prob * 100, 1) . "%\n";
        }
    }
    echo "\n";
} else {
    echo "❌ Prediction failed\n\n";
    exit(1);
}

// Test 5: Test batch prediction
echo "5. Testing batch prediction...\n";
$students = Student::where('is_active', true)->limit(3)->get();

if ($students->count() >= 2) {
    $batchData = [];
    foreach ($students as $stu) {
        $batchData[] = [
            'student_data' => $predictionService->prepareStudentData($stu),
            'school_data' => $predictionService->prepareSchoolData($stu),
        ];
    }

    $batchResult = $predictionService->getBatchPredictions($batchData);

    if ($batchResult) {
        echo "✅ Batch prediction successful!\n";
        echo "   Processed: {$batchResult['total_processed']} students\n";
        echo "   Errors: {$batchResult['total_errors']} students\n\n";
    } else {
        echo "❌ Batch prediction failed\n\n";
    }
} else {
    echo "⚠️  Not enough students for batch test (need at least 2)\n\n";
}

echo "🎉 Integration test completed!\n";
echo "\nNext steps:\n";
echo "1. Start the Laravel development server: php artisan serve\n";
echo "2. Test the API endpoints with authentication\n";
echo "3. Integrate predictions into your frontend application\n";
echo "4. Set up automated prediction triggers (e.g., after exams, attendance updates)\n";
