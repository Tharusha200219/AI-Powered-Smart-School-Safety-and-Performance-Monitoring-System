<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating sample attendance records for students...');

        // Get the first student for testing
        $student = Student::first();

        if (!$student) {
            $this->command->warn('No students found. Please run StudentSeeder first.');
            return;
        }

        // Get a user to attribute as the one who recorded attendance
        $recordedBy = User::role('Admin')->first() ?? User::first();

        $this->command->info("Creating attendance records for student: {$student->full_name} (ID: {$student->student_id})");

        // Create attendance records for the current month (last 30 days)
        $today = Carbon::now();
        $attendanceRecords = 0;

        for ($i = 29; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);

            // Skip weekends (Saturday = 6, Sunday = 0)
            if ($date->dayOfWeek === 0 || $date->dayOfWeek === 6) {
                continue;
            }

            // Generate realistic attendance patterns
            $status = $this->getRandomAttendanceStatus($i);

            $checkInTime = null;
            $checkOutTime = null;

            if ($status === 'present' || $status === 'late') {
                // School starts at 8:00 AM
                $baseCheckIn = Carbon::createFromTime(8, 0, 0);

                if ($status === 'late') {
                    // Late by 15-60 minutes
                    $baseCheckIn->addMinutes(rand(15, 60));
                } else {
                    // On time or early by 0-15 minutes
                    $baseCheckIn->subMinutes(rand(0, 15));
                }

                $checkInTime = $baseCheckIn->format('H:i:s');

                // School ends at 2:30 PM
                $checkOutTime = '14:30:00';
            }

            Attendance::create([
                'student_id' => $student->student_id,
                'attendance_date' => $date->format('Y-m-d'),
                'check_in_time' => $checkInTime,
                'check_out_time' => $checkOutTime,
                'status' => $status,
                'nfc_tag_id' => 'TEST_TAG_' . $student->student_id,
                'device_id' => 'ARDUINO_001',
                'temperature' => $status !== 'absent' ? rand(365, 375) / 10 : null, // 36.5-37.5°C
                'remarks' => $this->getRemarksForStatus($status),
                'recorded_by' => $recordedBy ? $recordedBy->id : null,
            ]);

            $attendanceRecords++;
        }

        $this->command->info("Created {$attendanceRecords} attendance records for testing.");
    }

    /**
     * Get a random attendance status with realistic distribution
     */
    private function getRandomAttendanceStatus(int $dayIndex): string
    {
        // Create a pattern where the student has good attendance overall
        // but with some variation for testing different scenarios

        $rand = rand(1, 100);

        if ($rand <= 85) {
            return 'present'; // 85% present
        } elseif ($rand <= 95) {
            return 'late'; // 10% late
        } else {
            return 'absent'; // 5% absent
        }
    }

    /**
     * Get appropriate remarks based on attendance status
     */
    private function getRemarksForStatus(string $status): ?string
    {
        switch ($status) {
            case 'present':
                return 'On time';
            case 'late':
                return 'Arrived late - traffic delay';
            case 'absent':
                return 'Medical leave';
            default:
                return null;
        }
    }
}
