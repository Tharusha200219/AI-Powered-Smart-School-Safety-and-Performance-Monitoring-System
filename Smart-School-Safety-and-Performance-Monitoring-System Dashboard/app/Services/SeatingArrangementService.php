<?php

namespace App\Services;

use App\Models\Student;
use App\Models\SeatingArrangement;
use App\Models\StudentSeatAssignment;
use App\Models\Mark;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SeatingArrangementService
{
    protected string $apiUrl;

    public function __construct()
    {
        // Default to localhost, can be configured in .env
        $this->apiUrl = config('services.seating_arrangement.url', 'http://localhost:5001');
    }

    /**
     * Generate seating arrangement for a grade/section
     *
     * @param string $gradeLevel
     * @param string|null $section
     * @param int $classId
     * @param string $academicYear
     * @param int $term
     * @param int $seatsPerRow
     * @param int $totalRows
     * @param int $userId
     * @return SeatingArrangement|null
     */
    public function generateSeatingArrangement(
        string $gradeLevel,
        ?string $section,
        ?int $classId,
        string $academicYear,
        int $term,
        int $seatsPerRow = 5,
        int $totalRows = 6,
        int $userId
    ): ?SeatingArrangement {
        try {
            // Get students for this grade/section
            $students = $this->getStudentsForClass($gradeLevel, $section, $classId);

            if ($students->isEmpty()) {
                Log::warning("No students found for grade {$gradeLevel}, section {$section}");
                return null;
            }

            // Prepare data for API
            $requestData = [
                'grade' => $gradeLevel,
                'section' => $section ?? 'A',
                'students' => $this->prepareStudentData($students),
                'seats_per_row' => $seatsPerRow,
                'total_rows' => $totalRows,
            ];

            // Call seating arrangement API
            $response = Http::timeout(30)
                ->post("{$this->apiUrl}/generate-seating", $requestData);

            if ($response->successful()) {
                $arrangementData = $response->json();

                // Store arrangement in database
                return $this->storeSeatingArrangement(
                    $gradeLevel,
                    $section,
                    $classId,
                    $academicYear,
                    $term,
                    $seatsPerRow,
                    $totalRows,
                    $arrangementData,
                    $userId
                );
            } else {
                Log::error("Seating arrangement API error: " . $response->body());
                return null;
            }
        } catch (\Exception $e) {
            Log::error("Error generating seating arrangement: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get students for a specific grade/section/class
     */
    protected function getStudentsForClass(string $gradeLevel, ?string $section, ?int $classId)
    {
        $query = Student::query()
            ->where('grade_level', $gradeLevel)
            ->where('is_active', true);

        if ($section) {
            $query->where('section', $section);
        }

        if ($classId) {
            $query->where('class_id', $classId);
        }

        return $query->with('marks')->get();
    }

    /**
     * Prepare student data for API request
     */
    protected function prepareStudentData($students): array
    {
        $studentData = [];

        foreach ($students as $student) {
            $averageMarks = $this->calculateAverageMarks($student);

            $studentData[] = [
                'student_id' => $student->student_id,
                'name' => $student->full_name,
                'average_marks' => $averageMarks,
                'grade' => $student->grade_level,
                'section' => $student->section ?? 'A',
            ];
        }

        return $studentData;
    }

    /**
     * Calculate average marks for a student
     */
    protected function calculateAverageMarks(Student $student): float
    {
        $averageMarks = $student->marks()->avg('marks');
        return $averageMarks ? round($averageMarks, 2) : 50.0;
    }

    /**
     * Store seating arrangement in database
     */
    protected function storeSeatingArrangement(
        string $gradeLevel,
        ?string $section,
        ?int $classId,
        string $academicYear,
        int $term,
        int $seatsPerRow,
        int $totalRows,
        array $arrangementData,
        int $userId
    ): SeatingArrangement {
        return DB::transaction(function () use (
            $gradeLevel,
            $section,
            $classId,
            $academicYear,
            $term,
            $seatsPerRow,
            $totalRows,
            $arrangementData,
            $userId
        ) {
            // Deactivate old arrangements for this class
            SeatingArrangement::where('grade_level', $gradeLevel)
                ->where('section', $section)
                ->where('academic_year', $academicYear)
                ->where('term', $term)
                ->update(['is_active' => false]);

            // Create new arrangement
            $arrangement = SeatingArrangement::create([
                'grade_level' => $gradeLevel,
                'section' => $section,
                'class_id' => $classId,
                'academic_year' => $academicYear,
                'term' => $term,
                'total_rows' => $totalRows,
                'seats_per_row' => $seatsPerRow,
                'arrangement_data' => $arrangementData,
                'generated_by' => $userId,
                'generated_at' => now(),
                'is_active' => true,
            ]);

            // Store individual seat assignments
            if (isset($arrangementData['seating_arrangement'])) {
                $this->storeSeatAssignments($arrangement, $arrangementData['seating_arrangement']);
            }

            return $arrangement;
        });
    }

    /**
     * Store individual seat assignments
     */
    protected function storeSeatAssignments(SeatingArrangement $arrangement, array $seatingData): void
    {
        foreach ($seatingData as $rowNumber => $row) {
            foreach ($row as $seatNumber => $seatData) {
                if (isset($seatData['student_id']) && $seatData['student_id']) {
                    StudentSeatAssignment::create([
                        'seating_arrangement_id' => $arrangement->id,
                        'student_id' => $seatData['student_id'],
                        'row_number' => $rowNumber + 1, // Convert from 0-indexed to 1-indexed
                        'seat_number' => $seatNumber + 1,
                        'seat_position' => "Row " . ($rowNumber + 1) . " - Seat " . ($seatNumber + 1),
                    ]);
                }
            }
        }
    }

    /**
     * Get student's seat assignment
     */
    public function getStudentSeatAssignment(Student $student, ?string $academicYear = null): ?StudentSeatAssignment
    {
        $query = StudentSeatAssignment::with('seatingArrangement')
            ->where('student_id', $student->student_id)
            ->whereHas('seatingArrangement', function ($q) use ($academicYear) {
                $q->where('is_active', true);
                if ($academicYear) {
                    $q->where('academic_year', $academicYear);
                }
            });

        return $query->latest()->first();
    }

    /**
     * Get active seating arrangement for a grade/section
     */
    public function getActiveSeatingArrangement(string $gradeLevel, ?string $section = null): ?SeatingArrangement
    {
        $query = SeatingArrangement::where('grade_level', $gradeLevel)
            ->where('is_active', true);

        if ($section) {
            $query->where('section', $section);
        }

        return $query->latest()->first();
    }

    /**
     * Check if seating API is available
     */
    public function checkApiHealth(): bool
    {
        try {
            $response = Http::timeout(5)->get("{$this->apiUrl}/health");
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
