<?php

namespace Database\Seeders;

use App\Models\Mark;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class MarkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating sample marks for students...');

        $academicYear = '2024-2025';
        $terms = [1, 2, 3];

        // Get all students with their subjects
        $students = Student::with('subjects')->get();

        // Get a user to attribute as the one who entered the marks (e.g., admin or teacher)
        $enteredBy = User::role('Admin')->first() ?? User::first();

        $marksCreated = 0;

        foreach ($students as $student) {
            /** @var \App\Models\Student $student */
            // Ensure student has subjects
            $studentSubjects = $student->subjects;

            if ($studentSubjects->isEmpty()) {
                $this->command->warn("Student {$student->full_name} has no subjects. Assigning defaults...");
                
                // Get subjects based on grade guidelines
                $subjectsData = \App\Models\Subject::getSubjectsWithRules($student->grade_level);
                $availableSubjects = [];

                if ($student->grade_level >= 12 && $student->grade_level <= 13) {
                    // Advanced Level - must pick stream first
                    $stream = $student->stream;
                    if (!$stream && isset($subjectsData['subjects']['streams'])) {
                        $availableStreams = array_keys($subjectsData['subjects']['streams']);
                        $stream = $availableStreams[array_rand($availableStreams)];
                        // Update student stream if missing
                        $student->update(['stream' => $stream]);
                    }

                    if ($stream && isset($subjectsData['subjects']['streams'][$stream])) {
                        $streamSubjects = $subjectsData['subjects']['streams'][$stream];
                        $count = min(3, count($streamSubjects));
                        $indices = array_rand($streamSubjects, $count);
                        if ($count === 1) $indices = [$indices];
                        foreach ($indices as $idx) {
                            $availableSubjects[] = $streamSubjects[$idx]['id'];
                        }
                    }
                } else {
                    // Primary/Secondary - simpler selection
                    if (isset($subjectsData['subjects']['core'])) {
                        foreach ($subjectsData['subjects']['core'] as $sub) {
                            $availableSubjects[] = $sub['id'];
                        }
                    }
                    if (isset($subjectsData['subjects']['first_language']) && !empty($subjectsData['subjects']['first_language'])) {
                        $availableSubjects[] = $subjectsData['subjects']['first_language'][array_rand($subjectsData['subjects']['first_language'])]['id'];
                    }
                    if (isset($subjectsData['subjects']['religion']) && !empty($subjectsData['subjects']['religion'])) {
                        $availableSubjects[] = $subjectsData['subjects']['religion'][array_rand($subjectsData['subjects']['religion'])]['id'];
                    }
                    if (isset($subjectsData['subjects']['elective']) && !empty($subjectsData['subjects']['elective'])) {
                        $electiveCount = min(3, count($subjectsData['subjects']['elective']));
                        $electiveIndices = array_rand($subjectsData['subjects']['elective'], $electiveCount);
                        if ($electiveCount === 1) $electiveIndices = [$electiveIndices];
                        foreach ($electiveIndices as $idx) {
                            $availableSubjects[] = $subjectsData['subjects']['elective'][$idx]['id'];
                        }
                    }
                }

                if (!empty($availableSubjects)) {
                    $syncData = [];
                    foreach (array_unique($availableSubjects) as $subId) {
                        $syncData[$subId] = [
                            'enrollment_date' => $student->enrollment_date ?? now(),
                            'grade' => $student->grade_level
                        ];
                    }
                    $student->subjects()->sync($syncData);
                    $studentSubjects = $student->fresh()->subjects;
                }
            }

            // Create marks for each subject and term
            foreach ($studentSubjects as $subject) {
                foreach ($terms as $term) {
                    $totalMarks = 100;
                    $obtainedMarks = rand(30, 98); // Higher minimum for better display

                    Mark::updateOrCreate(
                        [
                            'student_id' => $student->student_id,
                            'subject_id' => $subject->id,
                            'academic_year' => $academicYear,
                            'term' => $term,
                        ],
                        [
                            'grade_level' => $student->grade_level,
                            'marks' => $obtainedMarks,
                            'total_marks' => $totalMarks,
                            'remarks' => $this->generateRemark($obtainedMarks, $totalMarks),
                            'entered_by' => $enteredBy ? $enteredBy->id : null,
                        ]
                    );
                    $marksCreated++;
                }
            }
        }

        $this->command->info("Created {$marksCreated} mark entries. Every student now has comprehensive marks.");
    }

    /**
     * Generate appropriate remark based on marks
     */
    private function generateRemark(float $marks, float $totalMarks): string
    {
        $percentage = ($marks / $totalMarks) * 100;

        if ($percentage >= 90) {
            $remarks = [
                'Excellent performance! Keep up the outstanding work.',
                'Outstanding achievement! Continue this excellent work.',
                'Exceptional work! You have shown great dedication.',
            ];
        } elseif ($percentage >= 75) {
            $remarks = [
                'Very good performance. Keep pushing for excellence.',
                'Good work! Continue to strive for improvement.',
                'Well done! Your efforts are showing positive results.',
            ];
        } elseif ($percentage >= 60) {
            $remarks = [
                'Satisfactory performance. More effort needed.',
                'Good progress. Keep working to improve further.',
                'Fair performance. Focus on areas that need improvement.',
            ];
        } elseif ($percentage >= 50) {
            $remarks = [
                'Adequate performance. Significant improvement needed.',
                'Passing grade. More dedication required for better results.',
                'Needs improvement. Please focus more on this subject.',
            ];
        } else {
            $remarks = [
                'Needs significant improvement. Extra attention required.',
                'Below expectations. Please seek additional help.',
                'Urgent attention needed. Consider tutoring or extra classes.',
            ];
        }

        return $remarks[array_rand($remarks)];
    }
}
