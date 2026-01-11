<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

class FaceRecognitionService
{
    protected string $apiUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->apiUrl = env('FACE_RECOGNITION_API_URL', 'http://localhost:5001');
        $this->timeout = 30;
    }

    /**
     * Add or update student in face recognition system
     */
    public function syncStudent(string $studentId, string $studentName, string $imagePath): array
    {
        try {
            if (!file_exists($imagePath)) {
                return ['success' => false, 'error' => 'Image file not found'];
            }

            $response = Http::timeout($this->timeout)
                ->attach('image', file_get_contents($imagePath), basename($imagePath))
                ->post("{$this->apiUrl}/students/add", [
                    'student_id' => $studentId,
                    'student_name' => $studentName,
                ]);

            if ($response->successful()) {
                Log::info("Student synced to face recognition: {$studentId}");
                return $response->json();
            }

            Log::error("Failed to sync student to face recognition", [
                'student_id' => $studentId,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return ['success' => false, 'error' => 'API request failed'];

        } catch (\Exception $e) {
            Log::error("Face recognition sync error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Remove student from face recognition system
     */
    public function removeStudent(string $studentId): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->apiUrl}/students/remove", [
                    'student_id' => $studentId,
                ]);

            if ($response->successful()) {
                Log::info("Student removed from face recognition: {$studentId}");
                return $response->json();
            }

            return ['success' => false, 'error' => 'API request failed'];

        } catch (\Exception $e) {
            Log::error("Face recognition removal error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Trigger model training
     */
    public function triggerTraining(int $epochs = 10, int $batchSize = 8): array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->apiUrl}/train", [
                    'epochs' => $epochs,
                    'batch_size' => $batchSize
                ]);

            if ($response->successful()) {
                Log::info("Face recognition training triggered");
                return $response->json();
            }

            Log::error("Training trigger failed: " . $response->body());
            return ['success' => false, 'error' => 'API request failed'];

        } catch (\Exception $e) {
            Log::error("Could not trigger training: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get face recognition system status
     */
    public function getStatus(): array
    {
        try {
            $response = Http::timeout(5)->get("{$this->apiUrl}/health");

            if ($response->successful()) {
                return $response->json();
            }

            return ['status' => 'unavailable'];

        } catch (\Exception $e) {
            return ['status' => 'unavailable', 'error' => $e->getMessage()];
        }
    }

    /**
     * Get dataset information
     */
    public function getDatasetInfo(): array
    {
        try {
            $response = Http::timeout(10)->get("{$this->apiUrl}/dataset/info");

            if ($response->successful()) {
                return $response->json();
            }

            return ['success' => false, 'error' => 'API request failed'];

        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get today's attendance from face recognition
     */
    public function getTodayAttendance(): array
    {
        try {
            $response = Http::timeout(10)->get("{$this->apiUrl}/attendance/today");

            if ($response->successful()) {
                return $response->json();
            }

            return ['success' => false, 'records' => []];

        } catch (\Exception $e) {
            return ['success' => false, 'records' => [], 'error' => $e->getMessage()];
        }
    }

    /**
     * Detect faces in an image
     */
    public function detectFaces(string $imagePath): array
    {
        try {
            if (!file_exists($imagePath)) {
                return ['success' => false, 'error' => 'Image file not found'];
            }

            $response = Http::timeout($this->timeout)
                ->attach('image', file_get_contents($imagePath), basename($imagePath))
                ->post("{$this->apiUrl}/detect");

            if ($response->successful()) {
                return $response->json();
            }

            return ['success' => false, 'detections' => []];

        } catch (\Exception $e) {
            Log::error("Face detection error: " . $e->getMessage());
            return ['success' => false, 'detections' => [], 'error' => $e->getMessage()];
        }
    }

    /**
     * Bulk sync all students
     */
    public function syncAllStudents(): array
    {
        try {
            $students = \App\Models\Student::whereNotNull('photo_path')
                ->where('is_active', true)
                ->get();

            $synced = 0;
            $failed = 0;

            foreach ($students as $student) {
                $imagePath = storage_path('app/public/' . $student->photo_path);
                
                if (file_exists($imagePath)) {
                    $result = $this->syncStudent(
                        $student->student_code,
                        $student->full_name,
                        $imagePath
                    );

                    if ($result['success'] ?? false) {
                        $synced++;
                    } else {
                        $failed++;
                    }
                } else {
                    $failed++;
                }
            }

            // Trigger final training
            if ($synced > 0) {
                $this->triggerTraining(10, 8); // Use optimized settings
            }

            return [
                'success' => true,
                'synced' => $synced,
                'failed' => $failed,
                'total' => $students->count()
            ];

        } catch (\Exception $e) {
            Log::error("Bulk sync error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
