<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Assign subjects to students based on their grade level
     */
    public function run(): void
    {
        $this->command->info('Assigning subjects to students based on grade level...');

        $students = Student::all();

        foreach ($students as $student) {
            $this->assignSubjectsToStudent($student);
        }

        $this->command->info('Student-subject assignments completed!');
    }

    /**
     * Assign appropriate subjects to a student based on their grade level
     */
    private function assignSubjectsToStudent(Student $student): void
    {
        $gradeLevel = $student->grade_level;
        
        // Get subjects for this grade level
        $subjects = Subject::where(function($query) use ($gradeLevel) {
            // Match exact grade
            $query->where('grade_level', (string)$gradeLevel)
                  // Or match range (e.g., "1-5", "6-9")
                  ->orWhere(function($q) use ($gradeLevel) {
                      $q->where('grade_level', 'like', '%-%')
                        ->whereRaw("? BETWEEN CAST(SUBSTRING_INDEX(grade_level, '-', 1) AS UNSIGNED) AND CAST(SUBSTRING_INDEX(grade_level, '-', -1) AS UNSIGNED)", [$gradeLevel]);
                  });
        })->where('status', 'active')->get();

        if ($subjects->isEmpty()) {
            $this->command->warn("No subjects found for grade {$gradeLevel}");
            return;
        }

        // For grades 12-13, filter by stream if applicable
        if ($gradeLevel >= 12) {
            // Randomly assign a stream
            $streams = ['Science Stream', 'Arts Stream', 'Commerce Stream', 'Technology Stream'];
            $studentStream = $streams[array_rand($streams)];
            
            // Filter subjects - include Core and the student's stream
            $subjects = $subjects->filter(function($subject) use ($studentStream) {
                return $subject->type === 'Core' || $subject->type === $studentStream;
            });
        }

        // Attach subjects to student
        foreach ($subjects as $subject) {
            // Check if already attached
            if (!$student->subjects()->where('subject_id', $subject->id)->exists()) {
                $student->subjects()->attach($subject->id, [
                    'enrollment_date' => $student->enrollment_date,
                    'grade' => $student->grade_level,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info("Assigned {$subjects->count()} subjects to student {$student->student_code} (Grade {$gradeLevel})");
    }
}
