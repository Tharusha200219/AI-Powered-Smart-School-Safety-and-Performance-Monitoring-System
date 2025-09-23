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
                </div>

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
                            <form id="school-info-form">
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
                                                <input type="color" class="form-control color-picker" id="primary_color"
                                                    name="primary_color" value="{{ $setting->primary_color ?? '#06C167' }}"
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
                                            <input type="date" class="form-control" name="academic_year_start"
                                                value="{{ $setting->academic_year_start ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">Academic Year End</label>
                                            <input type="date" class="form-control" name="academic_year_end"
                                                value="{{ $setting->academic_year_end ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">School Start Time</label>
                                            <input type="time" class="form-control" name="school_start_time"
                                                value="{{ $setting->school_start_time ?? '08:00' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">School End Time</label>
                                            <input type="time" class="form-control" name="school_end_time"
                                                value="{{ $setting->school_end_time ?? '15:00' }}">
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
        // Theme customization functions
        function updateThemePreview() {
            const primaryColor = document.getElementById("primary_color").value;
            const secondaryColor = document.getElementById("secondary_color").value;
            const accentColor = document.getElementById("accent_color").value;

            // Update text inputs
            document.getElementById("primary_color_text").value = primaryColor;
            document.getElementById("secondary_color_text").value = secondaryColor;
            document.getElementById("accent_color_text").value = accentColor;

            // Apply colors immediately for preview
            applyThemeColors(primaryColor, secondaryColor, accentColor);

            // Show preview badge
            showColorPreview(primaryColor, secondaryColor, accentColor);
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
                root.style.setProperty('--accent-green', accent);

                // Update Bootstrap variables
                root.style.setProperty('--bs-primary', primary);
                root.style.setProperty('--bs-secondary', secondary);
                root.style.setProperty('--bs-success', primary);

                // Update glassmorphism colors with transparency
                root.style.setProperty('--primary-rgba', `${primaryRgb.r}, ${primaryRgb.g}, ${primaryRgb.b}`);
                root.style.setProperty('--secondary-rgba', `${secondaryRgb.r}, ${secondaryRgb.g}, ${secondaryRgb.b}`);
                root.style.setProperty('--accent-rgba', `${accentRgb.r}, ${accentRgb.g}, ${accentRgb.b}`);

                // Update specific elements that use these colors
                const elementsToUpdate = [
                    '.btn-primary',
                    '.bg-gradient-primary',
                    '.text-primary',
                    '.border-primary',
                    '.navbar-brand',
                    '.nav-link.active',
                    '.btn-outline-primary'
                ];

                elementsToUpdate.forEach(selector => {
                    const elements = document.querySelectorAll(selector);
                    elements.forEach(element => {
                        if (selector.includes('btn-primary')) {
                            element.style.backgroundColor = primary;
                            element.style.borderColor = primary;
                        } else if (selector.includes('text-primary')) {
                            element.style.color = primary;
                        } else if (selector.includes('border-primary')) {
                            element.style.borderColor = primary;
                        }
                    });
                });
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
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Settings saved successfully!', 'success');
                    } else {
                        showNotification('Error saving settings', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error saving settings', 'error');
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
    </script>
@endsection
