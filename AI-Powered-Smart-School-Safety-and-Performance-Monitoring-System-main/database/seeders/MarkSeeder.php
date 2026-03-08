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
            $studentSubjects = $student->subjects;

            // If the student doesn't have any subjects, assign some random ones
            if ($studentSubjects->isEmpty()) {
                // Try to get subjects for their grade level, otherwise just get any random subjects
                if ($student->grade_level) {
                    $randomSubjects = \App\Models\Subject::where('grade_level', $student->grade_level)->inRandomOrder()->take(5)->get();
                } else {
                    $randomSubjects = \App\Models\Subject::inRandomOrder()->take(5)->get();
                }

                if ($randomSubjects->isNotEmpty()) {
                    $student->subjects()->sync($randomSubjects->pluck('id')->toArray());
                    // Reload subjects to get the newly attached ones
                    $studentSubjects = $student->fresh()->subjects;
                }
            }

            // Create marks for each subject the student is enrolled in
            foreach ($studentSubjects as $subject) {
                // Create marks for each term
                foreach ($terms as $term) {
                    // Generate random marks (between 0-98)
                    $totalMarks = 100;
                    $obtainedMarks = rand(0, 98);

                    // Create or update mark entry
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

        $this->command->info("Created {$marksCreated} mark entries for students across all terms.");
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
