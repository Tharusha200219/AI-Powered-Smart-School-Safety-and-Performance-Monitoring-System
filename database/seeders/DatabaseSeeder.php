<?php

namespace Database\Seeders;

use App\Models\User;
use Hamcrest\Core\Set;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(UsersSeed::class);
        $this->call(SettingSeed::class);
    }
}
