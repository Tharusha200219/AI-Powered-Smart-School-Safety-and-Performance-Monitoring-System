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

        // Get a user to attribute as the one who entered the marks (e.g., admin or teacher)
        $enteredBy = User::role('Admin')->first() ?? User::first();

        $marksCreated = 0;

        // First, create marks for the first student (for testing AI predictions)
        $firstStudent = Student::first();

        if ($firstStudent) {
            $this->command->info("Creating marks for first student: {$firstStudent->full_name}");

            // Get some subjects for this grade level
            $subjects = \App\Models\Subject::where('grade_level', $firstStudent->grade_level)->take(5)->get();

            if ($subjects->isEmpty()) {
                // Fallback: get any subjects
                $subjects = \App\Models\Subject::take(5)->get();
            }

            foreach ($subjects as $subject) {
                foreach ($terms as $term) {
                    // Generate high marks for testing (80-95 range for good performance)
                    $obtainedMarks = rand(80, 95);
                    $totalMarks = 100;

                    Mark::create([
                        'student_id' => $firstStudent->student_id,
                        'subject_id' => $subject->id,
                        'grade_level' => $firstStudent->grade_level,
                        'academic_year' => $academicYear,
                        'term' => $term,
                        'marks' => $obtainedMarks,
                        'total_marks' => $totalMarks,
                        'percentage' => round(($obtainedMarks / $totalMarks) * 100, 2),
                        'grade' => $this->calculateGrade($obtainedMarks),
                        'remarks' => $this->generateRemark($obtainedMarks, $totalMarks),
                        'entered_by' => $enteredBy ? $enteredBy->id : null,
                    ]);

                    $marksCreated++;
                }
            }
        }

        // Create marks for other students (optional - limit to avoid too much data)
        $otherStudents = Student::where('student_id', '>', 1)->take(5)->get();

        foreach ($otherStudents as $student) {
            // Get subjects for this student
            $subjects = $student->subjects ?? collect();

            if ($subjects->isEmpty()) {
                // If no subjects assigned, skip this student
                continue;
            }

            foreach ($subjects->take(3) as $subject) { // Limit subjects per student
                foreach ($terms as $term) {
                    $totalMarks = 100;
                    $obtainedMarks = rand(40, 100);

                    Mark::create([
                        'student_id' => $student->student_id,
                        'subject_id' => $subject->id,
                        'grade_level' => $student->grade_level,
                        'academic_year' => $academicYear,
                        'term' => $term,
                        'marks' => $obtainedMarks,
                        'total_marks' => $totalMarks,
                        'percentage' => round(($obtainedMarks / $totalMarks) * 100, 2),
                        'grade' => $this->calculateGrade($obtainedMarks),
                        'remarks' => $this->generateRemark($obtainedMarks, $totalMarks),
                        'entered_by' => $enteredBy ? $enteredBy->id : null,
                    ]);

                    $marksCreated++;
                }
            }
        }

        $this->command->info("Created {$marksCreated} mark entries for students.");
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

    /**
     * Calculate grade based on marks (Sri Lankan grading system)
     */
    private function calculateGrade(float $marks): string
    {
        if ($marks >= 90) {
            return 'A+';
        } elseif ($marks >= 80) {
            return 'A';
        } elseif ($marks >= 70) {
            return 'B+';
        } elseif ($marks >= 60) {
            return 'B';
        } elseif ($marks >= 50) {
            return 'C+';
        } elseif ($marks >= 40) {
            return 'C';
        } elseif ($marks >= 30) {
            return 'D+';
        } elseif ($marks >= 20) {
            return 'D';
        } else {
            return 'E';
        }
    }
}
