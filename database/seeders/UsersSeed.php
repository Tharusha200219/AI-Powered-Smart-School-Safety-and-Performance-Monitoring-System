<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserType;
use App\Enums\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UsersSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or find admin role
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web'
        ]);

        // Get all permissions and assign to admin role
        $permissions = Permission::all();
        $adminRole->syncPermissions($permissions);

        // Create admin user
        $adminUser = User::updateOrCreate([
            'email' => 'admin@gmail.com'
        ], [
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('Admin@123'),
            'status' => Status::ACTIVE,
            'usertype' => UserType::ADMIN,
        ]);

        // Assign admin role with all permissions
        $adminUser->assignRole('admin');

        echo "Admin user created/updated: admin@gmail.com / Admin@123\n";
        echo "Admin role has " . $permissions->count() . " permissions\n";
    }
}
