@extends('admin.layouts.app')

@section('title', pageTitle())

@section('css')
    @vite('resources/css/admin/forms.css')
    <style>
        .card {
            border-radius: 12px !important;
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
        }

        .input-group-outline {
            margin-bottom: 1.5rem !important;
        }

        .input-group-outline .form-control {
            border-radius: 8px !important;
            border: 1.5px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .input-group-outline .form-control:focus {
            border-color: #5e72e4;
            box-shadow: 0 0 0 0.2rem rgba(94, 114, 228, 0.15);
        }

        .input-group-outline.is-focused .form-label {
            color: #5e72e4;
        }

        .form-section {
            border-left: 4px solid #5e72e4;
            background: linear-gradient(135deg, rgba(94, 114, 228, 0.05) 0%, rgba(123, 136, 238, 0.03) 100%);
            padding: 1.5rem;
            border-radius: 0 8px 8px 0;
            margin-bottom: 2rem;
        }

        .form-section h6 {
            color: #5e72e4;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .btn {
            border-radius: 8px !important;
            font-weight: 600;
            text-transform: none;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #5e72e4 0%, #7b88ee 100%) !important;
            border: none !important;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #8a9296 100%) !important;
            border: none !important;
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            border-radius: 12px 12px 0 0 !important;
            border: none !important;
            color: white;
        }

        .card-header h6 {
            color: white !important;
            margin-bottom: 0;
        }

        .photo-upload {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .photo-upload:hover {
            border-color: #5e72e4;
            background-color: rgba(94, 114, 228, 0.02);
        }

        .photo-preview {
            max-width: 150px;
            max-height: 150px;
            border-radius: 8px;
            object-fit: cover;
        }
    </style>
@endsection

@section('content')
    @include('admin.layouts.sidebar')

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        @include('admin.layouts.navbar')

        <div class="container-fluid pt-2">
            <div class="row">
                <div class="col-12">
                    @include('admin.layouts.flash')
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6 d-flex align-items-center">
                                    <h6 class="mb-0">
                                        <i class="material-symbols-rounded me-2">security</i>
                                        {{ isset($security) ? 'Edit Security Staff' : 'Create Security Staff' }}
                                    </h6>
                                </div>
                                <div class="col-6 text-end">
                                    <a class="btn btn-secondary mb-0" href="{{ route('admin.management.security.index') }}">
                                        <i class="material-symbols-rounded text-sm me-1">arrow_back</i>
                                        Back to Security Staff
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <form action="{{ route('admin.management.security.enroll') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @if (isset($security))
                                    <input type="hidden" name="id" value="{{ $security->security_id }}">
                                @endif

                                <!-- Basic Information Section -->
                                <div class="form-section">
                                    <h6>
                                        <i class="material-symbols-rounded me-2" style="color: #5e72e4;">person</i>
                                        Personal Information
                                    </h6>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Security Code *</label>
                                                <input type="text" name="security_code" class="form-control"
                                                    value="{{ old('security_code', $security->security_code ?? '') }}"
                                                    required>
                                            </div>
                                            @error('security_code')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Employee ID</label>
                                                <input type="text" name="employee_id" class="form-control"
                                                    value="{{ old('employee_id', $security->employee_id ?? '') }}">
                                            </div>
                                            @error('employee_id')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">First Name *</label>
                                                <input type="text" name="first_name" class="form-control"
                                                    value="{{ old('first_name', $security->first_name ?? '') }}" required>
                                            </div>
                                            @error('first_name')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Middle Name</label>
                                                <input type="text" name="middle_name" class="form-control"
                                                    value="{{ old('middle_name', $security->middle_name ?? '') }}">
                                            </div>
                                            @error('middle_name')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Last Name *</label>
                                                <input type="text" name="last_name" class="form-control"
                                                    value="{{ old('last_name', $security->last_name ?? '') }}" required>
                                            </div>
                                            @error('last_name')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Date of Birth</label>
                                                <input type="date" name="date_of_birth" class="form-control"
                                                    value="{{ old('date_of_birth', isset($security) && $security->date_of_birth ? $security->date_of_birth->format('Y-m-d') : '') }}">
                                            </div>
                                            @error('date_of_birth')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Gender</label>
                                                <select name="gender" class="form-control">
                                                    <option value="">Select Gender</option>
                                                    <option value="Male"
                                                        {{ old('gender', $security->gender ?? '') == 'Male' ? 'selected' : '' }}>
                                                        Male
                                                    </option>
                                                    <option value="Female"
                                                        {{ old('gender', $security->gender ?? '') == 'Female' ? 'selected' : '' }}>
                                                        Female
                                                    </option>
                                                    <option value="Other"
                                                        {{ old('gender', $security->gender ?? '') == 'Other' ? 'selected' : '' }}>
                                                        Other
                                                    </option>
                                                </select>
                                            </div>
                                            @error('gender')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Nationality</label>
                                                <input type="text" name="nationality" class="form-control"
                                                    value="{{ old('nationality', $security->nationality ?? '') }}">
                                            </div>
                                            @error('nationality')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="email" class="form-control"
                                                    value="{{ old('email', $security->email ?? '') }}">
                                            </div>
                                            @error('email')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Employment Information Section -->
                                <div class="form-section">
                                    <h6>
                                        <i class="material-symbols-rounded me-2" style="color: #5e72e4;">work</i>
                                        Employment Information
                                    </h6>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Joining Date</label>
                                                <input type="date" name="joining_date" class="form-control"
                                                    value="{{ old('joining_date', isset($security) && $security->joining_date ? $security->joining_date->format('Y-m-d') : '') }}">
                                            </div>
                                            @error('joining_date')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Position</label>
                                                <select name="position" class="form-control">
                                                    <option value="">Select Position</option>
                                                    <option value="Security Guard"
                                                        {{ old('position', $security->position ?? '') == 'Security Guard' ? 'selected' : '' }}>
                                                        Security Guard
                                                    </option>
                                                    <option value="Security Supervisor"
                                                        {{ old('position', $security->position ?? '') == 'Security Supervisor' ? 'selected' : '' }}>
                                                        Security Supervisor
                                                    </option>
                                                    <option value="Security Manager"
                                                        {{ old('position', $security->position ?? '') == 'Security Manager' ? 'selected' : '' }}>
                                                        Security Manager
                                                    </option>
                                                    <option value="Gate Keeper"
                                                        {{ old('position', $security->position ?? '') == 'Gate Keeper' ? 'selected' : '' }}>
                                                        Gate Keeper
                                                    </option>
                                                    <option value="Campus Security"
                                                        {{ old('position', $security->position ?? '') == 'Campus Security' ? 'selected' : '' }}>
                                                        Campus Security
                                                    </option>
                                                </select>
                                            </div>
                                            @error('position')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Shift</label>
                                                <select name="shift" class="form-control">
                                                    <option value="">Select Shift</option>
                                                    <option value="Morning"
                                                        {{ old('shift', $security->shift ?? '') == 'Morning' ? 'selected' : '' }}>
                                                        Morning (6:00 AM - 2:00 PM)
                                                    </option>
                                                    <option value="Evening"
                                                        {{ old('shift', $security->shift ?? '') == 'Evening' ? 'selected' : '' }}>
                                                        Evening (2:00 PM - 10:00 PM)
                                                    </option>
                                                    <option value="Night"
                                                        {{ old('shift', $security->shift ?? '') == 'Night' ? 'selected' : '' }}>
                                                        Night (10:00 PM - 6:00 AM)
                                                    </option>
                                                    <option value="Rotating"
                                                        {{ old('shift', $security->shift ?? '') == 'Rotating' ? 'selected' : '' }}>
                                                        Rotating Shifts
                                                    </option>
                                                </select>
                                            </div>
                                            @error('shift')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Status</label>
                                                <select name="is_active" class="form-control">
                                                    <option value="1"
                                                        {{ old('is_active', $security->is_active ?? '1') == '1' ? 'selected' : '' }}>
                                                        Active
                                                    </option>
                                                    <option value="0"
                                                        {{ old('is_active', $security->is_active ?? '1') == '0' ? 'selected' : '' }}>
                                                        Inactive
                                                    </option>
                                                </select>
                                            </div>
                                            @error('is_active')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Contact Information Section -->
                                <div class="form-section">
                                    <h6>
                                        <i class="material-symbols-rounded me-2" style="color: #5e72e4;">contact_phone</i>
                                        Contact Information
                                    </h6>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Mobile Phone</label>
                                                <input type="tel" name="mobile_phone" class="form-control"
                                                    value="{{ old('mobile_phone', $security->mobile_phone ?? '') }}">
                                            </div>
                                            @error('mobile_phone')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Home Phone</label>
                                                <input type="tel" name="home_phone" class="form-control"
                                                    value="{{ old('home_phone', $security->home_phone ?? '') }}">
                                            </div>
                                            @error('home_phone')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Address Line 1</label>
                                                <input type="text" name="address_line1" class="form-control"
                                                    value="{{ old('address_line1', $security->address_line1 ?? '') }}">
                                            </div>
                                            @error('address_line1')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Address Line 2</label>
                                                <input type="text" name="address_line2" class="form-control"
                                                    value="{{ old('address_line2', $security->address_line2 ?? '') }}">
                                            </div>
                                            @error('address_line2')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">City</label>
                                                <input type="text" name="city" class="form-control"
                                                    value="{{ old('city', $security->city ?? '') }}">
                                            </div>
                                            @error('city')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">State</label>
                                                <input type="text" name="state" class="form-control"
                                                    value="{{ old('state', $security->state ?? '') }}">
                                            </div>
                                            @error('state')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Postal Code</label>
                                                <input type="text" name="postal_code" class="form-control"
                                                    value="{{ old('postal_code', $security->postal_code ?? '') }}">
                                            </div>
                                            @error('postal_code')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-3">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Country</label>
                                                <input type="text" name="country" class="form-control"
                                                    value="{{ old('country', $security->country ?? '') }}">
                                            </div>
                                            @error('country')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Photo Upload Section -->
                                <div class="form-section">
                                    <h6>
                                        <i class="material-symbols-rounded me-2" style="color: #5e72e4;">photo_camera</i>
                                        Photo
                                    </h6>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="photo-upload">
                                                <input type="file" name="photo" class="form-control"
                                                    accept="image/*" onchange="previewPhoto(this)">
                                                <div class="mt-2">
                                                    <i class="material-symbols-rounded"
                                                        style="font-size: 2rem; color: #6c757d;">upload</i>
                                                    <p class="text-muted mb-0">Click to upload photo</p>
                                                    <small class="text-muted">Max file size: 2MB</small>
                                                </div>
                                            </div>
                                            @error('photo')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <div id="photoPreview">
                                                @if (isset($security) && $security->photo_path)
                                                    <img src="{{ asset('storage/' . $security->photo_path) }}"
                                                        alt="Current Photo" class="photo-preview">
                                                    <p class="text-muted mt-2">Current Photo</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="row mt-4">
                                    <div class="col-12 text-end">
                                        <a href="{{ route('admin.management.security.index') }}"
                                            class="btn btn-secondary me-2">
                                            <i class="material-symbols-rounded me-1">cancel</i>
                                            Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-symbols-rounded me-1">
                                                {{ isset($security) ? 'update' : 'add' }}
                                            </i>
                                            {{ isset($security) ? 'Update Security Staff' : 'Create Security Staff' }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('js')
    <script>
        function previewPhoto(input) {
            const preview = document.getElementById('photoPreview');
            const file = input.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `
                        <img src="${e.target.result}" alt="Photo Preview" class="photo-preview">
                        <p class="text-muted mt-2">Photo Preview</p>
                    `;
                };
                reader.readAsDataURL(file);
            }
        }

        // Auto-generate security code if empty
        document.addEventListener('DOMContentLoaded', function() {
            const securityCodeInput = document.querySelector('input[name="security_code"]');
            if (!securityCodeInput.value) {
                generateSecurityCode();
            }
        });

        function generateSecurityCode() {
            const year = new Date().getFullYear();
            const random = Math.floor(Math.random() * 9999) + 1;
            const securityCode = `SEC${year}${random.toString().padStart(4, '0')}`;
            document.querySelector('input[name="security_code"]').value = securityCode;
        }
    </script>
@endsection
