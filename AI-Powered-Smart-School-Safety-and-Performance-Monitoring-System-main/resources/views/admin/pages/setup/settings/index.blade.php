@extends('admin.layouts.app')

@section('css')
    @vite(['resources/css/admin/settings.css', 'resources/css/components/utilities.css'])
@endsection

@section('content')
    @include('admin.layouts.sidebar')

    <main class="main-content position-relative max-height-vh-100">

        @include('admin.layouts.navbar')

        <div class="container-fluid pt-2">
            <div class="row">
                <div class="ms-3">
                    @php
                        $breadcrumbs = getBreadcrumbs();
                        $breadcrumb = $breadcrumbs[count($breadcrumbs) - 2];
                    @endphp
                    <h3 class="mb-0 h4 font-weight-bolder">{{ ucfirst($breadcrumb) }}</h3>
                    <p class="mb-4 d-flex align-items-center">
                        <i class="material-symbols-rounded opacity-5 me-2">settings</i>
                        Configure your school settings
                    </p>
                </div>
            </div>

            <div class="row">
                <!-- School Information Settings -->
                <div class="col-12">
                    <div class="card my-4 glassmorphism-card">
                        <div class="card-header pb-0">
                            <div class="d-flex align-items-center">
                                <i class="material-symbols-rounded me-2">school</i>
                                <h6 class="mb-0">{{ __('settings.school_information') }}</h6>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <form id="school-info-form" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="col-md-6">
                                            <div
                                                class="input-group input-group-outline mb-3 
                                                            {{ $setting->school_name || $setting->title ? 'is-filled' : '' }}">
                                                <label class="form-label">School Name</label>
                                                <input type="text" class="form-control" name="school_name"
                                                    value="{{ $setting->school_name ?? ($setting->title ?? '') }}" required
                                                    maxlength="255">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">School Type</label>
                                            <select class="form-control" name="school_type">
                                                <option value="">Select School Type</option>
                                                <option value="Primary" {{ ($setting->school_type ?? '') === 'Primary' ? 'selected' : '' }}>
                                                    Primary School</option>
                                                <option value="Secondary" {{ ($setting->school_type ?? '') === 'Secondary' ? 'selected' : '' }}>
                                                    Secondary School</option>
                                                <option value="Combined" {{ ($setting->school_type ?? '') === 'Combined' ? 'selected' : '' }}>
                                                    Combined School</option>
                                                <option value="International" {{ ($setting->school_type ?? '') === 'International' ? 'selected' : '' }}>
                                                    International School</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">School Motto</label>
                                            <input type="text" class="form-control" name="school_motto"
                                                value="{{ $setting->school_motto ?? '' }}" maxlength="255">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">Principal Name</label>
                                            <input type="text" class="form-control" name="principal_name"
                                                value="{{ $setting->principal_name ?? '' }}" maxlength="255">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">Established Year</label>
                                            <input type="number" class="form-control" name="established_year"
                                                value="{{ $setting->established_year ?? '' }}" min="1800" max="2030">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">Total Capacity (Students)</label>
                                            <input type="number" class="form-control" name="total_capacity"
                                                value="{{ $setting->total_capacity ?? '' }}" min="1">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">Website URL</label>
                                            <input type="url" class="form-control" name="website_url"
                                                value="{{ $setting->website_url ?? '' }}"
                                                placeholder="https://www.example.com">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="school-logo-upload-section">
                                            <div class="upload-header mb-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="upload-icon-circle me-3">
                                                        <i class="material-symbols-rounded">school</i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1 upload-title">School Logo</h6>
                                                        <small class="text-muted">Upload your school's official logo</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="logo-upload-container">
                                                <div class="row align-items-center">
                                                    <div class="col-md-4">
                                                        <div class="logo-preview-card">
                                                            <div class="logo-preview-wrapper" id="logo-preview-wrapper">
                                                                @if ($setting->logo ?? '')
                                                                    <img id="logo-preview"
                                                                        src="{{ asset('storage/' . $setting->logo) }}"
                                                                        alt="School Logo Preview" class="logo-preview-image">
                                                                    <div class="logo-overlay">
                                                                        <i class="material-symbols-rounded">edit</i>
                                                                    </div>
                                                                @else
                                                                    <div class="logo-placeholder" id="logo-placeholder">
                                                                        <i
                                                                            class="material-symbols-rounded logo-placeholder-icon">add_photo_alternate</i>
                                                                        <p class="logo-placeholder-text">Click to upload
                                                                            logo</p>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="upload-controls">
                                                            <input type="file" name="logo" id="logo" class="d-none"
                                                                accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                                                                onchange="handleLogoUpload(event)">

                                                            <div class="upload-actions mb-3">
                                                                <button type="button" class="btn btn-primary btn-upload"
                                                                    onclick="document.getElementById('logo').click()">
                                                                    <i
                                                                        class="material-symbols-rounded me-2">cloud_upload</i>
                                                                    Choose Logo
                                                                </button>
                                                                @if ($setting->logo ?? '')
                                                                    <button type="button" class="btn btn-outline-danger ms-2"
                                                                        onclick="removeLogo()">
                                                                        <i class="material-symbols-rounded me-1">delete</i>
                                                                        Remove
                                                                    </button>
                                                                @endif
                                                            </div>

                                                            <div class="upload-requirements">
                                                                <div class="requirement-item">
                                                                    <i
                                                                        class="material-symbols-rounded text-success">check_circle</i>
                                                                    <span>Formats: JPG, PNG, GIF, WebP</span>
                                                                </div>
                                                                <div class="requirement-item">
                                                                    <i
                                                                        class="material-symbols-rounded text-success">check_circle</i>
                                                                    <span>Max size: 2MB</span>
                                                                </div>
                                                                <div class="requirement-item">
                                                                    <i
                                                                        class="material-symbols-rounded text-success">check_circle</i>
                                                                    <span>Recommended: 200x200px (square)</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="material-symbols-rounded me-1">save</i>
                                        Save School Info
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


                <!-- Academic Settings -->
                <div class="col-12">
                    <div class="card my-4 glassmorphism-card">
                        <div class="card-header pb-0">
                            <div class="d-flex align-items-center">
                                <i class="material-symbols-rounded me-2">schedule</i>
                                <h6 class="mb-0">{{ __('settings.academic_settings') }}</h6>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <form id="academic-form">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="academic_year_start"
                                                class="form-label">{{ __('school.academic_year_starts') }}</label>
                                            <div class="input-group input-group-outline">
                                                <select class="form-control" name="academic_year_start"
                                                    id="academic_year_start" required>
                                                    <option value="" disabled selected>
                                                        {{ __('school.academic_year_starts') }}
                                                    </option>
                                                    @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                                        <option value="{{ $month }}" {{ ($setting->academic_year_start ?? 'January') === $month ? 'selected' : '' }}>
                                                            {{ __('settings.' . strtolower($month)) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="academic_year_end"
                                                class="form-label">{{ __('school.academic_year_ends') }}</label>
                                            <div class="input-group input-group-outline">
                                                <select class="form-control" name="academic_year_end" id="academic_year_end"
                                                    required>
                                                    <option value="" disabled selected>
                                                        {{ __('school.academic_year_ends') }}
                                                    </option>
                                                    @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                                        <option value="{{ $month }}" {{ ($setting->academic_year_end ?? 'December') === $month ? 'selected' : '' }}>
                                                            {{ __('settings.' . strtolower($month)) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">{{ __('school.school_start_time') }}</label>
                                            <input type="time" class="form-control" name="school_start_time" required
                                                value="{{ $setting->school_start_time ? \Carbon\Carbon::parse($setting->school_start_time)->format('H:i') : '08:00' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">{{ __('school.school_end_time') }}</label>
                                            <input type="time" class="form-control" name="school_end_time" required
                                                value="{{ $setting->school_end_time ? \Carbon\Carbon::parse($setting->school_end_time)->format('H:i') : '15:00' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="timezone" class="form-label">Timezone</label>
                                            <div class="input-group input-group-outline">
                                                <select class="form-control" name="timezone" id="timezone" required>
                                                    <option value="" disabled selected>Select Timezone</option>
                                                    <option value="Asia/Colombo" {{ ($setting->timezone ?? 'Asia/Colombo') === 'Asia/Colombo' ? 'selected' : '' }}>Sri Lanka
                                                        (Asia/Colombo)</option>
                                                    <option value="America/New_York" {{ ($setting->timezone ?? '') === 'America/New_York' ? 'selected' : '' }}>USA - Eastern
                                                        (America/New_York)</option>
                                                    <option value="America/Chicago" {{ ($setting->timezone ?? '') === 'America/Chicago' ? 'selected' : '' }}>USA - Central
                                                        (America/Chicago)</option>
                                                    <option value="America/Los_Angeles" {{ ($setting->timezone ?? '') === 'America/Los_Angeles' ? 'selected' : '' }}>USA - Pacific
                                                        (America/Los_Angeles)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="material-symbols-rounded me-1">save</i>
                                        {{ __('school.save_academic_settings') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Attendance System Mode -->
                <div class="col-12">
                    <div class="card my-4 glassmorphism-card">
                        <div class="card-header pb-0">
                            <div class="d-flex align-items-center">
                                <i class="material-symbols-rounded me-2">how_to_reg</i>
                                <h6 class="mb-0">Attendance System</h6>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <p class="text-sm text-muted mb-4">
                                Choose how the system records student attendance.
                                Only one mode is active at a time. Switching modes does not delete existing records.
                            </p>

                            <div class="row g-3">
                                {{-- RFID card --}}
                                <div class="col-md-6">
                                    <label
                                        class="atm-card d-flex align-items-start gap-3 p-3 rounded-3 border cursor-pointer
                                                                                                {{ ($setting->attendance_mode ?? 'rfid') === 'rfid' ? 'atm-card--active border-primary' : 'border' }}"
                                        for="atm-rfid" id="atm-card-rfid">
                                        <input type="radio" id="atm-rfid" name="attendance_mode" value="rfid"
                                            class="atm-radio d-none" {{ ($setting->attendance_mode ?? 'rfid') === 'rfid' ? 'checked' : '' }}>
                                        <div class="atm-card-icon flex-shrink-0 rounded-3 d-flex align-items-center justify-content-center"
                                            style="width:52px;height:52px;background:#f3e8ff;">
                                            <i class="material-symbols-rounded"
                                                style="color:#7c3aed;font-size:1.8rem">contactless</i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <strong>RFID Wristband</strong>
                                                <span
                                                    class="badge {{ ($setting->attendance_mode ?? 'rfid') === 'rfid' ? 'bg-success' : 'bg-secondary' }}"
                                                    id="atm-rfid-badge">
                                                    {{ ($setting->attendance_mode ?? 'rfid') === 'rfid' ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>
                                            <p class="text-muted small mb-0 mt-1">
                                                Students tap RFID wristbands on the card reader connected via Arduino.
                                                Fast, contact-free and reliable.
                                            </p>
                                        </div>
                                    </label>
                                </div>

                                {{-- Face Recognition card --}}
                                <div class="col-md-6">
                                    <label
                                        class="atm-card d-flex align-items-start gap-3 p-3 rounded-3 border cursor-pointer
                                                                                                {{ ($setting->attendance_mode ?? 'rfid') === 'face_recognition' ? 'atm-card--active border-primary' : 'border' }}"
                                        for="atm-face" id="atm-card-face">
                                        <input type="radio" id="atm-face" name="attendance_mode" value="face_recognition"
                                            class="atm-radio d-none" {{ ($setting->attendance_mode ?? 'rfid') === 'face_recognition' ? 'checked' : '' }}>
                                        <div class="atm-card-icon flex-shrink-0 rounded-3 d-flex align-items-center justify-content-center"
                                            style="width:52px;height:52px;background:#dbeafe;">
                                            <i class="material-symbols-rounded"
                                                style="color:#2563eb;font-size:1.8rem">face_retouching_natural</i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <strong>Facial Recognition</strong>
                                                <span
                                                    class="badge {{ ($setting->attendance_mode ?? 'rfid') === 'face_recognition' ? 'bg-success' : 'bg-secondary' }}"
                                                    id="atm-face-badge">
                                                    {{ ($setting->attendance_mode ?? 'rfid') === 'face_recognition' ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>
                                            <p class="text-muted small mb-0 mt-1">
                                                Camera identifies student faces automatically.
                                                Requires face registration per student before use.
                                            </p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div id="atm-save-wrap" class="text-end mt-4 d-none">
                                <button type="button" class="btn btn-primary px-4" onclick="saveAttendanceMode()">
                                    <i class="material-symbols-rounded me-1 align-middle" style="font-size:1rem">save</i>
                                    Save Mode
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance Timing Settings -->
                <div class="col-12">
                    <div class="card my-4 glassmorphism-card">
                        <div class="card-header pb-0">
                            <div class="d-flex align-items-center">
                                <i class="material-symbols-rounded me-2">schedule</i>
                                <h6 class="mb-0">Attendance Timing</h6>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <p class="text-sm text-muted mb-4">
                                Define the expected check-in and check-out times. Students who check in after the
                                grace period will be automatically marked as <strong>Late</strong>.
                            </p>
                            <form id="attendance-timing-form">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">Check-in Deadline</label>
                                            <input type="time" class="form-control" name="checkin_deadline" required
                                                value="{{ $setting->checkin_deadline ?? '08:00' }}">
                                        </div>
                                        <small class="text-muted">Students must check in by this time.</small>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">Expected Check-out Time</label>
                                            <input type="time" class="form-control" name="checkout_time" required
                                                value="{{ $setting->checkout_time ?? '15:00' }}">
                                        </div>
                                        <small class="text-muted">The scheduled end-of-school time.</small>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">Grace Period (minutes)</label>
                                            <input type="number" class="form-control" name="late_after_minutes" min="0"
                                                max="480" required value="{{ $setting->late_after_minutes ?? 15 }}">
                                        </div>
                                        <small class="text-muted">
                                            Students arriving more than this many minutes after the check-in
                                            deadline will be marked <strong>Late</strong>.
                                        </small>
                                    </div>
                                </div>
                                <div class="alert alert-info py-2 mt-2 mb-3" role="alert" style="font-size:.85rem">
                                    <i class="material-symbols-rounded align-middle me-1" style="font-size:1rem">info</i>
                                    Example: Deadline <strong>08:00</strong>, Grace period <strong>15 min</strong> →
                                    arriving after <strong>08:15</strong> is marked Late. This rule applies to
                                    Face Recognition, RFID, and Manual attendance entries.
                                </div>
                                <div class="text-end">
                                    <button type="button" class="btn btn-primary" onclick="saveAttendanceTiming()">
                                        <i class="material-symbols-rounded me-1">save</i>
                                        Save Timing Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Language Settings -->
                <div class="col-12">
                    <div class="card my-4 glassmorphism-card">
                        <div class="card-header pb-0">
                            <div class="d-flex align-items-center">
                                <i class="material-symbols-rounded me-2">language</i>
                                <h6 class="mb-0">{{ __('settings.language_settings') }}</h6>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <form id="language-form">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">{{ __('settings.language') }}</label>
                                            <select class="form-control" name="language" required
                                                onchange="previewLanguageChange(this.value)">
                                                <option value="">{{ __('settings.select_language') }}</option>
                                                <option value="en" {{ ($setting->language ?? 'en') === 'en' ? 'selected' : '' }}>
                                                    {{ __('settings.english') }}
                                                </option>
                                                <option value="si" {{ ($setting->language ?? 'en') === 'si' ? 'selected' : '' }}>
                                                    {{ __('settings.sinhala') }}
                                                </option>
                                            </select>
                                        </div>
                                        <small class="text-muted">{{ __('common.preview_below') }}</small>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="language-preview p-3" style="background: #f8f9fa; border-radius: 8px;">
                                            <h6 class="mb-2" id="preview-title">{{ __('settings.language') }}</h6>
                                            <p class="mb-1 text-sm" id="preview-dashboard">{{ __('common.dashboard') }}
                                            </p>
                                            <p class="mb-1 text-sm" id="preview-settings">{{ __('common.settings') }}</p>
                                            <p class="mb-0 text-sm" id="preview-save">{{ __('common.save') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="material-symbols-rounded me-1">save</i>
                                        {{ __('common.save') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Logo preview callback for immediate sidebar update
        window.onFilePreviewLogo = function (dataUrl, file) {
            // Update sidebar logo immediately with preview
            const sidebarLogo = document.querySelector('.sidebar-logo');
            if (sidebarLogo) {
                sidebarLogo.src = dataUrl;
            }
        };

        // Beautiful logo upload handler
        function handleLogoUpload(event) {
            const file = event.target.files[0];
            const previewWrapper = document.getElementById('logo-preview-wrapper');
            const maxSize = 2048 * 1024; // 2MB in bytes
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];

            if (!file) return;

            // Validate file size
            if (file.size > maxSize) {
                showNotification('File size must be less than 2MB', 'error');
                event.target.value = '';
                return;
            }

            // Validate file type
            if (!allowedTypes.includes(file.type)) {
                showNotification('Please select a valid image file (JPG, PNG, GIF, WebP)', 'error');
                event.target.value = '';
                return;
            }

            // Show loading state
            previewWrapper.innerHTML = `
                <div class="logo-placeholder">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="logo-placeholder-text mt-2">Uploading...</p>
                </div>
                `;

            const reader = new FileReader();
            reader.onload = function (e) {
                // Create new preview with image
                previewWrapper.innerHTML = `
                                                                            <img id="logo-preview" src="${e.target.result}"
                                                                                 alt="School Logo Preview" class="logo-preview-image upload-success">
                                                                            <div class="logo-overlay">
                                                                                <i class="material-symbols-rounded">edit</i>
                                                                            </div>
                                                                        `;

                // Add click handler to preview for re-upload
                previewWrapper.onclick = function () {
                    document.getElementById('logo').click();
                };

                // Update sidebar logo immediately
                const sidebarLogo = document.querySelector('.sidebar-logo');
                if (sidebarLogo) {
                    sidebarLogo.src = e.target.result;
                }

                // Show success notification
                showNotification('Logo uploaded successfully!', 'success');
            };

            reader.onerror = function () {
                showNotification('Error reading file', 'error');
                resetLogoPlaceholder();
            };

            reader.readAsDataURL(file);
        }

        // Remove logo function
        function removeLogo() {
            if (confirm('Are you sure you want to remove the school logo?')) {
                document.getElementById('logo').value = '';
                resetLogoPlaceholder();

                // Reset sidebar logo to default
                const sidebarLogo = document.querySelector('.sidebar-logo');
                if (sidebarLogo) {
                    sidebarLogo.src = '{{ asset('assets/img/default-logo.png') }}'; // Add your default logo path
                }

                showNotification('Logo removed successfully!', 'success');
            }
        }

        // Reset logo placeholder
        function resetLogoPlaceholder() {
            const previewWrapper = document.getElementById('logo-preview-wrapper');
            previewWrapper.innerHTML = `
                <div class="logo-placeholder" id="logo-placeholder">
                    <i class="material-symbols-rounded logo-placeholder-icon">add_photo_alternate</i>
                    <p class="logo-placeholder-text">Click to upload logo</p>
                </div>
                `;

            // Add click handler to placeholder
            previewWrapper.onclick = function () {
                document.getElementById('logo').click();
            };
        }

        // Add click handler to logo preview wrapper on page load
        document.addEventListener('DOMContentLoaded', function () {
            const previewWrapper = document.getElementById('logo-preview-wrapper');
            if (previewWrapper) {
                previewWrapper.onclick = function () {
                    document.getElementById('logo').click();
                };
            }
        });

        // Theme customization functions
        function updateThemePreview() {
            const primaryColor = document.getElementById("primary_color").value;
            const secondaryColor = document.getElementById("secondary_color").value;
            const accentColor = document.getElementById("accent_color").value;

            // Update text inputs for primary colors
            document.getElementById("primary_color_text").value = primaryColor;
            document.getElementById("secondary_color_text").value = secondaryColor;
            document.getElementById("accent_color_text").value = accentColor;

            // Get status colors if they exist
            const successColor = document.getElementById("success-color")?.value || '#10B981';
            const infoColor = document.getElementById("info-color")?.value || '#3B82F6';
            const warningColor = document.getElementById("warning-color")?.value || '#F59E0B';
            const dangerColor = document.getElementById("danger-color")?.value || '#EF4444';

            // Update status color text inputs
            if (document.getElementById("success-color-text")) {
                document.getElementById("success-color-text").value = successColor;
            }
            if (document.getElementById("info-color-text")) {
                document.getElementById("info-color-text").value = infoColor;
            }
            if (document.getElementById("warning-color-text")) {
                document.getElementById("warning-color-text").value = warningColor;
            }
            if (document.getElementById("danger-color-text")) {
                document.getElementById("danger-color-text").value = dangerColor;
            }

            // Apply comprehensive theme colors
            const root = document.documentElement;
            root.style.setProperty('--primary-green', primaryColor);
            root.style.setProperty('--light-green', secondaryColor);
            root.style.setProperty('--dark-green', secondaryColor);
            root.style.setProperty('--accent-green', accentColor);
            root.style.setProperty('--success-green', successColor);
            root.style.setProperty('--info-blue', infoColor);
            root.style.setProperty('--warning-orange', warningColor);
            root.style.setProperty('--danger-red', dangerColor);

            // Convert colors to RGB for rgba usage
            const primaryRgb = hexToRgb(primaryColor);
            const secondaryRgb = hexToRgb(secondaryColor);
            const accentRgb = hexToRgb(accentColor);

            if (primaryRgb) {
                root.style.setProperty('--primary-rgb', `${primaryRgb.r}, ${primaryRgb.g}, ${primaryRgb.b}`);
            }
            if (secondaryRgb) {
                root.style.setProperty('--secondary-rgb', `${secondaryRgb.r}, ${secondaryRgb.g}, ${secondaryRgb.b}`);
            }
            if (accentRgb) {
                root.style.setProperty('--accent-rgb', `${accentRgb.r}, ${accentRgb.g}, ${accentRgb.b}`);
            }

            // Apply colors immediately for preview
            applyThemeColors(primaryColor, secondaryColor, accentColor);

            // Update gradient previews
            updateGradientPreview('primary');
            updateGradientPreview('secondary');

            // Show preview badge
            showColorPreview(primaryColor, secondaryColor, accentColor);

            console.log('Comprehensive theme colors applied:', {
                primaryColor,
                secondaryColor,
                accentColor,
                // Form submission handlers
                document.addEventListener('DOMContentLoaded', function () {
                    document.getElementById('school-info-form').addEventListener('submit', function (e) {
                        e.preventDefault();
                        submitForm('school-info', '{{ route('admin.setup.settings.school-info') }}');
                    });


                    document.getElementById('academic-form').addEventListener('submit', function (e) {
                        e.preventDefault();
                        submitForm('academic', '{{ route('admin.setup.settings.academic') }}');
                    });

                    // Language form handler
                    document.getElementById('language-form').addEventListener('submit', function (e) {
                        e.preventDefault();
                        submitForm('language', '{{ route('admin.setup.settings.language') }}');
                    });
                });

                function submitForm(type, url) {
                const formData = new FormData(document.getElementById(type + '-form'));

                fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
                                                                        })
                                                                        .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Server error');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showNotification('Settings saved successfully!', 'success');

                    // If logo was uploaded, update sidebar logo
                    if (type === 'school-info' && data.logo_url) {
                        updateSidebarLogo(data.logo_url);
                    }
                } else {
                    console.error('Validation errors:', data.errors);
                    let errorMessage = 'Error saving settings';
                    if (data.errors) {
                        const errorFields = Object.keys(data.errors);
                        errorMessage += ': ' + errorFields.join(', ') + ' validation failed';
                    }
                    showNotification(errorMessage, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error saving settings: ' + error.message, 'error');
            });
                                                                }


        function showNotification(message, type) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 1060;
                min-width: 300px;
                `;
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
                                                                    `;

            document.body.appendChild(notification);

            // Auto remove after 3 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 3000);
        }

        function updateSidebarLogo(logoUrl) {
            // Update sidebar logo immediately without page refresh
            const sidebarLogo = document.querySelector('.sidebar-logo');
            if (sidebarLogo) {
                sidebarLogo.src = logoUrl;
            }

            // Also update the x-input preview if it exists
            const logoPreview = document.getElementById('logo-preview');
            if (logoPreview && logoPreview.tagName === 'IMG') {
                logoPreview.src = logoUrl;
            }
        }

        // Language preview functionality
        const translations = {
            'en': {
                'language': 'Language',
                'dashboard': 'Dashboard',
                'settings': 'Settings',
                'save': 'Save'
            },
            'si': {
                'language': 'භාෂාව',
                'dashboard': 'පාලක පුවරුව',
                'settings': 'සැකසීම්',
                'save': 'සුරකින්න'
            }
        };

        function previewLanguageChange(lang) {
            if (lang && translations[lang]) {
                const previewTitle = document.getElementById('preview-title');
                const previewDashboard = document.getElementById('preview-dashboard');
                const previewSettings = document.getElementById('preview-settings');
                const previewSave = document.getElementById('preview-save');

                if (previewTitle) previewTitle.textContent = translations[lang]['language'];
                if (previewDashboard) previewDashboard.textContent = translations[lang]['dashboard'];
                if (previewSettings) previewSettings.textContent = translations[lang]['settings'];
                if (previewSave) previewSave.textContent = translations[lang]['save'];
            }
        }
    </script>

    {{-- ── Attendance Mode JS + CSS ─────────────────────────── --}}
    <script>
        (function () {
            const cards = document.querySelectorAll('.atm-card');
            const radios = document.querySelectorAll('.atm-radio');
            const saveWrap = document.getElementById('atm-save-wrap');

            cards.forEach(card => {
                card.addEventListener('click', function () {
                    const radio = this.querySelector('.atm-radio');
                    if (!radio) return;
                    // Deselect all
                    cards.forEach(c => {
                        c.classList.remove('atm-card--active', 'border-primary');
                        c.classList.add('border');
                    });
                    // Select clicked
                    this.classList.add('atm-card--active', 'border-primary');
                    radio.checked = true;
                    // Update badges
                    document.getElementById('atm-rfid-badge').className = 'badge bg-secondary';
                    document.getElementById('atm-rfid-badge').textContent = 'Inactive';
                    document.getElementById('atm-face-badge').className = 'badge bg-secondary';
                    document.getElementById('atm-face-badge').textContent = 'Inactive';
                    const activeBadgeId = radio.value === 'rfid' ? 'atm-rfid-badge' : 'atm-face-badge';
                    document.getElementById(activeBadgeId).className = 'badge bg-success';
                    document.getElementById(activeBadgeId).textContent = 'Active';
                    // Show save button
                    saveWrap.classList.remove('d-none');
                });
            });
        })();

        function saveAttendanceMode() {
            const checked = document.querySelector('.atm-radio:checked');
            if (!checked) return;
            const btn = document.querySelector('#atm-save-wrap .btn');
            const orig = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving…';

            fetch('{{ route('admin.setup.settings.attendance-mode') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                },
                body: JSON.stringify({
                    attendance_mode: checked.value
                }),
            })
                .then(r => r.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = orig;
                    if (data.success) {
                        document.getElementById('atm-save-wrap').classList.add('d-none');
                        // Show a toast if the page has one
                        if (typeof showToast === 'function') {
                            showToast('Attendance mode saved.', 'success');
                        } else {
                            alert('Attendance mode updated successfully.');
                        }
                    } else {
                        alert(data.message || 'Failed to update attendance mode.');
                    }
                })
                .catch(() => {
                    btn.disabled = false;
                    btn.innerHTML = orig;
                    alert('Network error — please try again.');
                });
        }

        function saveAttendanceTiming() {
            const form = document.getElementById('attendance-timing-form');
            const deadline = form.querySelector('[name="checkin_deadline"]').value;
            const checkout = form.querySelector('[name="checkout_time"]').value;
            const lateMin = form.querySelector('[name="late_after_minutes"]').value;

            if (!deadline || !checkout || lateMin === '') {
                alert('Please fill in all attendance timing fields.');
                return;
            }

            const btn = form.querySelector('button[onclick="saveAttendanceTiming()"]');
            const orig = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving…';

            fetch('{{ route('admin.setup.settings.attendance-timing') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                },
                body: JSON.stringify({
                    checkin_deadline: deadline,
                    checkout_time: checkout,
                    late_after_minutes: parseInt(lateMin, 10),
                }),
            })
                .then(r => r.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = orig;
                    if (data.success) {
                        if (typeof showToast === 'function') {
                            showToast('Attendance timing settings saved.', 'success');
                        } else {
                            alert('Attendance timing settings updated successfully.');
                        }
                    } else {
                        alert(data.message || 'Failed to save attendance timing settings.');
                    }
                })
                .catch(() => {
                    btn.disabled = false;
                    btn.innerHTML = orig;
                    alert('Network error — please try again.');
                });
        }
    </script>

    <style>
        .atm-card {
            cursor: pointer;
            transition: border-color .2s, box-shadow .2s, background .2s;
            background: #fff;
        }

        .atm-card:hover {
            background: #f8f9ff;
        }

        .atm-card--active {
            background: #eff6ff !important;
            box-shadow: 0 0 0 2px #2563eb33;
        }
    </style>
@endsection