<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            'title' => env('APP_NAME')  ?? 'safe_learn_hub',
            'company_email' => 'info@safe_learn_hub.com'
        ]);
    }
}
