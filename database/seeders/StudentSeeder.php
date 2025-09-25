<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\ParentModel;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = [
            [
                'first_name' => 'Emma',
                'middle_name' => 'Grace',
                'last_name' => 'Anderson',
                'date_of_birth' => '2018-03-15',
                'gender' => 'F',
                'nationality' => 'American',
                'religion' => 'Christian',
                'home_language' => 'English',
                'enrollment_date' => '2024-08-20',
                'grade_level' => '1',
                'section' => 'A',
                'address_line1' => '123 Oak Street',
                'address_line2' => 'Apt 4B',
                'city' => 'Springfield',
                'state' => 'Illinois',
                'postal_code' => '62701',
                'country' => 'USA',
                'home_phone' => '+1-217-555-0101',
                'mobile_phone' => '+1-217-555-0102',
                'email' => 'emma.anderson@student.school.edu',
                'parent_names' => ['Michael Anderson'],
                'subjects' => ['Mathematics', 'English Language Arts', 'Science', 'Physical Education']
            ],
            [
                'first_name' => 'Liam',
                'middle_name' => 'Alexander',
                'last_name' => 'Johnson',
                'date_of_birth' => '2018-07-22',
                'gender' => 'M',
                'nationality' => 'American',
                'religion' => 'Christian',
                'home_language' => 'English',
                'enrollment_date' => '2024-08-20',
                'grade_level' => '1',
                'section' => 'B',
                'address_line1' => '456 Maple Avenue',
                'city' => 'Springfield',
                'state' => 'Illinois',
                'postal_code' => '62702',
                'country' => 'USA',
                'home_phone' => '+1-217-555-0201',
                'mobile_phone' => '+1-217-555-0202',
                'email' => 'liam.johnson@student.school.edu',
                'parent_names' => ['Sarah Johnson'],
                'subjects' => ['Mathematics', 'English Language Arts', 'Science', 'Physical Education']
            ],
            [
                'first_name' => 'Olivia',
                'middle_name' => 'Rose',
                'last_name' => 'Williams',
                'date_of_birth' => '2017-11-08',
                'gender' => 'F',
                'nationality' => 'American',
                'religion' => 'Catholic',
                'home_language' => 'English',
                'enrollment_date' => '2024-08-20',
                'grade_level' => '2',
                'section' => 'A',
                'address_line1' => '789 Pine Street',
                'city' => 'Springfield',
                'state' => 'Illinois',
                'postal_code' => '62703',
                'country' => 'USA',
                'home_phone' => '+1-217-555-0301',
                'mobile_phone' => '+1-217-555-0302',
                'email' => 'olivia.williams@student.school.edu',
                'parent_names' => ['David Williams'],
                'subjects' => ['Mathematics', 'English Language Arts', 'Science', 'Social Studies', 'Physical Education']
            ],
            [
                'first_name' => 'Noah',
                'middle_name' => 'James',
                'last_name' => 'Brown',
                'date_of_birth' => '2017-05-14',
                'gender' => 'M',
                'nationality' => 'American',
                'religion' => 'Protestant',
                'home_language' => 'English',
                'enrollment_date' => '2024-08-20',
                'grade_level' => '2',
                'section' => 'B',
                'address_line1' => '321 Cedar Lane',
                'address_line2' => 'Unit 12',
                'city' => 'Springfield',
                'state' => 'Illinois',
                'postal_code' => '62704',
                'country' => 'USA',
                'home_phone' => '+1-217-555-0401',
                'mobile_phone' => '+1-217-555-0402',
                'email' => 'noah.brown@student.school.edu',
                'parent_names' => ['Jennifer Brown'],
                'subjects' => ['Mathematics', 'English Language Arts', 'Science', 'Social Studies', 'Physical Education']
            ],
            [
                'first_name' => 'Sophia',
                'middle_name' => 'Elizabeth',
                'last_name' => 'Davis',
                'date_of_birth' => '2016-09-30',
                'gender' => 'F',
                'nationality' => 'American',
                'religion' => 'Jewish',
                'home_language' => 'English',
                'enrollment_date' => '2024-08-20',
                'grade_level' => '3',
                'section' => 'A',
                'address_line1' => '654 Elm Street',
                'city' => 'Springfield',
                'state' => 'Illinois',
                'postal_code' => '62705',
                'country' => 'USA',
                'home_phone' => '+1-217-555-0501',
                'mobile_phone' => '+1-217-555-0502',
                'email' => 'sophia.davis@student.school.edu',
                'parent_names' => ['Robert Davis'],
                'subjects' => ['Mathematics', 'English Language Arts', 'Science', 'Social Studies', 'Visual Arts', 'Physical Education']
            ],
            [
                'first_name' => 'Jackson',
                'middle_name' => 'William',
                'last_name' => 'Miller',
                'date_of_birth' => '2016-12-03',
                'gender' => 'M',
                'nationality' => 'American',
                'religion' => 'Christian',
                'home_language' => 'English',
                'enrollment_date' => '2024-08-20',
                'grade_level' => '3',
                'section' => 'B',
                'address_line1' => '987 Birch Road',
                'city' => 'Springfield',
                'state' => 'Illinois',
                'postal_code' => '62706',
                'country' => 'USA',
                'home_phone' => '+1-217-555-0601',
                'mobile_phone' => '+1-217-555-0602',
                'email' => 'jackson.miller@student.school.edu',
                'parent_names' => ['Lisa Miller'],
                'subjects' => ['Mathematics', 'English Language Arts', 'Science', 'Social Studies', 'Music', 'Physical Education']
            ],
            [
                'first_name' => 'Ava',
                'middle_name' => 'Nicole',
                'last_name' => 'Wilson',
                'date_of_birth' => '2015-04-18',
                'gender' => 'F',
                'nationality' => 'American',
                'religion' => 'Baptist',
                'home_language' => 'English',
                'enrollment_date' => '2024-08-20',
                'grade_level' => '4',
                'section' => 'A',
                'address_line1' => '147 Spruce Street',
                'address_line2' => 'House 5',
                'city' => 'Springfield',
                'state' => 'Illinois',
                'postal_code' => '62707',
                'country' => 'USA',
                'home_phone' => '+1-217-555-0701',
                'mobile_phone' => '+1-217-555-0702',
                'email' => 'ava.wilson@student.school.edu',
                'parent_names' => ['Christopher Wilson'],
                'subjects' => ['Mathematics', 'English Language Arts', 'Science', 'Social Studies', 'Technology Education', 'Physical Education']
            ],
            [
                'first_name' => 'Lucas',
                'middle_name' => 'Daniel',
                'last_name' => 'Moore',
                'date_of_birth' => '2015-08-25',
                'gender' => 'M',
                'nationality' => 'American',
                'religion' => 'Methodist',
                'home_language' => 'English',
                'enrollment_date' => '2024-08-20',
                'grade_level' => '4',
                'section' => 'B',
                'address_line1' => '258 Walnut Avenue',
                'city' => 'Springfield',
                'state' => 'Illinois',
                'postal_code' => '62708',
                'country' => 'USA',
                'home_phone' => '+1-217-555-0801',
                'mobile_phone' => '+1-217-555-0802',
                'email' => 'lucas.moore@student.school.edu',
                'parent_names' => ['Amanda Moore'],
                'subjects' => ['Mathematics', 'English Language Arts', 'Science', 'Social Studies', 'Technology Education', 'Physical Education']
            ],
            [
                'first_name' => 'Isabella',
                'middle_name' => 'Marie',
                'last_name' => 'Taylor',
                'date_of_birth' => '2014-01-12',
                'gender' => 'F',
                'nationality' => 'American',
                'religion' => 'Catholic',
                'home_language' => 'English',
                'enrollment_date' => '2024-08-20',
                'grade_level' => '5',
                'section' => 'A',
                'address_line1' => '369 Chestnut Drive',
                'city' => 'Springfield',
                'state' => 'Illinois',
                'postal_code' => '62709',
                'country' => 'USA',
                'home_phone' => '+1-217-555-0901',
                'mobile_phone' => '+1-217-555-0902',
                'email' => 'isabella.taylor@student.school.edu',
                'parent_names' => ['Kevin Taylor'],
                'subjects' => ['Mathematics', 'English Language Arts', 'Science', 'Social Studies', 'Spanish Language', 'Technology Education', 'Physical Education']
            ],
            [
                'first_name' => 'Ethan',
                'middle_name' => 'Christopher',
                'last_name' => 'Garcia',
                'date_of_birth' => '2014-06-07',
                'gender' => 'M',
                'nationality' => 'American',
                'religion' => 'Christian',
                'home_language' => 'English',
                'enrollment_date' => '2024-08-20',
                'grade_level' => '5',
                'section' => 'B',
                'address_line1' => '741 Poplar Street',
                'address_line2' => 'Building A',
                'city' => 'Springfield',
                'state' => 'Illinois',
                'postal_code' => '62710',
                'country' => 'USA',
                'home_phone' => '+1-217-555-1001',
                'mobile_phone' => '+1-217-555-1002',
                'email' => 'ethan.garcia@student.school.edu',
                'parent_names' => ['Michelle Garcia'],
                'subjects' => ['Mathematics', 'English Language Arts', 'Science', 'Social Studies', 'Spanish Language', 'Technology Education', 'Physical Education']
            ],
        ];

        foreach ($students as $studentData) {
            // Create user account for student
            $user = User::create([
                'name' => $studentData['first_name'] . ' ' . $studentData['last_name'],
                'email' => $studentData['email'],
                'password' => Hash::make('student123'), // Default password
                'email_verified_at' => now(),
            ]);

            // Assign student role
            $user->assignRole('Student');

            // Find the appropriate class based on grade and section
            $schoolClass = SchoolClass::where('grade_level', $studentData['grade_level'])
                ->where('section', $studentData['section'])
                ->first();

            // Create student record
            $student = Student::create([
                'user_id' => $user->id,
                'student_code' => Student::generateStudentCode(),
                'first_name' => $studentData['first_name'],
                'middle_name' => $studentData['middle_name'],
                'last_name' => $studentData['last_name'],
                'date_of_birth' => $studentData['date_of_birth'],
                'gender' => $studentData['gender'],
                'nationality' => $studentData['nationality'],
                'religion' => $studentData['religion'],
                'home_language' => $studentData['home_language'],
                'enrollment_date' => $studentData['enrollment_date'],
                'grade_level' => $studentData['grade_level'],
                'class_id' => $schoolClass ? $schoolClass->id : null,
                'section' => $studentData['section'],
                'is_active' => true,
                'address_line1' => $studentData['address_line1'],
                'address_line2' => $studentData['address_line2'] ?? null,
                'city' => $studentData['city'],
                'state' => $studentData['state'],
                'postal_code' => $studentData['postal_code'],
                'country' => $studentData['country'],
                'home_phone' => $studentData['home_phone'],
                'mobile_phone' => $studentData['mobile_phone'],
                'email' => $studentData['email'],
            ]);

            // Attach parents to student
            foreach ($studentData['parent_names'] as $parentName) {
                $nameParts = explode(' ', $parentName);
                $firstName = $nameParts[0];
                $lastName = end($nameParts);

                $parent = ParentModel::where('first_name', $firstName)
                    ->where('last_name', $lastName)
                    ->first();

                if ($parent) {
                    $student->parents()->attach($parent->parent_id, [
                        'is_primary_contact' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            // Attach subjects to student
            foreach ($studentData['subjects'] as $subjectName) {
                $subject = Subject::where('subject_name', $subjectName)->first();
                if ($subject) {
                    $student->subjects()->attach($subject->id, [
                        'enrollment_date' => $studentData['enrollment_date'],
                        'grade' => (int)$studentData['grade_level'], // Add the grade field
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            $this->command->info("Created student: {$student->full_name} ({$student->student_code}) - Grade {$studentData['grade_level']}{$studentData['section']} - Subjects: " . implode(', ', $studentData['subjects']));
        }
    }
}
