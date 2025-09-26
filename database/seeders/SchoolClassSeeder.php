<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use Illuminate\Database\Seeder;

class SchoolClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = [
            [
                'class_code' => 'CL-001',
                'class_name' => 'Grade 1A',
                'grade_level' => '1',
                'academic_year' => '2024-2025',
                'section' => 'A',
                'room_number' => '101',
                'capacity' => 25,
                'description' => 'First grade class section A with focus on foundational learning',
                'status' => 'active',
            ],
            [
                'class_code' => 'CL-002',
                'class_name' => 'Grade 1B',
                'grade_level' => '1',
                'academic_year' => '2024-2025',
                'section' => 'B',
                'room_number' => '102',
                'capacity' => 25,
                'description' => 'First grade class section B with emphasis on creative learning',
                'status' => 'active',
            ],
            [
                'class_code' => 'CL-003',
                'class_name' => 'Grade 2A',
                'grade_level' => '2',
                'academic_year' => '2024-2025',
                'section' => 'A',
                'room_number' => '201',
                'capacity' => 28,
                'description' => 'Second grade class section A with advanced reading programs',
                'status' => 'active',
            ],
            [
                'class_code' => 'CL-004',
                'class_name' => 'Grade 2B',
                'grade_level' => '2',
                'academic_year' => '2024-2025',
                'section' => 'B',
                'room_number' => '202',
                'capacity' => 28,
                'description' => 'Second grade class section B with STEM focus',
                'status' => 'active',
            ],
            [
                'class_code' => 'CL-005',
                'class_name' => 'Grade 3A',
                'grade_level' => '3',
                'academic_year' => '2024-2025',
                'section' => 'A',
                'room_number' => '301',
                'capacity' => 30,
                'description' => 'Third grade class section A with comprehensive curriculum',
                'status' => 'active',
            ],
            [
                'class_code' => 'CL-006',
                'class_name' => 'Grade 3B',
                'grade_level' => '3',
                'academic_year' => '2024-2025',
                'section' => 'B',
                'room_number' => '302',
                'capacity' => 30,
                'description' => 'Third grade class section B with arts integration',
                'status' => 'active',
            ],
            [
                'class_code' => 'CL-007',
                'class_name' => 'Grade 4A',
                'grade_level' => '4',
                'academic_year' => '2024-2025',
                'section' => 'A',
                'room_number' => '401',
                'capacity' => 32,
                'description' => 'Fourth grade class section A with technology integration',
                'status' => 'active',
            ],
            [
                'class_code' => 'CL-008',
                'class_name' => 'Grade 4B',
                'grade_level' => '4',
                'academic_year' => '2024-2025',
                'section' => 'B',
                'room_number' => '402',
                'capacity' => 32,
                'description' => 'Fourth grade class section B with project-based learning',
                'status' => 'active',
            ],
            [
                'class_code' => 'CL-009',
                'class_name' => 'Grade 5A',
                'grade_level' => '5',
                'academic_year' => '2024-2025',
                'section' => 'A',
                'room_number' => '501',
                'capacity' => 35,
                'description' => 'Fifth grade class section A preparing for middle school transition',
                'status' => 'active',
            ],
            [
                'class_code' => 'CL-010',
                'class_name' => 'Grade 5B',
                'grade_level' => '5',
                'academic_year' => '2024-2025',
                'section' => 'B',
                'room_number' => '502',
                'capacity' => 35,
                'description' => 'Fifth grade class section B with advanced mathematics and science',
                'status' => 'active',
            ],
        ];

        foreach ($classes as $classData) {
            $schoolClass = SchoolClass::create($classData);
            $this->command->info("Created class: {$schoolClass->class_name} ({$schoolClass->class_code})");
        }

        // After all teachers are created, assign class teachers
        $this->assignClassTeachers();
    }

    /**
     * Assign class teachers to classes after teachers are created
     */
    private function assignClassTeachers(): void
    {
        // This will be called after TeacherSeeder runs
        // We'll update classes with class teachers in a separate method
    }
}
