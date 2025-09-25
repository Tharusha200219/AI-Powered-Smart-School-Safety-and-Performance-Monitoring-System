@extends('admin.layouts.app')

@section('css')
<<<<<<< HEAD
    <style>
        .stat-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .recent-activity-item {
            transition: all 0.2s ease;
            border-radius: 8px;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            border: 1px solid #f0f2f5;
        }

        .recent-activity-item:hover {
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }

        .grade-chart {
            height: 300px;
        }

        .progress-ring {
            width: 80px;
            height: 80px;
        }

        .dashboard-title {
            background: #06C167;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
        }
    </style>
=======
    @vite('resources/css/admin/dashboard.css')
>>>>>>> 4358fa2a22b070c3f048b27b38865b1db4389606
@endsection

@section('content')
    @include('admin.layouts.sidebar')

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        @include('admin.layouts.navbar')

        <div class="container-fluid py-2">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="ms-3">
                        <h3 class="mb-0 h4 dashboard-title">School Management Dashboard</h3>
                        <p class="mb-4 text-muted">
                            <i class="material-symbols-rounded me-1">school</i>
                            Monitor your school's performance and manage educational resources.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
<<<<<<< HEAD
                    <div class="card stat-card h-100">
                        <div class="card-body p-3">
                            <div class="text-center">
                                <div class="stat-icon bg-gradient-primary mx-auto">
                                    <i class="material-symbols-rounded text-white text-lg">school</i>
                                </div>
                                <h4 class="mb-0 text-primary">{{ number_format($stats['total_students']) }}</h4>
                                <p class="text-sm mb-0 text-capitalize">Total Students</p>
=======
                    <div class="card stat-card h-100" style="--index: 0;">
                        <div class="card-body p-3">
                            <div class="text-center">
                                <div class="stat-icon mx-auto floating-element">
                                    <i class="material-symbols-rounded text-lg">school</i>
                                </div>
                                <h4 class="mb-0 stat-number">{{ number_format($stats['total_students']) }}</h4>
                                <p class="text-sm mb-0 text-capitalize text-muted">Total Students</p>
>>>>>>> 4358fa2a22b070c3f048b27b38865b1db4389606
                                <small class="text-success">
                                    <i class="material-symbols-rounded text-xs">trending_up</i>
                                    {{ $stats['active_students'] }} Active
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
<<<<<<< HEAD
                    <div class="card stat-card h-100">
                        <div class="card-body p-3">
                            <div class="text-center">
                                <div class="stat-icon bg-gradient-success mx-auto">
                                    <i class="material-symbols-rounded text-white text-lg">person</i>
                                </div>
                                <h4 class="mb-0 text-success">{{ number_format($stats['total_teachers']) }}</h4>
                                <p class="text-sm mb-0 text-capitalize">Total Teachers</p>
=======
                    <div class="card stat-card h-100" style="--index: 1;">
                        <div class="card-body p-3">
                            <div class="text-center">
                                <div class="stat-icon mx-auto floating-element">
                                    <i class="material-symbols-rounded text-lg">person</i>
                                </div>
                                <h4 class="mb-0 stat-number">{{ number_format($stats['total_teachers']) }}</h4>
                                <p class="text-sm mb-0 text-capitalize text-muted">Total Teachers</p>
>>>>>>> 4358fa2a22b070c3f048b27b38865b1db4389606
                                <small class="text-success">
                                    <i class="material-symbols-rounded text-xs">check_circle</i>
                                    {{ $stats['active_teachers'] }} Active
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
<<<<<<< HEAD
                    <div class="card stat-card h-100">
                        <div class="card-body p-3">
                            <div class="text-center">
                                <div class="stat-icon bg-gradient-warning mx-auto">
                                    <i class="material-symbols-rounded text-white text-lg">family_restroom</i>
                                </div>
                                <h4 class="mb-0 text-warning">{{ number_format($stats['total_parents']) }}</h4>
                                <p class="text-sm mb-0 text-capitalize">Total Parents</p>
=======
                    <div class="card stat-card h-100" style="--index: 2;">
                        <div class="card-body p-3">
                            <div class="text-center">
                                <div class="stat-icon mx-auto floating-element">
                                    <i class="material-symbols-rounded text-lg">family_restroom</i>
                                </div>
                                <h4 class="mb-0 stat-number">{{ number_format($stats['total_parents']) }}</h4>
                                <p class="text-sm mb-0 text-capitalize text-muted">Total Parents</p>
>>>>>>> 4358fa2a22b070c3f048b27b38865b1db4389606
                                <small class="text-muted">
                                    <i class="material-symbols-rounded text-xs">people</i>
                                    Registered
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6">
<<<<<<< HEAD
                    <div class="card stat-card h-100">
                        <div class="card-body p-3">
                            <div class="text-center">
                                <div class="stat-icon bg-gradient-info mx-auto">
                                    <i class="material-symbols-rounded text-white text-lg">meeting_room</i>
                                </div>
                                <h4 class="mb-0 text-info">{{ number_format($stats['total_classes']) }}</h4>
                                <p class="text-sm mb-0 text-capitalize">Total Classes</p>
=======
                    <div class="card stat-card h-100" style="--index: 3;">
                        <div class="card-body p-3">
                            <div class="text-center">
                                <div class="stat-icon mx-auto floating-element">
                                    <i class="material-symbols-rounded text-lg">meeting_room</i>
                                </div>
                                <h4 class="mb-0 stat-number">{{ number_format($stats['total_classes']) }}</h4>
                                <p class="text-sm mb-0 text-capitalize text-muted">Total Classes</p>
>>>>>>> 4358fa2a22b070c3f048b27b38865b1db4389606
                                <small class="text-info">
                                    <i class="material-symbols-rounded text-xs">book</i>
                                    {{ $stats['total_subjects'] }} Subjects
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Secondary Stats -->
            <div class="row mb-4">
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
<<<<<<< HEAD
                    <div class="card stat-card">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div
                                    class="icon icon-md icon-shape bg-gradient-secondary shadow text-center border-radius-lg me-3">
                                    <i class="material-symbols-rounded opacity-10 text-white">security</i>
                                </div>
                                <div>
                                    <p class="text-sm mb-0 text-capitalize">Security Staff</p>
                                    <h4 class="mb-0">{{ $stats['total_security_staff'] }}</h4>
=======
                    <div class="card stat-card" style="--index: 4;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon me-3" style="width: 50px; height: 50px;">
                                    <i class="material-symbols-rounded">security</i>
                                </div>
                                <div>
                                    <p class="text-sm mb-0 text-capitalize text-muted">Security Staff</p>
                                    <h4 class="mb-0 stat-number">{{ $stats['total_security_staff'] }}</h4>
>>>>>>> 4358fa2a22b070c3f048b27b38865b1db4389606
                                    <small class="text-success">{{ $stats['active_security_staff'] }} Active</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
<<<<<<< HEAD
                    <div class="card stat-card">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div
                                    class="icon icon-md icon-shape bg-gradient-primary shadow text-center border-radius-lg me-3">
                                    <i class="material-symbols-rounded opacity-10 text-white">person_add</i>
                                </div>
                                <div>
                                    <p class="text-sm mb-0 text-capitalize">New Enrollments</p>
                                    <h4 class="mb-0">{{ $recent_enrollments }}</h4>
=======
                    <div class="card stat-card" style="--index: 5;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon me-3" style="width: 50px; height: 50px;">
                                    <i class="material-symbols-rounded">person_add</i>
                                </div>
                                <div>
                                    <p class="text-sm mb-0 text-capitalize text-muted">New Enrollments</p>
                                    <h4 class="mb-0 stat-number">{{ $recent_enrollments }}</h4>
>>>>>>> 4358fa2a22b070c3f048b27b38865b1db4389606
                                    <small class="text-muted">Last 30 days</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-sm-12">
<<<<<<< HEAD
                    <div class="card stat-card">
                        <div class="card-body p-3">
                            <h6 class="mb-2">Quick Actions</h6>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('admin.management.students.form') }}"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="material-symbols-rounded me-1">person_add</i>Add Student
                                </a>
                                <a href="#" class="btn btn-sm btn-outline-success">
                                    <i class="material-symbols-rounded me-1">person_add</i>Add Teacher
=======
                    <div class="card stat-card" style="--index: 6;">
                        <div class="card-body p-3">
                            <h6 class="mb-3" style="color: var(--primary-green); font-weight: 600;">Quick Actions</h6>
                            <div class="d-flex gap-3 flex-wrap">
                                <a href="{{ route('admin.management.students.form') }}" class="quick-action-btn btn btn-sm"
                                    style="--index: 0; flex: 1; min-width: 120px;">
                                    <i class="material-symbols-rounded me-1">person_add</i>Add Student
                                </a>
                                <a href="#" class="quick-action-btn btn btn-sm"
                                    style="--index: 1; flex: 1; min-width: 120px;">
                                    <i class="material-symbols-rounded me-1">school</i>Add Teacher
>>>>>>> 4358fa2a22b070c3f048b27b38865b1db4389606
                                </a>
                                <a href="#" class="btn btn-sm btn-outline-warning">
                                    <i class="material-symbols-rounded me-1">class</i>Manage Classes
                                </a>
                                <a href="#" class="btn btn-sm btn-outline-info">
                                    <i class="material-symbols-rounded me-1">book</i>View Reports
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="row">
                <!-- Grade Distribution Chart -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header pb-0">
                            <h6>Grade Distribution</h6>
                            <p class="text-sm mb-0">Students by grade level</p>
                        </div>
                        <div class="card-body">
                            <div class="grade-chart">
                                @foreach ($grade_distribution as $grade)
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="text-sm">Grade {{ $grade->grade_level }}</span>
                                        <div class="d-flex align-items-center">
                                            <div class="progress me-2" style="width: 100px; height: 6px;">
                                                <div class="progress-bar bg-gradient-primary"
                                                    style="width: {{ $stats['total_students'] > 0 ? ($grade->count / $stats['total_students']) * 100 : 0 }}%">
                                                </div>
                                            </div>
                                            <span class="text-sm fw-bold">{{ $grade->count }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Classes Overview -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header pb-0">
                            <h6>Classes Overview</h6>
                            <p class="text-sm mb-0">Student count per class</p>
                        </div>
                        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                            @foreach ($classes_with_counts as $class)
                                <div class="recent-activity-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0 text-sm">{{ $class->full_name }}</h6>
                                            <small class="text-muted">Grade {{ $class->grade_level }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-gradient-primary">{{ $class->students_count }}
                                                students</span>
                                            @if ($class->capacity)
                                                <small class="text-muted d-block">Capacity: {{ $class->capacity }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header pb-0">
                            <h6>Recent Student Activities</h6>
                            <p class="text-sm mb-0">Latest enrolled students</p>
                        </div>
                        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                            @foreach ($recent_students as $student)
                                <div class="recent-activity-item">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm bg-gradient-primary rounded-circle me-3">
                                            <span class="text-white text-xs fw-bold">
                                                {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0 text-sm">{{ $student->full_name }}</h6>
                                            <small class="text-muted">
                                                Grade {{ $student->grade_level }}
                                                @if ($student->schoolClass)
                                                    - {{ $student->schoolClass->class_name }}
                                                @endif
                                            </small>
                                            <small class="text-muted d-block">
                                                <i class="material-symbols-rounded text-xs">schedule</i>
                                                {{ $student->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <span
                                                class="badge badge-sm bg-gradient-{{ $student->is_active ? 'success' : 'secondary' }}">
                                                {{ $student->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

<<<<<<< HEAD
=======
            <!-- School Settings & Theme Customization -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">
                                    <i class="material-symbols-rounded me-2">settings</i>
                                    School Settings & Theme Customization
                                </h6>
                                <small class="text-muted">Configure your school information and customize the interface
                                    colors</small>
                            </div>
                            <button class="btn btn-outline-primary btn-sm" onclick="toggleSettingsPanel()">
                                <i class="material-symbols-rounded" id="settings-toggle-icon">expand_more</i>
                            </button>
                        </div>
                        <div class="card-body" id="settings-panel" style="display: none;">
                            <div class="row">
                                <!-- School Information -->
                                <div class="col-lg-6 mb-4">
                                    <div class="card h-100">
                                        <div class="card-header card-header-smooth">
                                            <h6 class="mb-0">
                                                <i class="material-symbols-rounded me-2">school</i>
                                                School Information
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <form id="school-info-form">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">School Name</label>
                                                        <input type="text" class="form-control" id="school_name"
                                                            value="{{ $settings->school_name ?? ($settings->title ?? '') }}"
                                                            placeholder="Enter school name">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">School Type</label>
                                                        <select class="form-control" id="school_type">
                                                            <option value="">Select Type</option>
                                                            <option value="Primary"
                                                                {{ ($settings->school_type ?? '') === 'Primary' ? 'selected' : '' }}>
                                                                Primary School</option>
                                                            <option value="Secondary"
                                                                {{ ($settings->school_type ?? '') === 'Secondary' ? 'selected' : '' }}>
                                                                Secondary School</option>
                                                            <option value="Combined"
                                                                {{ ($settings->school_type ?? '') === 'Combined' ? 'selected' : '' }}>
                                                                Combined School</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">School Motto</label>
                                                    <input type="text" class="form-control" id="school_motto"
                                                        value="{{ $settings->school_motto ?? '' }}"
                                                        placeholder="Enter school motto">
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Principal Name</label>
                                                        <input type="text" class="form-control" id="principal_name"
                                                            value="{{ $settings->principal_name ?? '' }}"
                                                            placeholder="Enter principal name">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Established Year</label>
                                                        <input type="number" class="form-control" id="established_year"
                                                            value="{{ $settings->established_year ?? '' }}"
                                                            placeholder="e.g., 1985" min="1800"
                                                            max="{{ date('Y') }}">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Total Capacity</label>
                                                        <input type="number" class="form-control" id="total_capacity"
                                                            value="{{ $settings->total_capacity ?? '' }}"
                                                            placeholder="e.g., 1000" min="1">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label">Website URL</label>
                                                        <input type="url" class="form-control" id="website_url"
                                                            value="{{ $settings->website_url ?? '' }}"
                                                            placeholder="https://yourschool.edu">
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-outline-smooth"
                                                    onclick="updateSchoolInfo()">
                                                    <i class="material-symbols-rounded me-1">save</i>
                                                    Update School Info
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Theme Customization -->
                                <div class="col-lg-6 mb-4">
                                    <div class="card h-100">
                                        <div class="card-header card-header-smooth">
                                            <h6 class="mb-0">
                                                <i class="material-symbols-rounded me-2">palette</i>
                                                Theme Customization
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <form id="theme-form">
                                                @csrf
                                                <div class="mb-3">
                                                    <label class="form-label">Primary Color</label>
                                                    <div class="d-flex align-items-center">
                                                        <input type="color" class="form-control form-control-color me-2"
                                                            id="primary_color"
                                                            value="{{ $settings->primary_color ?? '#06C167' }}"
                                                            onchange="updateThemePreview()">
                                                        <input type="text" class="form-control"
                                                            value="{{ $settings->primary_color ?? '#06C167' }}"
                                                            id="primary_color_text"
                                                            onchange="updateColorFromText('primary_color')">
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Secondary Color</label>
                                                    <div class="d-flex align-items-center">
                                                        <input type="color" class="form-control form-control-color me-2"
                                                            id="secondary_color"
                                                            value="{{ $settings->secondary_color ?? '#10B981' }}"
                                                            onchange="updateThemePreview()">
                                                        <input type="text" class="form-control"
                                                            value="{{ $settings->secondary_color ?? '#10B981' }}"
                                                            id="secondary_color_text"
                                                            onchange="updateColorFromText('secondary_color')">
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Accent Color</label>
                                                    <div class="d-flex align-items-center">
                                                        <input type="color" class="form-control form-control-color me-2"
                                                            id="accent_color"
                                                            value="{{ $settings->accent_color ?? '#F0FDF4' }}"
                                                            onchange="updateThemePreview()">
                                                        <input type="text" class="form-control"
                                                            value="{{ $settings->accent_color ?? '#F0FDF4' }}"
                                                            id="accent_color_text"
                                                            onchange="updateColorFromText('accent_color')">
                                                    </div>
                                                </div>

                                                <!-- Quick Theme Presets -->
                                                <div class="mb-3">
                                                    <label class="form-label">Quick Presets</label>
                                                    <div class="d-flex gap-2 flex-wrap">
                                                        <button type="button" class="btn btn-sm btn-outline-success"
                                                            onclick="applyThemePreset('green')">Green</button>
                                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                            onclick="applyThemePreset('blue')">Blue</button>
                                                        <button type="button" class="btn btn-sm btn-outline-warning"
                                                            onclick="applyThemePreset('orange')">Orange</button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="applyThemePreset('red')">Red</button>
                                                        <button type="button" class="btn btn-sm btn-outline-info"
                                                            onclick="applyThemePreset('purple')">Purple</button>
                                                    </div>
                                                </div>

                                                <!-- Theme Settings -->
                                                <div class="mb-3">
                                                    <label class="form-label">Theme Mode</label>
                                                    <select class="form-control" id="theme_mode">
                                                        <option value="light"
                                                            {{ ($settings->theme_mode ?? 'light') === 'light' ? 'selected' : '' }}>
                                                            Light</option>
                                                        <option value="dark"
                                                            {{ ($settings->theme_mode ?? 'light') === 'dark' ? 'selected' : '' }}>
                                                            Dark</option>
                                                        <option value="auto"
                                                            {{ ($settings->theme_mode ?? 'light') === 'auto' ? 'selected' : '' }}>
                                                            Auto</option>
                                                    </select>
                                                </div>

                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox"
                                                        id="enable_animations"
                                                        {{ $settings->enable_animations ?? true ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="enable_animations">
                                                        Enable Animations
                                                    </label>
                                                </div>

                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-primary"
                                                        onclick="updateThemeColors()">
                                                        <i class="material-symbols-rounded me-1">save</i>
                                                        Save Theme
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary"
                                                        onclick="resetTheme()">
                                                        <i class="material-symbols-rounded me-1">refresh</i>
                                                        Reset
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Academic Settings -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header card-header-smooth">
                                            <h6 class="mb-0">
                                                <i class="material-symbols-rounded me-2">schedule</i>
                                                Academic Settings
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <form id="academic-form">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-3 mb-3">
                                                        <label class="form-label">School Start Time</label>
                                                        <input type="time" class="form-control" id="school_start_time"
                                                            value="{{ $settings->school_start_time ? $settings->school_start_time->format('H:i') : '08:00' }}">
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label class="form-label">School End Time</label>
                                                        <input type="time" class="form-control" id="school_end_time"
                                                            value="{{ $settings->school_end_time ? $settings->school_end_time->format('H:i') : '15:00' }}">
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label class="form-label">Academic Year Start</label>
                                                        <select class="form-control" id="academic_year_start">
                                                            @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                                                <option value="{{ $month }}"
                                                                    {{ ($settings->academic_year_start ?? 'January') === $month ? 'selected' : '' }}>
                                                                    {{ $month }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label class="form-label">Academic Year End</label>
                                                        <select class="form-control" id="academic_year_end">
                                                            @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                                                <option value="{{ $month }}"
                                                                    {{ ($settings->academic_year_end ?? 'December') === $month ? 'selected' : '' }}>
                                                                    {{ $month }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-outline-smooth"
                                                    onclick="updateAcademicSettings()">
                                                    <i class="material-symbols-rounded me-1">save</i>
                                                    Update Academic Settings
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

>>>>>>> 4358fa2a22b070c3f048b27b38865b1db4389606
            @include('admin.layouts.inner-footer')
        </div>
    </main>
@endsection

@section('js')
<<<<<<< HEAD
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add some interactive animations
            const statCards = document.querySelectorAll('.stat-card');

            statCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px) scale(1.02)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });

            // Auto-refresh data every 5 minutes
            setInterval(function() {
                // You can add AJAX calls here to refresh statistics
                console.log('Dashboard data refresh...');
            }, 300000);
        });
    </script>
=======
    @vite('resources/js/admin/dashboard.js')
>>>>>>> 4358fa2a22b070c3f048b27b38865b1db4389606
@endsection
