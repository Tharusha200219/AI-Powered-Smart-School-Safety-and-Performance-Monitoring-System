@extends('admin.layouts.app')

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
                    <p class="mb-4">
                        <i class="material-symbols-rounded opacity-5">settings</i>
                        Configure your school settings and customize themes
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
                                <h6 class="mb-0">School Information</h6>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <form id="school-info-form" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">School Name</label>
                                            <input type="text" class="form-control" name="school_name"
                                                value="{{ $setting->school_name ?? ($setting->title ?? '') }}" required
                                                maxlength="255">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">School Type</label>
                                            <select class="form-control" name="school_type">
                                                <option value="">Select School Type</option>
                                                <option value="Primary"
                                                    {{ ($setting->school_type ?? '') === 'Primary' ? 'selected' : '' }}>
                                                    Primary School</option>
                                                <option value="Secondary"
                                                    {{ ($setting->school_type ?? '') === 'Secondary' ? 'selected' : '' }}>
                                                    Secondary School</option>
                                                <option value="Combined"
                                                    {{ ($setting->school_type ?? '') === 'Combined' ? 'selected' : '' }}>
                                                    Combined School</option>
                                                <option value="International"
                                                    {{ ($setting->school_type ?? '') === 'International' ? 'selected' : '' }}>
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
                                                value="{{ $setting->established_year ?? '' }}" min="1800"
                                                max="2030">
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
                                        <x-input 
                                            name="logo" 
                                            type="file"
                                            title="School Logo" 
                                            :value="$setting->logo ?? ''"
                                            accept="image/jpeg,image/jpg,image/png,image/gif"
                                            :showPreview="true"
                                            :maxSize="2048"
                                            placeholder="Supported formats: JPG, PNG, GIF. Max size: 2MB"
                                        />
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

                <!-- Theme Customization -->
                <div class="col-12">
                    <div class="card my-4 glassmorphism-card">
                        <div class="card-header pb-0">
                            <div class="d-flex align-items-center">
                                <i class="material-symbols-rounded me-2">palette</i>
                                <h6 class="mb-0">Theme Customization</h6>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <form id="theme-form">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Primary Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="form-control color-picker"
                                                    id="primary_color" name="primary_color"
                                                    value="{{ $setting->primary_color ?? '#06C167' }}"
                                                    onchange="updateThemePreview()">
                                                <input type="text" class="form-control color-text"
                                                    id="primary_color_text" name="primary_color_text"
                                                    value="{{ $setting->primary_color ?? '#06C167' }}"
                                                    onchange="updateColorFromText('primary_color')" placeholder="#06C167">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Secondary Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="form-control color-picker"
                                                    id="secondary_color" name="secondary_color"
                                                    value="{{ $setting->secondary_color ?? '#10B981' }}"
                                                    onchange="updateThemePreview()">
                                                <input type="text" class="form-control color-text"
                                                    id="secondary_color_text" name="secondary_color_text"
                                                    value="{{ $setting->secondary_color ?? '#10B981' }}"
                                                    onchange="updateColorFromText('secondary_color')"
                                                    placeholder="#10B981">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Accent Color</label>
                                            <div class="color-picker-group">
                                                <input type="color" class="form-control color-picker" id="accent_color"
                                                    name="accent_color" value="{{ $setting->accent_color ?? '#F0FDF4' }}"
                                                    onchange="updateThemePreview()">
                                                <input type="text" class="form-control color-text"
                                                    id="accent_color_text" name="accent_color_text"
                                                    value="{{ $setting->accent_color ?? '#F0FDF4' }}"
                                                    onchange="updateColorFromText('accent_color')" placeholder="#F0FDF4">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Colors Section -->
                                <div class="color-section mb-4">
                                    <h6 class="mb-3 text-success">Status & Alert Colors</h6>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group mb-3">
                                                <label class="form-label text-success">Success Color</label>
                                                <div class="color-picker-group">
                                                    <input type="color" class="form-control color-picker"
                                                        id="success-color" name="success_color"
                                                        value="{{ $setting->success_color ?? '#10B981' }}"
                                                        onchange="updateThemePreview()">
                                                    <input type="text" class="form-control color-text"
                                                        id="success-color-text" name="success_color_text"
                                                        value="{{ $setting->success_color ?? '#10B981' }}"
                                                        onchange="updateColorFromText('success-color')"
                                                        placeholder="#10B981">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group mb-3">
                                                <label class="form-label text-info">Info Color</label>
                                                <div class="color-picker-group">
                                                    <input type="color" class="form-control color-picker"
                                                        id="info-color" name="info_color"
                                                        value="{{ $setting->info_color ?? '#3B82F6' }}"
                                                        onchange="updateThemePreview()">
                                                    <input type="text" class="form-control color-text"
                                                        id="info-color-text" name="info_color_text"
                                                        value="{{ $setting->info_color ?? '#3B82F6' }}"
                                                        onchange="updateColorFromText('info-color')"
                                                        placeholder="#3B82F6">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group mb-3">
                                                <label class="form-label text-warning">Warning Color</label>
                                                <div class="color-picker-group">
                                                    <input type="color" class="form-control color-picker"
                                                        id="warning-color" name="warning_color"
                                                        value="{{ $setting->warning_color ?? '#F59E0B' }}"
                                                        onchange="updateThemePreview()">
                                                    <input type="text" class="form-control color-text"
                                                        id="warning-color-text" name="warning_color_text"
                                                        value="{{ $setting->warning_color ?? '#F59E0B' }}"
                                                        onchange="updateColorFromText('warning-color')"
                                                        placeholder="#F59E0B">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group mb-3">
                                                <label class="form-label text-danger">Danger Color</label>
                                                <div class="color-picker-group">
                                                    <input type="color" class="form-control color-picker"
                                                        id="danger-color" name="danger_color"
                                                        value="{{ $setting->danger_color ?? '#EF4444' }}"
                                                        onchange="updateThemePreview()">
                                                    <input type="text" class="form-control color-text"
                                                        id="danger-color-text" name="danger_color_text"
                                                        value="{{ $setting->danger_color ?? '#EF4444' }}"
                                                        onchange="updateColorFromText('danger-color')"
                                                        placeholder="#EF4444">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Gradient Color Pairs Section -->
                                <div class="color-section mb-4">
                                    <h6 class="mb-3"
                                        style="background: linear-gradient(45deg, #6366F1, #8B5CF6); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;">
                                        Gradient Color Pairs</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label">Primary Gradient</label>
                                            <div class="gradient-pair mb-3">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <small class="text-muted">Start Color</small>
                                                            <div class="color-picker-group">
                                                                <input type="color" class="form-control color-picker"
                                                                    id="primary-gradient-start"
                                                                    name="primary_gradient_start"
                                                                    value="{{ $setting->primary_gradient_start ?? '#06C167' }}"
                                                                    onchange="updateGradientPreview('primary')">
                                                                <input type="text" class="form-control color-text"
                                                                    id="primary-gradient-start-text"
                                                                    name="primary_gradient_start_text"
                                                                    value="{{ $setting->primary_gradient_start ?? '#06C167' }}"
                                                                    onchange="updateColorFromText('primary-gradient-start')"
                                                                    placeholder="#06C167">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <small class="text-muted">End Color</small>
                                                            <div class="color-picker-group">
                                                                <input type="color" class="form-control color-picker"
                                                                    id="primary-gradient-end" name="primary_gradient_end"
                                                                    value="{{ $setting->primary_gradient_end ?? '#10B981' }}"
                                                                    onchange="updateGradientPreview('primary')">
                                                                <input type="text" class="form-control color-text"
                                                                    id="primary-gradient-end-text"
                                                                    name="primary_gradient_end_text"
                                                                    value="{{ $setting->primary_gradient_end ?? '#10B981' }}"
                                                                    onchange="updateColorFromText('primary-gradient-end')"
                                                                    placeholder="#10B981">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="gradient-preview mt-2" id="primary-gradient-preview"
                                                    style="height: 40px; border-radius: 8px; background: linear-gradient(135deg, {{ $setting->primary_gradient_start ?? '#06C167' }}, {{ $setting->primary_gradient_end ?? '#10B981' }}); box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Secondary Gradient</label>
                                            <div class="gradient-pair mb-3">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <small class="text-muted">Start Color</small>
                                                            <div class="color-picker-group">
                                                                <input type="color" class="form-control color-picker"
                                                                    id="secondary-gradient-start"
                                                                    name="secondary_gradient_start"
                                                                    value="{{ $setting->secondary_gradient_start ?? '#8B5CF6' }}"
                                                                    onchange="updateGradientPreview('secondary')">
                                                                <input type="text" class="form-control color-text"
                                                                    id="secondary-gradient-start-text"
                                                                    name="secondary_gradient_start_text"
                                                                    value="{{ $setting->secondary_gradient_start ?? '#8B5CF6' }}"
                                                                    onchange="updateColorFromText('secondary-gradient-start')"
                                                                    placeholder="#8B5CF6">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <small class="text-muted">End Color</small>
                                                            <div class="color-picker-group">
                                                                <input type="color" class="form-control color-picker"
                                                                    id="secondary-gradient-end"
                                                                    name="secondary_gradient_end"
                                                                    value="{{ $setting->secondary_gradient_end ?? '#EC4899' }}"
                                                                    onchange="updateGradientPreview('secondary')">
                                                                <input type="text" class="form-control color-text"
                                                                    id="secondary-gradient-end-text"
                                                                    name="secondary_gradient_end_text"
                                                                    value="{{ $setting->secondary_gradient_end ?? '#EC4899' }}"
                                                                    onchange="updateColorFromText('secondary-gradient-end')"
                                                                    placeholder="#EC4899">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="gradient-preview mt-2" id="secondary-gradient-preview"
                                                    style="height: 40px; border-radius: 8px; background: linear-gradient(135deg, {{ $setting->secondary_gradient_start ?? '#8B5CF6' }}, {{ $setting->secondary_gradient_end ?? '#EC4899' }}); box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Color Presets -->
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <label class="form-label">Color Presets</label>
                                        <div class="color-presets d-flex gap-2 flex-wrap">
                                            <button type="button" class="btn btn-sm color-preset"
                                                onclick="applyColorPreset('#06C167', '#10B981', '#F0FDF4')"
                                                style="background: linear-gradient(45deg, #06C167, #10B981, #F0FDF4);">
                                                Default Green
                                            </button>
                                            <button type="button" class="btn btn-sm color-preset"
                                                onclick="applyColorPreset('#3B82F6', '#1D4ED8', '#EFF6FF')"
                                                style="background: linear-gradient(45deg, #3B82F6, #1D4ED8, #EFF6FF);">
                                                Blue Ocean
                                            </button>
                                            <button type="button" class="btn btn-sm color-preset"
                                                onclick="applyColorPreset('#8B5CF6', '#7C3AED', '#F3E8FF')"
                                                style="background: linear-gradient(45deg, #8B5CF6, #7C3AED, #F3E8FF);">
                                                Purple Dream
                                            </button>
                                            <button type="button" class="btn btn-sm color-preset"
                                                onclick="applyColorPreset('#F59E0B', '#D97706', '#FEF3C7')"
                                                style="background: linear-gradient(45deg, #F59E0B, #D97706, #FEF3C7);">
                                                Golden Sun
                                            </button>
                                            <button type="button" class="btn btn-sm color-preset"
                                                onclick="applyColorPreset('#EF4444', '#DC2626', '#FEF2F2')"
                                                style="background: linear-gradient(45deg, #EF4444, #DC2626, #FEF2F2);">
                                                Red Energy
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end mt-3">
                                    <button type="button" class="btn btn-outline-secondary me-2"
                                        onclick="resetToDefault()">
                                        <i class="material-symbols-rounded me-1">refresh</i>
                                        Reset to Default
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="material-symbols-rounded me-1">save</i>
                                        Save Theme
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
                                <h6 class="mb-0">Academic Settings</h6>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <form id="academic-form">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">Academic Year Start</label>
                                            <select class="form-control" name="academic_year_start" required>
                                                <option value="">Select Start Month</option>
                                                @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                                    <option value="{{ $month }}"
                                                        {{ ($setting->academic_year_start ?? 'January') === $month ? 'selected' : '' }}>
                                                        {{ $month }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">Academic Year End</label>
                                            <select class="form-control" name="academic_year_end" required>
                                                <option value="">Select End Month</option>
                                                @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                                    <option value="{{ $month }}"
                                                        {{ ($setting->academic_year_end ?? 'December') === $month ? 'selected' : '' }}>
                                                        {{ $month }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">School Start Time</label>
                                            <input type="time" class="form-control" name="school_start_time" required
                                                value="{{ $setting->school_start_time ? \Carbon\Carbon::parse($setting->school_start_time)->format('H:i') : '08:00' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">School End Time</label>
                                            <input type="time" class="form-control" name="school_end_time" required
                                                value="{{ $setting->school_end_time ? \Carbon\Carbon::parse($setting->school_end_time)->format('H:i') : '15:00' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="material-symbols-rounded me-1">save</i>
                                        Save Academic Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <style>
        .color-picker-group {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .color-picker {
            width: 60px;
            height: 40px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            padding: 0;
        }

        .color-text {
            flex: 1;
            font-family: monospace;
            text-transform: uppercase;
        }

        .color-presets {
            margin-top: 8px;
        }

        .color-preset {
            border: 2px solid transparent;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .color-preset:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .glassmorphism-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
    </style>

    <script>
        // Logo preview callback for immediate sidebar update
        window.onFilePreviewLogo = function(dataUrl, file) {
            // Update sidebar logo immediately with preview
            const sidebarLogo = document.querySelector('.sidebar-logo');
            if (sidebarLogo) {
                sidebarLogo.src = dataUrl;
            }
        };
        
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
                successColor,
                infoColor,
                warningColor,
                dangerColor
            });
        }

        function updateGradientPreview(type) {
            const startColor = document.getElementById(`${type}-gradient-start`)?.value;
            const endColor = document.getElementById(`${type}-gradient-end`)?.value;

            if (startColor && endColor) {
                const preview = document.getElementById(`${type}-gradient-preview`);
                if (preview) {
                    preview.style.background = `linear-gradient(135deg, ${startColor}, ${endColor})`;
                }

                // Update text inputs
                const startText = document.getElementById(`${type}-gradient-start-text`);
                const endText = document.getElementById(`${type}-gradient-end-text`);
                if (startText) startText.value = startColor;
                if (endText) endText.value = endColor;

                // Apply gradient to theme system
                const root = document.documentElement;
                if (type === 'primary') {
                    root.style.setProperty('--primary-gradient-start', startColor);
                    root.style.setProperty('--primary-gradient-end', endColor);
                } else if (type === 'secondary') {
                    root.style.setProperty('--secondary-gradient-start', startColor);
                    root.style.setProperty('--secondary-gradient-end', endColor);
                }
            }
        }

        function updateColorFromText(colorType) {
            const textInput = document.getElementById(colorType + "_text");
            const colorInput = document.getElementById(colorType);

            if (isValidHexColor(textInput.value)) {
                colorInput.value = textInput.value;
                updateThemePreview();
            } else {
                // Show error for invalid color
                textInput.style.borderColor = '#EF4444';
                setTimeout(() => {
                    textInput.style.borderColor = '';
                }, 2000);
            }
        }

        function isValidHexColor(hex) {
            return /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(hex);
        }

        function showColorPreview(primary, secondary, accent) {
            // Create or update preview badge
            let previewBadge = document.getElementById('color-preview-badge');
            if (!previewBadge) {
                previewBadge = document.createElement('div');
                previewBadge.id = 'color-preview-badge';
                previewBadge.style.cssText = `
              position: fixed;
              top: 80px;
              right: 20px;
              background: white;
              padding: 10px;
              border-radius: 8px;
              box-shadow: 0 4px 12px rgba(0,0,0,0.15);
              z-index: 1050;
              display: flex;
              align-items: center;
              gap: 8px;
              font-size: 12px;
              font-weight: 500;
              color: #374151;
            `;
                document.body.appendChild(previewBadge);
            }

            previewBadge.innerHTML = `
            <span>Preview:</span>
            <div style="width: 20px; height: 20px; background: ${primary}; border-radius: 4px; border: 1px solid #e5e7eb;"></div>
            <div style="width: 20px; height: 20px; background: ${secondary}; border-radius: 4px; border: 1px solid #e5e7eb;"></div>
            <div style="width: 20px; height: 20px; background: ${accent}; border-radius: 4px; border: 1px solid #e5e7eb;"></div>
            <button onclick="hideColorPreview()" style="background: none; border: none; color: #6B7280; cursor: pointer; padding: 2px;">×</button>
          `;

            previewBadge.style.display = 'flex';
        }

        function hideColorPreview() {
            const previewBadge = document.getElementById('color-preview-badge');
            if (previewBadge) {
                previewBadge.style.display = 'none';
            }
        }

        function hexToRgb(hex) {
            const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            return result ? {
                r: parseInt(result[1], 16),
                g: parseInt(result[2], 16),
                b: parseInt(result[3], 16)
            } : null;
        }

        function applyThemeColors(primary, secondary, accent) {
            const root = document.documentElement;

            // Convert hex to RGB for glassmorphism effects
            const primaryRgb = hexToRgb(primary);
            const secondaryRgb = hexToRgb(secondary);
            const accentRgb = hexToRgb(accent);

            if (primaryRgb && secondaryRgb && accentRgb) {
                // Update CSS custom properties
                root.style.setProperty('--primary-green', primary);
                root.style.setProperty('--light-green', secondary);
                root.style.setProperty('--dark-green', secondary);
                root.style.setProperty('--accent-green', accent);

                // Update Bootstrap variables
                root.style.setProperty('--bs-primary', primary);
                root.style.setProperty('--bs-secondary', secondary);
                root.style.setProperty('--bs-success', primary);

                // Update RGB values for transparency effects
                root.style.setProperty('--primary-rgb', `${primaryRgb.r}, ${primaryRgb.g}, ${primaryRgb.b}`);
                root.style.setProperty('--secondary-rgb', `${secondaryRgb.r}, ${secondaryRgb.g}, ${secondaryRgb.b}`);
                root.style.setProperty('--accent-rgb', `${accentRgb.r}, ${accentRgb.g}, ${accentRgb.b}`);

                // Apply to all themed elements
                const themedElements = [
                    '.btn-primary',
                    '.bg-gradient-primary',
                    '.bg-gradient-dark',
                    '.bg-gradient-secondary',
                    '.bg-primary',
                    '.text-primary',
                    '.border-primary',
                    '.navbar-brand',
                    '.nav-link.active',
                    '.btn-outline-primary',
                    '.stat-icon',
                    '.quick-action-btn',
                    '.card-primary .card-header',
                    '.progress-bar',
                    '.badge-primary',
                    '.icon-background',
                    '.avatar-primary',
                    '.notification-primary'
                ];

                themedElements.forEach(selector => {
                    const elements = document.querySelectorAll(selector);
                    elements.forEach(element => {
                        if (selector.includes('bg-gradient-primary') || selector.includes(
                                'bg-gradient-dark') || selector.includes('bg-gradient-secondary')) {
                            element.style.background = `linear-gradient(135deg, ${primary}, ${secondary})`;
                        } else if (selector.includes('btn-primary') || selector.includes(
                                'quick-action-btn') || selector.includes('stat-icon')) {
                            element.style.background = `linear-gradient(135deg, ${primary}, ${secondary})`;
                            element.style.borderColor = primary;
                            element.style.color = 'white';
                        } else if (selector.includes('bg-primary')) {
                            element.style.backgroundColor = primary;
                        } else if (selector.includes('text-primary')) {
                            element.style.color = primary;
                        } else if (selector.includes('border-primary')) {
                            element.style.borderColor = primary;
                        } else if (selector.includes('btn-outline-primary')) {
                            element.style.color = primary;
                            element.style.borderColor = primary;
                        } else if (selector.includes('nav-link.active') || selector.includes(
                                'card-primary')) {
                            element.style.background = `linear-gradient(135deg, ${primary}, ${secondary})`;
                            element.style.color = 'white';
                        }
                    });
                });

                // Special handling for Material Dashboard classes
                const materialElements = document.querySelectorAll(
                    '.bg-gradient-faded-primary, .bg-gradient-faded-success');
                materialElements.forEach(element => {
                    element.style.background = `linear-gradient(135deg, ${primary}cc, ${secondary}cc)`;
                });

                // Update progress bars
                const progressBars = document.querySelectorAll('.progress-bar');
                progressBars.forEach(bar => {
                    bar.style.background = `linear-gradient(135deg, ${primary}, ${secondary})`;
                });

                // Update form control focus colors
                const style = document.createElement('style');
                style.textContent = `
                    .form-control:focus {
                        border-color: ${primary} !important;
                        box-shadow: 0 0 0 0.2rem ${primary}40 !important;
                    }
                    .form-check-input:checked {
                        background-color: ${primary} !important;
                        border-color: ${primary} !important;
                    }
                `;
                document.head.appendChild(style);
            }
        }

        // Form submission handlers
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('school-info-form').addEventListener('submit', function(e) {
                e.preventDefault();
                submitForm('school-info', '{{ route('admin.setup.settings.school-info') }}');
            });

            document.getElementById('theme-form').addEventListener('submit', function(e) {
                e.preventDefault();
                submitForm('theme', '{{ route('admin.setup.settings.theme') }}');
            });

            document.getElementById('academic-form').addEventListener('submit', function(e) {
                e.preventDefault();
                submitForm('academic', '{{ route('admin.setup.settings.academic') }}');
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
                        hideColorPreview(); // Hide preview after successful save

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

        function applyColorPreset(primary, secondary, accent) {
            document.getElementById('primary_color').value = primary;
            document.getElementById('secondary_color').value = secondary;
            document.getElementById('accent_color').value = accent;
            document.getElementById('primary_color_text').value = primary;
            document.getElementById('secondary_color_text').value = secondary;
            document.getElementById('accent_color_text').value = accent;
            updateThemePreview();
        }

        function resetToDefault() {
            applyColorPreset('#06C167', '#10B981', '#F0FDF4');
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
    </script>
@endsection
