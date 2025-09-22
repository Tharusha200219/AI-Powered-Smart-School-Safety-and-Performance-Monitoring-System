<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\Management\StudentController;
use App\Http\Controllers\Admin\Management\TeacherController;
use App\Http\Controllers\Admin\Management\ParentController;
use App\Http\Controllers\Admin\Management\SecurityStaffController;
use App\Http\Controllers\Admin\Management\SchoolClassController;
use App\Http\Controllers\Admin\Management\SubjectController;
use App\Http\Controllers\Admin\PlaceholderController;
use App\Http\Controllers\Admin\Setup\SettingsController;
use App\Http\Controllers\Admin\Setup\RoleController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;


Auth::routes(['register' => true, 'verify' => false]);

Route::get('/', function () {
    return Redirect::to('/admin/dashboard/');
});

Route::middleware(['auth'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::prefix('dashboard')->name('dashboard.')->controller(DashboardController::class)->group(function () {
            Route::get('/', 'index')->name('index');
        });

        Route::prefix('management')->name('management.')->group(function () {
            // Students Management
            Route::prefix('students')->name('students.')->controller(StudentController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/form/{id?}', 'form')->name('form');
                Route::get('/show/{id}', 'show')->name('show');
                Route::get('/delete/{id}', 'delete')->name('delete');
                Route::post('/enroll', 'enroll')->name('enroll');
                Route::get('/generate-code', 'generateCode')->name('generate-code');
            });

            // Teachers Management
            Route::prefix('teachers')->name('teachers.')->controller(TeacherController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/form/{id?}', 'form')->name('form');
                Route::get('/show/{id}', 'show')->name('show');
                Route::get('/delete/{id}', 'delete')->name('delete');
                Route::post('/enroll', 'enroll')->name('enroll');
                Route::get('/generate-code', 'generateCode')->name('generate-code');
            });

            // Parents Management (View-only - Parents are created through Student management)
            Route::prefix('parents')->name('parents.')->controller(ParentController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/show/{id}', 'show')->name('show');
            });

            // Classes Management
            Route::prefix('classes')->name('classes.')->controller(SchoolClassController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/form/{id?}', 'form')->name('form');
                Route::get('/show/{id}', 'show')->name('show');
                Route::get('/delete/{id}', 'delete')->name('delete');
                Route::post('/enroll', 'enroll')->name('enroll');
            });

            // Subjects Management
            Route::prefix('subjects')->name('subjects.')->controller(SubjectController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/form/{id?}', 'form')->name('form');
                Route::get('/show/{id}', 'show')->name('show');
                Route::get('/delete/{id}', 'delete')->name('delete');
                Route::post('/enroll', 'enroll')->name('enroll');
            });

            // Security Staff Management
            Route::prefix('security')->name('security.')->controller(SecurityStaffController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/form/{id?}', 'form')->name('form');
                Route::get('/show/{id}', 'show')->name('show');
                Route::get('/delete/{id}', 'delete')->name('delete');
                Route::post('/enroll', 'enroll')->name('enroll');
            });
        });

        // Placeholder routes for features in development
        Route::controller(PlaceholderController::class)->group(function () {
            // Academic Operations
            Route::get('assignments', 'assignments')->name('assignments.index');
            Route::get('grades', 'grades')->name('grades.index');
            Route::get('attendance', 'attendance')->name('attendance.index');
            Route::get('timetable', 'timetable')->name('timetable.index');

            // Security Additional
            Route::get('security/visitors', 'visitors')->name('security.visitors.index');
            Route::get('security/incidents', 'incidents')->name('security.incidents.index');

            // Reports
            Route::get('reports/students', 'studentReports')->name('reports.students.index');
            Route::get('reports/academic', 'academicReports')->name('reports.academic.index');
            Route::get('reports/attendance', 'attendanceReports')->name('reports.attendance.index');

            // Communication
            Route::get('communication/announcements', 'announcements')->name('communication.announcements.index');
            Route::get('communication/messages', 'messages')->name('communication.messages.index');

            // System Setup Additional
            Route::get('setup/school', 'schoolInfo')->name('setup.school.index');
            Route::get('setup/grade-levels', 'gradeLevels')->name('setup.grade-levels.index');
            Route::get('setup/academic-year', 'academicYear')->name('setup.academic-year.index');
            Route::get('setup/users', 'users')->name('setup.users.index');
        });

        Route::prefix('setup')->name('setup.')->group(function () {
            // Role Management
            Route::prefix('role')->name('role.')->controller(\App\Http\Controllers\Admin\Setup\RoleController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/form/{id?}', 'form')->name('form');
                Route::get('/show/{id}', 'show')->name('show');
                Route::get('/delete/{id}', 'delete')->name('delete');
                Route::post('/enroll', 'enroll')->name('enroll');
            });

            // User Management
            Route::prefix('users')->name('users.')->controller(\App\Http\Controllers\Admin\Setup\UserController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/form/{id?}', 'form')->name('form');
                Route::get('/show/{id}', 'show')->name('show');
                Route::get('/delete/{id}', 'delete')->name('delete');
                Route::post('/enroll', 'enroll')->name('enroll');
            });

            Route::prefix('settings')->name('settings.')->controller(SettingsController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/update', 'update')->name('update');
            });
        });

        // Notification API routes
        Route::prefix('notifications')->name('notifications.')->controller(NotificationController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/mark-as-read', 'markAsRead')->name('mark-as-read');
            Route::post('/mark-all-as-read', 'markAllAsRead')->name('mark-all-as-read');
            Route::get('/unread-count', 'getUnreadCount')->name('unread-count');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });
    });
});
