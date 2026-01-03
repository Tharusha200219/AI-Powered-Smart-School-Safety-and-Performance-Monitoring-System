<?php

namespace Database\Seeders;

use App\Models\Mark;
use App\Models\Student;
use Illuminate\Database\Seeder;

class MarkSeederEnhanced extends Seeder
{
    /**
     * Run the database seeds.
     * Generate random marks for all students in their enrolled subjects
     */
    public function run(): void
    {
        $this->command->info('Generating marks for students...');

        $students = Student::with('subjects')->get();
        $currentYear = date('Y');
        $academicYear = $currentYear . '-' . ($currentYear + 1);

        // Generate marks for 3 terms
        $terms = [1, 2, 3];

        foreach ($students as $student) {
            if ($student->subjects->isEmpty()) {
                $this->command->warn("Student {$student->student_code} has no subjects assigned");
                continue;
            }

            foreach ($terms as $term) {
                $this->generateMarksForTerm($student, $academicYear, $term);
            }
        }

        $this->command->info('Marks generation completed!');
    }

    /**
     * Generate marks for a student for a specific term
     */
    private function generateMarksForTerm(Student $student, string $academicYear, int $term): void
    {
        foreach ($student->subjects as $subject) {
            // Check if marks already exist
            $existingMark = Mark::where('student_id', $student->student_id)
                ->where('subject_id', $subject->id)
                ->where('academic_year', $academicYear)
                ->where('term', $term)
                ->first();

            if ($existingMark) {
                continue; // Skip if marks already exist
            }

            // Generate random marks based on a realistic distribution
            // Most students score between 40-85, with some outliers
            $marks = $this->generateRealisticMarks();
            $totalMarks = 100.00;

            Mark::create([
                'student_id' => $student->student_id,
                'subject_id' => $subject->id,
                'grade_level' => $student->grade_level,
                'academic_year' => $academicYear,
                'term' => $term,
                'marks' => $marks,
                'total_marks' => $totalMarks,
                // percentage and grade will be auto-calculated by the model
                'remarks' => $this->getRemarkForMarks($marks),
                'entered_by' => 1, // Assuming admin user ID is 1
            ]);
        }

        $this->command->info("Generated marks for student {$student->student_code} - Term {$term}");
    }

    /**
     * Generate realistic marks with normal distribution
     */
    private function generateRealisticMarks(): float
    {
        // Create a realistic distribution
        // 5% - Excellent (90-100)
        // 25% - Very Good (75-89)
        // 40% - Good (60-74)
        // 20% - Average (50-59)
        // 10% - Below Average (40-49)

        $rand = rand(1, 100);

        if ($rand <= 5) {
            // Excellent
            return round(rand(9000, 10000) / 100, 2);
        } elseif ($rand <= 30) {
            // Very Good
            return round(rand(7500, 8900) / 100, 2);
        } elseif ($rand <= 70) {
            // Good
            return round(rand(6000, 7400) / 100, 2);
        } elseif ($rand <= 90) {
            // Average
            return round(rand(5000, 5900) / 100, 2);
        } else {
            // Below Average
            return round(rand(4000, 4900) / 100, 2);
        }
    }

    /**
     * Get appropriate remark based on marks
     */
    private function getRemarkForMarks(float $marks): ?string
    {
        if ($marks >= 90) {
            return 'Outstanding performance!';
        } elseif ($marks >= 75) {
            return 'Very good work!';
        } elseif ($marks >= 60) {
            return 'Good effort, keep it up!';
        } elseif ($marks >= 50) {
            return 'Satisfactory, needs improvement.';
        } else {
            return 'Needs significant improvement.';
        }
    }
}
