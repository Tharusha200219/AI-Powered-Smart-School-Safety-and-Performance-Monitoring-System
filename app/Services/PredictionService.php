<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class PredictionService
{
    protected string $apiBaseUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->apiBaseUrl = config('services.prediction_api.url', 'http://localhost:5000');
        $this->timeout = config('services.prediction_api.timeout', 30);
    }

    /**
     * Get prediction for a single student
     *
     * @param array $studentData Student profile data
     * @param array $schoolData Attendance and marks data
     * @return array|null
     */
    public function getStudentPrediction(array $studentData, array $schoolData = []): ?array
    {
        // First check if API is healthy
        if (!$this->isApiHealthy()) {
            Log::warning('Prediction API is not healthy, cannot provide predictions');
            return null; // Return null to show "Service Unavailable" message
        }

        try {
            $payload = [
                'student_data' => $studentData,
                'school_data' => $schoolData
            ];

            // Log the payload for debugging
            Log::info('Prediction API payload', [
                'student_id' => $studentData['student_id'] ?? null,
                'payload' => $payload
            ]);

            Log::info('Calling prediction API for student', [
                'student_id' => $studentData['student_id'] ?? null,
                'api_url' => $this->apiBaseUrl . '/predict'
            ]);

            $response = Http::timeout($this->timeout)
                ->post($this->apiBaseUrl . '/predict', $payload);

            if ($response->successful()) {
                $data = $response->json();

                // Validate that we got proper prediction data
                if (isset($data['prediction']) && isset($data['prediction']['predicted_track'])) {
                    Log::info('Prediction API call successful', [
                        'student_id' => $studentData['student_id'] ?? null,
                        'predicted_track' => $data['prediction']['predicted_track'] ?? null
                    ]);

                    return $data;
                } else {
                    Log::warning('Prediction API returned invalid data structure', [
                        'student_id' => $studentData['student_id'] ?? null,
                        'response_data' => $data
                    ]);

                    return null; // Return null if data structure is invalid
                }
            } else {
                Log::error('Prediction API call failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'student_id' => $studentData['student_id'] ?? null
                ]);

                return null;
            }
        } catch (Exception $e) {
            Log::error('Exception during prediction API call', [
                'message' => $e->getMessage(),
                'student_id' => $studentData['student_id'] ?? null
            ]);

            return null;
        }
    }

    /**
     * Get predictions for multiple students
     *
     * @param array $studentsData Array of student data with school data
     * @return array|null
     */
    public function getBatchPredictions(array $studentsData): ?array
    {
        try {
            $payload = ['students' => $studentsData];

            Log::info('Calling batch prediction API', [
                'student_count' => count($studentsData),
                'api_url' => $this->apiBaseUrl . '/predict/batch'
            ]);

            $response = Http::timeout($this->timeout)
                ->post($this->apiBaseUrl . '/predict/batch', $payload);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('Batch prediction API call successful', [
                    'total_processed' => $data['total_processed'] ?? 0,
                    'total_errors' => $data['total_errors'] ?? 0
                ]);

                return $data;
            } else {
                Log::error('Batch prediction API call failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return null;
            }
        } catch (Exception $e) {
            Log::error('Exception during batch prediction API call', [
                'message' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Check if the prediction API is healthy
     *
     * @return bool
     */
    public function isApiHealthy(): bool
    {
        try {
            $response = Http::timeout(10)->get($this->apiBaseUrl . '/health');

            return $response->successful() &&
                isset($response->json()['status']) &&
                $response->json()['status'] === 'healthy';
        } catch (Exception $e) {
            Log::warning('Prediction API health check failed', [
                'message' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Prepare student data for prediction API
     *
     * @param \App\Models\Student $student
     * @return array
     */
    public function prepareStudentData($student): array
    {
        return [
            'student_id' => $student->student_id,
            'first_name' => $student->first_name,
            'last_name' => $student->last_name,
            'date_of_birth' => $student->date_of_birth?->format('Y-m-d'),
            'gender' => $student->gender,
            'grade_level' => $student->grade_level,
            'enrollment_date' => $student->enrollment_date?->format('Y-m-d'),
        ];
    }

    /**
     * Prepare school data (attendance and marks) for prediction API
     *
     * @param \App\Models\Student $student
     * @param array $additionalData Additional survey data if available
     * @return array
     */
    public function prepareSchoolData($student, array $additionalData = []): array
    {
        // Get attendance records for current academic year
        $attendanceRecords = $student->attendance()
            ->whereYear('attendance_date', now()->year)
            ->get()
            ->map(function ($attendance) {
                return [
                    'date' => $attendance->attendance_date->format('Y-m-d'),
                    'status' => $attendance->status,
                    'check_in_time' => $attendance->check_in_time?->format('H:i:s'),
                    'check_out_time' => $attendance->check_out_time?->format('H:i:s'),
                ];
            })
            ->toArray();

        // Calculate attendance percentage
        $totalDays = count($attendanceRecords);
        $presentDays = count(array_filter($attendanceRecords, function ($record) {
            return in_array($record['status'], ['present', 'late']);
        }));
        $attendancePercentage = $totalDays > 0 ? ($presentDays / $totalDays) * 100 : 0;

        // Get marks records for current academic year
        $currentYear = now()->year;
        $academicYear = ($currentYear - 1) . '-' . $currentYear; // Format: 2024-2025

        $marksRecords = $student->marks()
            ->with('subject') // Load the subject relationship
            ->where('academic_year', $academicYear)
            ->get()
            ->map(function ($mark) {
                return [
                    'subject_id' => $mark->subject_id,
                    'subject' => $mark->subject?->subject_name ?? 'Unknown Subject',
                    'mark' => $mark->percentage, // Use percentage as the mark value
                    'marks' => $mark->marks,
                    'total_marks' => $mark->total_marks,
                    'percentage' => $mark->percentage,
                    'grade' => $mark->grade,
                    'term' => $mark->term,
                ];
            })
            ->toArray();

        // Calculate average marks
        $averageMarks = 0;
        if (!empty($marksRecords)) {
            $totalPercentage = array_sum(array_column($marksRecords, 'percentage'));
            $averageMarks = $totalPercentage / count($marksRecords);
        }

        // Prepare school data with defaults and additional data
        $schoolData = [
            'attendance_records' => $attendanceRecords,
            'marks_records' => $marksRecords,
            'attendance_percentage' => round($attendancePercentage, 2),
            'average_marks' => round($averageMarks, 2),
            'study_hours' => $additionalData['study_hours'] ?? 3,
            'resources_access' => $additionalData['resources_access'] ?? 1,
            'extracurricular_activities' => $additionalData['extracurricular_activities'] ?? 1,
            'motivation_level' => $additionalData['motivation_level'] ?? 3,
            'internet_access' => $additionalData['internet_access'] ?? 1,
            'learning_style' => $additionalData['learning_style'] ?? 'Visual',
            'online_courses_completed' => $additionalData['online_courses_completed'] ?? 0,
            'class_discussions_participation' => $additionalData['class_discussions_participation'] ?? 2,
            'assignment_completion_rate' => $additionalData['assignment_completion_rate'] ?? 80.0,
            'edutech_usage' => $additionalData['edutech_usage'] ?? 1,
            'stress_level' => $additionalData['stress_level'] ?? 2,
        ];

        return $schoolData;
    }
}
