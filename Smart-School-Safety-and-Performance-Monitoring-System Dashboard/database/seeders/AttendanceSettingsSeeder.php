<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttendanceSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setting = \App\Models\Setting::first();
        if ($setting) {
            $setting->update([
                'attendance_rfid_enabled' => true,
                'attendance_face_enabled' => false,
                'attendance_two_factor' => false,
                'face_recognition_api_url' => 'http://localhost:5000',
                'face_recognition_api_key' => 'your-api-key',
            ]);
        }
    }
}
