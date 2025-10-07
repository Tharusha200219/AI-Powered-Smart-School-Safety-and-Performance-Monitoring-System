<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Management\ParentController;
use App\Http\Controllers\Admin\Management\SchoolClassController;
use App\Http\Controllers\Admin\Management\SecurityStaffController;
use App\Http\Controllers\Admin\Management\StudentController;
use App\Http\Controllers\Admin\Management\SubjectController;
use App\Http\Controllers\Admin\Management\TeacherController;
use App\Http\Controllers\Admin\Management\TimetableController;
use App\Http\Controllers\Admin\TimetableViewerController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PlaceholderController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\Setup\SettingsController as SetupSettingsController;
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

        // Profile Management
        Route::prefix('profile')->name('profile.')->controller(ProfileController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/edit', 'edit')->name('edit');
            Route::put('/update', 'update')->name('update');
            Route::post('/change-password', 'changePassword')->name('change-password');
            Route::delete('/delete-image', 'deleteProfileImage')->name('delete-image');
            Route::post('/upload-image', 'update')->name('upload-image');
            Route::get('/stats', 'getProfileStats')->name('stats');
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
                Route::post('/write-nfc', 'writeToNFC')->name('write-nfc');
                Route::get('/test-arduino', 'testArduino')->name('test-arduino');
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

            // Timetables Management
            Route::prefix('timetables')->name('timetables.')->controller(TimetableController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/{timetable}', 'show')->name('show');
                Route::get('/{timetable}/edit', 'edit')->name('edit');
                Route::put('/{timetable}', 'update')->name('update');
                Route::delete('/{timetable}', 'destroy')->name('destroy');
                Route::get('/ajax/get-timetable', 'getTimetable')->name('get-timetable');
                Route::post('/create-time-slot', 'createTimeSlot')->name('create-time-slot');
                // Bulk operations for easier management
                Route::get('/bulk-assign', 'bulkAssignForm')->name('bulk-assign');
                Route::post('/bulk-assign', 'bulkAssignStore')->name('bulk-assign-store');
                Route::post('/quick-assign', 'quickAssign')->name('quick-assign');
                Route::delete('/bulk-delete', 'bulkDelete')->name('bulk-delete');
                Route::delete('/delete-time-slot/{timeSlot}', 'deleteTimeSlot')->name('delete-time-slot');
            });

            // Attendance Management
            Route::prefix('attendance')->name('attendance.')->controller(\App\Http\Controllers\Admin\Management\AttendanceController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/dashboard', 'dashboard')->name('dashboard');
                Route::get('/create', 'create')->name('create');
                Route::post('/', 'store')->name('store');
                Route::get('/report', 'report')->name('report');
                Route::get('/statistics', 'statistics')->name('statistics');
                Route::post('/search-student', 'searchStudent')->name('search-student');
                Route::post('/nfc-scan', 'nfcScan')->name('nfc-scan');
                Route::get('/student/{studentId}/percentage', 'studentPercentage')->name('student-percentage');
            });
        });

        // Placeholder routes for features in development
        Route::controller(PlaceholderController::class)->group(function () {
            // Academic Operations
            Route::get('assignments', 'assignments')->name('assignments.index');
            Route::get('grades', 'grades')->name('grades.index');
            // Route::get('attendance', 'attendance')->name('attendance.index'); // Removed - now implemented
            Route::get('timetable-viewer', [TimetableViewerController::class, 'index'])->name('timetable-viewer.index');

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

            Route::prefix('settings')->name('settings.')->controller(SetupSettingsController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/update', 'update')->name('update');

                // AJAX endpoints for settings page
                Route::post('/school-info', 'updateSchoolInfo')->name('school-info');
                Route::post('/theme', 'updateTheme')->name('theme');
                Route::post('/academic', 'updateAcademic')->name('academic');
                Route::post('/language', [SettingsController::class, 'updateLanguage'])->name('language');
            });
        });

        // Dashboard Settings Routes
        Route::prefix('settings')->name('settings.')->controller(SettingsController::class)->group(function () {
            Route::post('/update-school-info', 'updateSchoolInfo')->name('update-school-info');
            Route::post('/update-theme', 'updateTheme')->name('update-theme');
            Route::post('/update-academic', 'updateAcademic')->name('update-academic');
            Route::post('/update-social-media', 'updateSocialMedia')->name('update-social-media');
            Route::get('/theme-colors', 'getThemeColors')->name('theme-colors');
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
