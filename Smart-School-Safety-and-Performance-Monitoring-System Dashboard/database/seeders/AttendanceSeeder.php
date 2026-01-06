<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Generate random attendance records for all students
     */
    public function run(): void
    {
        $this->command->info('Generating attendance records for students...');

        $students = Student::all();
        $currentYear = date('Y');
        $academicYear = $currentYear . '-' . ($currentYear + 1);

        // Generate attendance for the last 60 school days
        $startDate = Carbon::now()->subDays(90);
        $endDate = Carbon::now();

        foreach ($students as $student) {
            $this->generateAttendanceForStudent($student, $startDate, $endDate);
        }

        $this->command->info('Attendance generation completed!');
    }

    /**
     * Generate attendance records for a single student
     */
    private function generateAttendanceForStudent(Student $student, Carbon $startDate, Carbon $endDate): void
    {
        $currentDate = $startDate->copy();
        $attendanceCount = 0;

        while ($currentDate->lte($endDate)) {
            // Skip weekends
            if ($currentDate->isWeekend()) {
                $currentDate->addDay();
                continue;
            }

            // Random attendance pattern (85% present, 5% late, 5% absent, 5% excused)
            $rand = rand(1, 100);


            if ($rand <= 85) {
                // 30
                $status = 'present';
                $checkInTime = $this->getRandomCheckInTime('07:30:00', '08:00:00');
                $checkOutTime = $this->getRandomCheckOutTime('14:00:00', '15:00:00');
            } elseif ($rand <= 90) {
                // 40
                $status = 'late';
                $checkInTime = $this->getRandomCheckInTime('08:01:00', '09:00:00');
                $checkOutTime = $this->getRandomCheckOutTime('14:00:00', '15:00:00');
            } elseif ($rand <= 95) {
                // 75
                $status = 'absent';
                $checkInTime = null;
                $checkOutTime = null;
            } else {
                $status = 'excused';
                $checkInTime = null;
                $checkOutTime = null;
            }

            // Create attendance record
            Attendance::create([
                'student_id' => $student->student_id,
                'attendance_date' => $currentDate->format('Y-m-d'),
                'check_in_time' => $checkInTime,
                'check_out_time' => $checkOutTime,
                'status' => $status,
                'nfc_tag_id' => $status === 'present' || $status === 'late' ? 'NFC' . str_pad($student->student_id, 6, '0', STR_PAD_LEFT) : null,
                'check_in_location' => $status === 'present' || $status === 'late' ? 'Main Gate' : null,
                'check_out_location' => $status === 'present' || $status === 'late' ? 'Main Gate' : null,
                'device_id' => $status === 'present' || $status === 'late' ? 'ARDUINO_001' : null,
                'temperature' => $status === 'present' || $status === 'late' ? round(36.5 + (rand(-5, 5) / 10), 1) : null,
                'remarks' => $status === 'excused' ? 'Medical appointment' : ($status === 'absent' ? 'Unexcused absence' : null),
            ]);

            $attendanceCount++;
            $currentDate->addDay();
        }

        $this->command->info("Generated {$attendanceCount} attendance records for student {$student->student_code}");
    }

    /**
     * Generate random check-in time
     */
    private function getRandomCheckInTime(string $startTime, string $endTime): string
    {
        $start = strtotime($startTime);
        $end = strtotime($endTime);
        $randomTime = rand($start, $end);
        return date('H:i:s', $randomTime);
    }

    /**
     * Generate random check-out time
     */
    private function getRandomCheckOutTime(string $startTime, string $endTime): string
    {
        $start = strtotime($startTime);
        $end = strtotime($endTime);
        $randomTime = rand($start, $end);
        return date('H:i:s', $randomTime);
    }
}
