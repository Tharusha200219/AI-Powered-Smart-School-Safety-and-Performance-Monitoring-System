<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles for school management
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $teacherRole = Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        $studentRole = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        $parentRole = Role::firstOrCreate(['name' => 'parent', 'guard_name' => 'web']);
        $securityRole = Role::firstOrCreate(['name' => 'security', 'guard_name' => 'web']);

        // Create permissions from routes_and_permissions config
        foreach (config('routes_and_permissions') as $sidebarElement) {
            if (isset($sidebarElement['items'])) {
                foreach ($sidebarElement['items'] as $element) {

                    $mainPermission = formatPermissionString($element['route']);

                    // Skip 'enroll' permissions as mentioned in sample
                    if (isset(['enroll'][strtolower($mainPermission)])) {
                        continue;
                    }

                    $permission = Permission::firstOrCreate([
                        'name' => $mainPermission,
                        'guard_name' => 'web',
                    ]);

                    // Give admin role all permissions
                    if (! $adminRole->hasPermissionTo($permission)) {
                        $adminRole->givePermissionTo($permission);
                    }

                    // Handle other_selected_routes
                    if (isset($element['other_selected_routes'])) {
                        foreach ($element['other_selected_routes'] as $route) {
                            $otherPermission = formatPermissionString($route);
                            $otherPermissionModel = Permission::firstOrCreate([
                                'name' => $otherPermission,
                                'guard_name' => 'web',
                            ]);

                            if (! $adminRole->hasPermissionTo($otherPermissionModel)) {
                                $adminRole->givePermissionTo($otherPermissionModel);
                            }

                            // Assign to specific roles based on permission context
                            $this->assignPermissionToRoles($otherPermissionModel, $teacherRole, $studentRole, $parentRole, $securityRole);
                        }
                    }

                    // Handle additional_permissions
                    if (isset($element['additional_permissions'])) {
                        foreach ($element['additional_permissions'] as $route) {
                            $additionalPermission = formatPermissionString($route);
                            $additionalPermissionModel = Permission::firstOrCreate([
                                'name' => $additionalPermission,
                                'guard_name' => 'web',
                            ]);

                            if (! $adminRole->hasPermissionTo($additionalPermissionModel)) {
                                $adminRole->givePermissionTo($additionalPermissionModel);
                            }

                            // Assign to specific roles based on permission context
                            $this->assignPermissionToRoles($additionalPermissionModel, $teacherRole, $studentRole, $parentRole, $securityRole);
                        }
                    }

                    // Assign main permission to specific roles
                    $this->assignPermissionToRoles($permission, $teacherRole, $studentRole, $parentRole, $securityRole);
                }
            }
        }

        echo "Roles and permissions created successfully!\n";
        echo 'Admin role has ' . $adminRole->permissions->count() . " permissions\n";
        echo 'Teacher role has ' . $teacherRole->permissions->count() . " permissions\n";
        echo 'Student role has ' . $studentRole->permissions->count() . " permissions\n";
        echo 'Parent role has ' . $parentRole->permissions->count() . " permissions\n";
        echo 'Security role has ' . $securityRole->permissions->count() . " permissions\n";
    }

    /**
     * Assign permission to appropriate roles based on permission context
     */
    private function assignPermissionToRoles($permission, $teacherRole, $studentRole, $parentRole, $securityRole)
    {
        $permissionName = $permission->name;

        // Teacher permissions
        if ($this->isTeacherPermission($permissionName)) {
            if (! $teacherRole->hasPermissionTo($permission)) {
                $teacherRole->givePermissionTo($permission);
            }
        }

        // Student permissions
        if ($this->isStudentPermission($permissionName)) {
            if (! $studentRole->hasPermissionTo($permission)) {
                $studentRole->givePermissionTo($permission);
            }
        }

        // Parent permissions
        if ($this->isParentPermission($permissionName)) {
            if (! $parentRole->hasPermissionTo($permission)) {
                $parentRole->givePermissionTo($permission);
            }
        }

        // Security permissions
        if ($this->isSecurityPermission($permissionName)) {
            if (! $securityRole->hasPermissionTo($permission)) {
                $securityRole->givePermissionTo($permission);
            }
        }
    }

    /**
     * Check if permission should be granted to teacher role.
     * NOTE: $permission is the DB-stored name with spaces, e.g. "admin dashboard index"
     */
    private function isTeacherPermission($permission)
    {
        // Use space-separated format matching what formatPermissionString() produces
        return str_contains($permission, 'students') ||
            str_contains($permission, 'classes') ||
            str_contains($permission, 'grades') ||
            str_contains($permission, 'attendance') ||
            str_contains($permission, 'assignments') ||
            str_contains($permission, 'subjects') ||
            str_contains($permission, 'timetable') ||
            in_array($permission, [
                'admin dashboard index',
                'admin management marks index',
                'admin management homework index',
                'admin management homework create',
                'admin management homework show',
                'admin management homework edit',
                'admin management homework destroy',
                'admin reports students index',
                'admin reports academic index',
                'admin reports attendance index',
                'admin communication announcements index',
                'admin communication messages index',
                'admin profile index',
                'admin profile edit',
            ]);
    }

    /**
     * Check if permission should be granted to parent role.
     * NOTE: $permission is the DB-stored name with spaces, e.g. "admin dashboard index"
     */
    private function isParentPermission($permission)
    {
        $parentPermissions = [
            'admin dashboard index',
            'admin management students show',
            'admin management marks index',
            'admin management attendance index',
            'admin management attendance dashboard',
            'admin reports students index',
            'admin communication announcements index',
            'admin communication messages index',
            'admin profile index',
            'admin profile edit',
        ];

        return in_array($permission, $parentPermissions);
    }

    /**
     * Check if permission should be granted to student role.
     * NOTE: $permission is the DB-stored name with spaces, e.g. "admin student homework index"
     */
    private function isStudentPermission($permission)
    {
        // All admin student * routes are student-accessible (space-separated in DB)
        if (str_starts_with($permission, 'admin student ')) {
            return true;
        }

        // Additional shared pages students can access (space-separated format)
        $studentPermissions = [
            'admin dashboard index',
            'admin timetable-viewer index',
            'admin management marks index',
            'admin management attendance dashboard',
            'admin management attendance index',
            'admin communication announcements index',
            'admin communication messages index',
            'admin profile index',
            'admin profile edit',
        ];

        return in_array($permission, $studentPermissions);
    }

    /**
     * Check if permission should be granted to security role.
     * NOTE: $permission is the DB-stored name with spaces, e.g. "admin dashboard index"
     */
    private function isSecurityPermission($permission)
    {
        // Use str_contains checks (keywords are the same with or without dots)
        return str_contains($permission, 'security') ||
            str_contains($permission, 'visitors') ||
            str_contains($permission, 'incidents') ||
            in_array($permission, [
                'admin dashboard index',
                'admin management attendance index',
                'admin management attendance dashboard',
                'admin profile index',
                'admin profile edit',
            ]);
    }
}
