<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [
            [
                'subject_code' => 'MATH-001',
                'subject_name' => 'Mathematics',
                'grade_level' => '1-5',
                'description' => 'Fundamental mathematics including numbers, basic operations, geometry, and problem solving',
                'credits' => 5,
                'type' => 'Core',
                'status' => 'active',
            ],
            [
                'subject_code' => 'ENG-001',
                'subject_name' => 'English Language Arts',
                'grade_level' => '1-5',
                'description' => 'Reading, writing, speaking, listening, and language skills development',
                'credits' => 5,
                'type' => 'Core',
                'status' => 'active',
            ],
            [
                'subject_code' => 'SCI-001',
                'subject_name' => 'Science',
                'grade_level' => '1-5',
                'description' => 'Introduction to physical science, life science, earth science, and scientific inquiry',
                'credits' => 4,
                'type' => 'Core',
                'status' => 'active',
            ],
            [
                'subject_code' => 'SOC-001',
                'subject_name' => 'Social Studies',
                'grade_level' => '1-5',
                'description' => 'History, geography, civics, and cultural studies',
                'credits' => 3,
                'type' => 'Core',
                'status' => 'active',
            ],
            [
                'subject_code' => 'ART-001',
                'subject_name' => 'Visual Arts',
                'grade_level' => '1-5',
                'description' => 'Drawing, painting, sculpture, and art appreciation',
                'credits' => 2,
                'type' => 'Elective',
                'status' => 'active',
            ],
            [
                'subject_code' => 'MUS-001',
                'subject_name' => 'Music',
                'grade_level' => '1-5',
                'description' => 'Music theory, singing, instruments, and music appreciation',
                'credits' => 2,
                'type' => 'Elective',
                'status' => 'active',
            ],
            [
                'subject_code' => 'PE-001',
                'subject_name' => 'Physical Education',
                'grade_level' => '1-5',
                'description' => 'Physical fitness, sports skills, health education, and teamwork',
                'credits' => 3,
                'type' => 'Core',
                'status' => 'active',
            ],
            [
                'subject_code' => 'LIB-001',
                'subject_name' => 'Library Skills',
                'grade_level' => '1-5',
                'description' => 'Information literacy, research skills, and reading comprehension',
                'credits' => 1,
                'type' => 'Core',
                'status' => 'active',
            ],
            [
                'subject_code' => 'TECH-001',
                'subject_name' => 'Technology Education',
                'grade_level' => '3-5',
                'description' => 'Basic computer skills, digital citizenship, and educational technology',
                'credits' => 2,
                'type' => 'Elective',
                'status' => 'active',
            ],
            [
                'subject_code' => 'LANG-001',
                'subject_name' => 'Spanish Language',
                'grade_level' => '2-5',
                'description' => 'Introduction to Spanish language and Hispanic culture',
                'credits' => 2,
                'type' => 'Elective',
                'status' => 'active',
            ],
        ];

        foreach ($subjects as $subjectData) {
            $subject = Subject::create($subjectData);
            $this->command->info("Created subject: {$subject->subject_name} ({$subject->subject_code})");
        }
    }
}
