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
<<<<<<< HEAD
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(UsersSeed::class);
        $this->call(SettingSeed::class);
=======
        // Core system setup
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(UsersSeed::class);
        $this->call(SettingSeed::class);

        // Basic data seeding (order matters due to relationships)
        $this->call(SubjectSeeder::class);
        $this->call(SchoolClassSeeder::class);
        $this->call(ParentSeeder::class);
        $this->call(TeacherSeeder::class);
        $this->call(SecurityStaffSeeder::class);
        $this->call(StudentSeeder::class); // Last because it depends on classes and parents

        // Final assignments (after all entities are created)
        $this->call(ClassTeacherAssignmentSeeder::class);
>>>>>>> 4358fa2a22b070c3f048b27b38865b1db4389606
    }
}
