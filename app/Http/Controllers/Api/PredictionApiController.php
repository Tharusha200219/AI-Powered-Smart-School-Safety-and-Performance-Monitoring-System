<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Services\PredictionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PredictionApiController extends Controller
{
    protected PredictionService $predictionService;

    public function __construct(PredictionService $predictionService)
    {
        $this->predictionService = $predictionService;
    }

    /**
     * Get prediction for a specific student
     *
     * @param Request $request
     * @param int $studentId
     * @return JsonResponse
     */
    public function getStudentPrediction(Request $request, int $studentId): JsonResponse
    {
        try {
            $student = Student::find($studentId);

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found',
                ], 404);
            }

            // Get additional data from request if provided
            $additionalData = $request->only([
                'study_hours',
                'resources_access',
                'extracurricular_activities',
                'motivation_level',
                'internet_access',
                'learning_style',
                'online_courses_completed',
                'class_discussions_participation',
                'assignment_completion_rate',
                'edutech_usage',
                'stress_level'
            ]);

            // Prepare data for API
            $studentData = $this->predictionService->prepareStudentData($student);
            $schoolData = $this->predictionService->prepareSchoolData($student, $additionalData);

            // Get prediction
            $prediction = $this->predictionService->getStudentPrediction($studentData, $schoolData);

            if ($prediction) {
                return response()->json([
                    'success' => true,
                    'data' => $prediction,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get prediction from AI service',
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error getting student prediction', [
                'student_id' => $studentId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
            ], 500);
        }
    }

    /**
     * Get predictions for multiple students
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getBatchPredictions(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'student_ids' => 'required|array|min:1|max:50',
            'student_ids.*' => 'integer|exists:students,student_id',
            'additional_data' => 'sometimes|array',
            'additional_data.*' => 'array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $studentIds = $request->input('student_ids');
            $additionalDataArray = $request->input('additional_data', []);

            $studentsData = [];

            foreach ($studentIds as $index => $studentId) {
                $student = Student::find($studentId);

                if ($student) {
                    $additionalData = $additionalDataArray[$index] ?? [];
                    $studentData = $this->predictionService->prepareStudentData($student);
                    $schoolData = $this->predictionService->prepareSchoolData($student, $additionalData);

                    $studentsData[] = [
                        'student_data' => $studentData,
                        'school_data' => $schoolData,
                    ];
                }
            }

            if (empty($studentsData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid students found',
                ], 404);
            }

            $predictions = $this->predictionService->getBatchPredictions($studentsData);

            if ($predictions) {
                return response()->json([
                    'success' => true,
                    'data' => $predictions,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get predictions from AI service',
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error getting batch predictions', [
                'error' => $e->getMessage(),
                'student_ids' => $request->input('student_ids'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
            ], 500);
        }
    }

    /**
     * Check if the prediction API is healthy
     *
     * @return JsonResponse
     */
    public function healthCheck(): JsonResponse
    {
        $isHealthy = $this->predictionService->isApiHealthy();

        return response()->json([
            'success' => $isHealthy,
            'service' => 'prediction_api',
            'status' => $isHealthy ? 'healthy' : 'unhealthy',
            'timestamp' => now()->toISOString(),
        ], $isHealthy ? 200 : 503);
    }

    /**
     * Get prediction statistics for a class or grade
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getClassPredictions(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'grade_level' => 'required|integer|min:1|max:13',
            'class_id' => 'sometimes|integer|exists:school_classes,class_id',
            'limit' => 'sometimes|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $query = Student::where('grade_level', $request->input('grade_level'))
                ->where('is_active', true);

            if ($request->has('class_id')) {
                $query->where('class_id', $request->input('class_id'));
            }

            $limit = $request->input('limit', 50);
            $students = $query->limit($limit)->get();

            if ($students->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No students found for the specified criteria',
                ], 404);
            }

            $studentsData = [];
            foreach ($students as $student) {
                $studentData = $this->predictionService->prepareStudentData($student);
                $schoolData = $this->predictionService->prepareSchoolData($student);

                $studentsData[] = [
                    'student_data' => $studentData,
                    'school_data' => $schoolData,
                ];
            }

            $predictions = $this->predictionService->getBatchPredictions($studentsData);

            if ($predictions) {
                // Group predictions by track
                $trackStats = [];
                foreach ($predictions['results'] as $result) {
                    $track = $result['prediction']['predicted_track'];
                    if (!isset($trackStats[$track])) {
                        $trackStats[$track] = 0;
                    }
                    $trackStats[$track]++;
                }

                return response()->json([
                    'success' => true,
                    'data' => [
                        'predictions' => $predictions,
                        'statistics' => [
                            'total_students' => count($predictions['results']),
                            'tracks_distribution' => $trackStats,
                            'grade_level' => $request->input('grade_level'),
                            'class_id' => $request->input('class_id'),
                        ],
                    ],
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get predictions from AI service',
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error getting class predictions', [
                'error' => $e->getMessage(),
                'grade_level' => $request->input('grade_level'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
            ], 500);
        }
    }
}
