@extends('admin.layouts.app')

@section('title', pageTitle())

@section('css')
<<<<<<< HEAD
    <style>
        .card-header {
            border-radius: 12px 12px 0 0 !important;
        }

=======
    @vite('resources/css/admin/forms.css')
    <style>
>>>>>>> 4358fa2a22b070c3f048b27b38865b1db4389606
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

        .section-divider {
            height: 2px;
            background: linear-gradient(90deg, #5e72e4, #825ee4);
            border-radius: 1px;
            margin: 1rem 0;
        }

        .btn {
            border-radius: 8px !important;
            font-weight: 600;
            text-transform: none;
            letter-spacing: 0.5px;
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
        }

        .btn-outline-warning {
            border-color: #ffc107;
            color: #ffc107;
        }

        .btn-outline-warning:hover {
            background: #ffc107;
            color: #212529;
        }

        .parent-form-border {
            border: 2px dashed #dee2e6 !important;
            border-radius: 12px !important;
            transition: all 0.3s ease;
        }

        .parent-form-border:hover {
            border-color: #5e72e4 !important;
            background-color: rgba(94, 114, 228, 0.02);
        }

        .form-section-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
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
                    <div class="card my-4">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6 d-flex align-items-center">
                                    <h6 class="mb-0">{{ pageTitle() }}</h6>
                                </div>
                                <div class="col-6 text-end">
                                    <a class="btn btn-outline-dark mb-0 d-flex align-items-center justify-content-center btn-back-auto"
                                        href="{{ route('admin.management.students.index') }}">
                                        <i class="material-symbols-rounded me-1 icon-size-md">arrow_back</i>Back
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.management.students.enroll') }}" method="POST" id="studentForm"
                                enctype="multipart/form-data">
                                @csrf
                                @if ($id)
                                    <input type="hidden" name="id" value="{{ $id }}">
                                @endif

                                <!-- Student Information -->
                                <div class="card mb-4 shadow-sm">
                                    <div class="card-header bg-gradient-primary">
                                        <h6 class="mb-0 d-flex align-items-center text-white">
                                            <i class="material-symbols-rounded me-2 icon-size-sm">person</i>
                                            Student Information
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <!-- Profile Image Upload -->
                                        <div class="row mb-4">
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <div class="avatar avatar-xl position-relative mb-3">
                                                        @if (isset($student) && $student->photo_path)
                                                            <img id="profilePreview"
                                                                src="{{ asset('storage/' . $student->photo_path) }}"
                                                                alt="Student Photo"
                                                                class="w-100 border-radius-lg shadow-sm">
                                                        @else
                                                            <div id="profilePreview"
                                                                class="w-100 border-radius-lg shadow-sm bg-gradient-primary d-flex align-items-center justify-content-center"
                                                                style="height: 120px;">
                                                                <i
                                                                    class="material-symbols-rounded text-white text-lg">person</i>
                                                            </div>
                                                        @endif
                                                        <label for="profileImage"
                                                            class="btn btn-sm btn-icon-only bg-gradient-light position-absolute bottom-0 end-0 mb-n2 me-n2 cursor-pointer">
                                                            <i class="material-symbols-rounded text-xs">edit</i>
                                                        </label>
                                                        <input type="file" id="profileImage" name="profile_image"
                                                            accept="image/*" style="display: none;">
                                                    </div>
                                                    <small class="text-muted">Click the edit icon to upload a photo</small>
                                                </div>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div
                                                            class="input-group input-group-outline mb-3 {{ old('student_code', $student->student_code ?? '') ? 'is-filled' : '' }}">
                                                            <label class="form-label">Student Code *</label>
                                                            <input type="text" name="student_code" class="form-control"
                                                                value="{{ old('student_code', $student->student_code ?? '') }}"
                                                                maxlength="50" required readonly
                                                                style="background-color: #f8f9fa; cursor: not-allowed;">
                                                        </div>
                                                        @if (!$id)
                                                            <small class="form-text text-muted">Auto-generated</small>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div
                                                            class="input-group input-group-outline mb-3 {{ old('first_name', $student->first_name ?? '') ? 'is-filled' : '' }}">
                                                            <label class="form-label">First Name *</label>
                                                            <input type="text" name="first_name" class="form-control"
                                                                value="{{ old('first_name', $student->first_name ?? '') }}"
                                                                maxlength="50" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div
                                                            class="input-group input-group-outline mb-3 {{ old('middle_name', $student->middle_name ?? '') ? 'is-filled' : '' }}">
                                                            <label class="form-label">Middle Name</label>
                                                            <input type="text" name="middle_name" class="form-control"
                                                                value="{{ old('middle_name', $student->middle_name ?? '') }}"
                                                                maxlength="50">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-3">
                                                <div
                                                    class="input-group input-group-outline mb-3 {{ old('last_name', $student->last_name ?? '') ? 'is-filled' : '' }}">
                                                    <label class="form-label">Last Name *</label>
                                                    <input type="text" name="last_name" class="form-control"
                                                        value="{{ old('last_name', $student->last_name ?? '') }}"
                                                        maxlength="50" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div
                                                    class="input-group input-group-outline mb-3 {{ old('date_of_birth', $student->date_of_birth ?? '') ? 'is-filled' : '' }}">
                                                    <label class="form-label">Date of Birth *</label>
                                                    <input type="date" name="date_of_birth" class="form-control"
                                                        value="{{ old('date_of_birth', $student->date_of_birth ?? '') }}"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="input-group input-group-outline mb-3">
                                                    <select name="gender" class="form-control" required>
                                                        <option value="">Select Gender</option>
                                                        <option value="M"
                                                            {{ old('gender', $student->gender ?? '') == 'M' ? 'selected' : '' }}>
                                                            Male</option>
                                                        <option value="F"
                                                            {{ old('gender', $student->gender ?? '') == 'F' ? 'selected' : '' }}>
                                                            Female</option>
                                                        <option value="Other"
                                                            {{ old('gender', $student->gender ?? '') == 'Other' ? 'selected' : '' }}>
                                                            Other</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div
                                                    class="input-group input-group-outline mb-3 {{ old('nationality', $student->nationality ?? '') ? 'is-filled' : '' }}">
                                                    <label class="form-label">Nationality</label>
                                                    <input type="text" name="nationality" class="form-control"
                                                        value="{{ old('nationality', $student->nationality ?? '') }}"
                                                        maxlength="50">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div
                                                    class="input-group input-group-outline mb-3 {{ old('religion', $student->religion ?? '') ? 'is-filled' : '' }}">
                                                    <label class="form-label">Religion</label>
                                                    <input type="text" name="religion" class="form-control"
                                                        value="{{ old('religion', $student->religion ?? '') }}"
                                                        maxlength="50">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div
                                                    class="input-group input-group-outline mb-3 {{ old('home_language', $student->home_language ?? '') ? 'is-filled' : '' }}">
                                                    <label class="form-label">Home Language</label>
                                                    <input type="text" name="home_language" class="form-control"
                                                        value="{{ old('home_language', $student->home_language ?? '') }}"
                                                        maxlength="50">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div
                                                    class="input-group input-group-outline mb-3 {{ old('mobile_phone', $student->mobile_phone ?? '') ? 'is-filled' : '' }}">
                                                    <label class="form-label">Mobile Phone</label>
                                                    <input type="text" name="mobile_phone" class="form-control"
                                                        value="{{ old('mobile_phone', $student->mobile_phone ?? '') }}"
                                                        maxlength="15">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div
                                                    class="input-group input-group-outline mb-3 {{ old('email', $student->email ?? '') ? 'is-filled' : '' }}">
                                                    <label class="form-label">Email Address</label>
                                                    <input type="email" name="email" class="form-control"
                                                        value="{{ old('email', $student->email ?? '') }}"
                                                        maxlength="100">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Address Information -->
                                <div class="card mb-4 shadow-sm">
                                    <div class="card-header bg-gradient-info">
                                        <h6 class="mb-0 d-flex align-items-center text-white">
                                            <i class="material-symbols-rounded me-2 icon-size-sm">location_on</i>
                                            Address Information
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div
                                                    class="input-group input-group-outline mb-3 {{ old('address_line1', $student->address_line1 ?? '') ? 'is-filled' : '' }}">
                                                    <label class="form-label">Address Line 1</label>
                                                    <input type="text" name="address_line1" class="form-control"
                                                        value="{{ old('address_line1', $student->address_line1 ?? '') }}"
                                                        maxlength="255">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div
                                                    class="input-group input-group-outline mb-3 {{ old('address_line2', $student->address_line2 ?? '') ? 'is-filled' : '' }}">
                                                    <label class="form-label">Address Line 2</label>
                                                    <input type="text" name="address_line2" class="form-control"
                                                        value="{{ old('address_line2', $student->address_line2 ?? '') }}"
                                                        maxlength="255">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div
                                                    class="input-group input-group-outline mb-3 {{ old('city', $student->city ?? '') ? 'is-filled' : '' }}">
                                                    <label class="form-label">City</label>
                                                    <input type="text" name="city" class="form-control"
                                                        value="{{ old('city', $student->city ?? '') }}" maxlength="100">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div
                                                    class="input-group input-group-outline mb-3 {{ old('state', $student->state ?? '') ? 'is-filled' : '' }}">
                                                    <label class="form-label">State/Province</label>
                                                    <input type="text" name="state" class="form-control"
                                                        value="{{ old('state', $student->state ?? '') }}"
                                                        maxlength="100">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div
                                                    class="input-group input-group-outline mb-3 {{ old('postal_code', $student->postal_code ?? '') ? 'is-filled' : '' }}">
                                                    <label class="form-label">Postal Code</label>
                                                    <input type="text" name="postal_code" class="form-control"
                                                        value="{{ old('postal_code', $student->postal_code ?? '') }}"
                                                        maxlength="20">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div
                                                    class="input-group input-group-outline mb-3 {{ old('country', $student->country ?? '') ? 'is-filled' : '' }}">
                                                    <label class="form-label">Country</label>
                                                    <input type="text" name="country" class="form-control"
                                                        value="{{ old('country', $student->country ?? '') }}"
                                                        maxlength="100">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Academic Information -->
                                <div class="card mb-4 shadow-sm">
                                    <div class="card-header bg-gradient-success">
                                        <h6 class="mb-0 d-flex align-items-center text-white">
                                            <i class="material-symbols-rounded me-2 icon-size-sm">school</i>
                                            Academic Information
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="input-group input-group-outline mb-3">
                                                    <select name="grade_level" class="form-control" required>
                                                        <option value="">Select Grade Level</option>
                                                        @for ($i = 1; $i <= 13; $i++)
                                                            <option value="{{ $i }}"
                                                                {{ old('grade_level', $student->grade_level ?? '') == $i ? 'selected' : '' }}>
                                                                Grade {{ $i }}
                                                            </option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="input-group input-group-outline mb-3">
                                                    <select name="class_id" class="form-control">
                                                        <option value="">Select Class</option>
                                                        @foreach ($classes as $class)
                                                            <option value="{{ $class->id }}"
                                                                {{ old('class_id', $student->class_id ?? '') == $class->id ? 'selected' : '' }}>
                                                                {{ $class->class_name }} (Grade {{ $class->grade_level }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div
                                                    class="input-group input-group-outline mb-3 {{ old('section', $student->section ?? '') ? 'is-filled' : '' }}">
                                                    <label class="form-label">Section</label>
                                                    <input type="text" name="section" class="form-control"
                                                        value="{{ old('section', $student->section ?? '') }}"
                                                        maxlength="10">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div
                                                    class="input-group input-group-outline mb-3 {{ old('enrollment_date', $student->enrollment_date ?? '') ? 'is-filled' : '' }}">
                                                    <label class="form-label">Enrollment Date *</label>
                                                    <input type="date" name="enrollment_date" class="form-control"
                                                        value="{{ old('enrollment_date', $student->enrollment_date ?? date('Y-m-d')) }}"
                                                        required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch pt-3">
                                                    <input class="form-check-input" type="checkbox" name="is_active"
                                                        value="1" id="isActiveSwitch"
                                                        {{ old('is_active', $student->is_active ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="isActiveSwitch">Active
                                                        Status</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- User Account Information -->
                                <div class="card mb-4 shadow-sm">
                                    <div class="card-header bg-gradient-warning">
                                        <h6 class="mb-0 d-flex align-items-center text-white">
                                            <i class="material-symbols-rounded me-2 icon-size-sm">account_circle</i>
                                            User Account Information
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @if (!$id)
                                                <div class="col-md-6">
                                                    <div class="input-group input-group-outline mb-3">
                                                        <label class="form-label">Password *</label>
                                                        <input type="password" name="password" class="form-control"
                                                            minlength="8" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="input-group input-group-outline mb-3">
                                                        <label class="form-label">Confirm Password *</label>
                                                        <input type="password" name="password_confirmation"
                                                            class="form-control" minlength="8" required>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="input-group input-group-outline mb-3">
                                                    <select name="roles[]" class="form-control" multiple>
                                                        @foreach ($roles as $role)
                                                            <option value="{{ $role->name }}"
                                                                {{ isset($student) && $student->user && $student->user->hasRole($role->name) ? 'selected' : ($role->name == 'student' ? 'selected' : '') }}>
                                                                {{ ucfirst($role->name) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple
                                                        roles</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Parent Information -->
                                @if (!$id)
                                    <div class="card mb-4 shadow-sm">
                                        <div class="card-header bg-gradient-secondary">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0 d-flex align-items-center text-white">
                                                    <i
                                                        class="material-symbols-rounded me-2 icon-size-sm">family_restroom</i>
                                                    Parent Information
                                                </h6>
                                                <button type="button" class="btn btn-sm btn-outline-light"
                                                    id="addParentBtn" onclick="addParentForm()">
                                                    <i class="material-symbols-rounded">add</i> Add Parent
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div id="parentContainer">
                                                <!-- Parent forms will be added here dynamically -->
                                            </div>
                                            <div class="text-center mt-3">
                                                <small class="text-muted">You can add multiple parents for this
                                                    student</small>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <!-- Existing Parents for Edit Mode -->
                                    <div class="card mb-4 shadow-sm">
                                        <div class="card-header bg-gradient-secondary">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0 d-flex align-items-center text-white">
                                                    <i
                                                        class="material-symbols-rounded me-2 icon-size-sm">family_restroom</i>
                                                    Parent Information
                                                </h6>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-sm btn-outline-light"
                                                        onclick="addParentForm()">
                                                        <i class="material-symbols-rounded">add</i> Add New Parent
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-light"
                                                        onclick="toggleParentSelector()">
                                                        <i class="material-symbols-rounded">link</i> Link Existing Parent
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <!-- Existing Parent Details Display -->
                                            @if (isset($student) && $student->parents && $student->parents->count() > 0)
                                                <div class="mb-4">
                                                    <h6 class="text-primary mb-3">
                                                        <i class="material-symbols-rounded me-2">people</i>
                                                        Current Parents ({{ $student->parents->count() }})
                                                    </h6>

                                                    @foreach ($student->parents as $index => $parent)
                                                        <div class="card border mb-3"
                                                            id="existingParent{{ $parent->parent_id }}">
                                                            <div class="card-header bg-light">
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center">
                                                                    <h6 class="mb-0">
                                                                        <span
                                                                            class="badge bg-primary me-2">{{ $parent->parent_code }}</span>
                                                                        {{ $parent->full_name }}
                                                                        <small
                                                                            class="text-muted">({{ ucfirst($parent->relationship_type) }})</small>
                                                                    </h6>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-outline-danger"
                                                                        onclick="unlinkParent({{ $parent->parent_id }})">
                                                                        <i class="material-symbols-rounded">link_off</i>
                                                                        Unlink
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="row g-3">
                                                                    <div class="col-md-3">
                                                                        <small class="text-muted">Name</small>
                                                                        <p class="mb-0 fw-medium">{{ $parent->full_name }}
                                                                        </p>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <small class="text-muted">Gender</small>
                                                                        <p class="mb-0">
                                                                            @if ($parent->gender == 'M')
                                                                                Male
                                                                            @elseif($parent->gender == 'F')
                                                                                Female
                                                                            @else
                                                                                Other
                                                                            @endif
                                                                        </p>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <small class="text-muted">Birth Date</small>
                                                                        <p class="mb-0">
                                                                            {{ $parent->date_of_birth ? $parent->date_of_birth->format('M d, Y') : 'N/A' }}
                                                                        </p>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <small class="text-muted">Relationship</small>
                                                                        <p class="mb-0">
                                                                            {{ ucfirst($parent->relationship_type) }}</p>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <small class="text-muted">Mobile Phone</small>
                                                                        <p class="mb-0">
                                                                            {{ $parent->mobile_phone ?? 'N/A' }}</p>
                                                                    </div>
                                                                </div>
                                                                <div class="row g-3 mt-2">
                                                                    <div class="col-md-4">
                                                                        <small class="text-muted">Email</small>
                                                                        <p class="mb-0">{{ $parent->email ?? 'N/A' }}
                                                                        </p>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <small class="text-muted">Occupation</small>
                                                                        <p class="mb-0">
                                                                            {{ $parent->occupation ?? 'N/A' }}</p>
                                                                    </div>
                                                                    <div class="col-md-3">
                                                                        <small class="text-muted">Workplace</small>
                                                                        <p class="mb-0">
                                                                            {{ $parent->workplace ?? 'N/A' }}</p>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <small class="text-muted">Emergency Contact</small>
                                                                        <p class="mb-0">
                                                                            @if ($parent->is_emergency_contact)
                                                                                <span class="badge bg-success">Yes</span>
                                                                            @else
                                                                                <span class="badge bg-secondary">No</span>
                                                                            @endif
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                                @if ($parent->address_line1)
                                                                    <div class="row g-3 mt-2">
                                                                        <div class="col-md-12">
                                                                            <small class="text-muted">Address</small>
                                                                            <p class="mb-0">{{ $parent->address_line1 }}
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                                <!-- Hidden input to maintain parent relationship -->
                                                                <input type="hidden" name="existing_parents[]"
                                                                    value="{{ $parent->parent_id }}">
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif

                                            <!-- Parent Selector (Hidden by default) -->
                                            <div id="parentSelector" style="display: none;">
                                                <div class="card border-dashed">
                                                    <div class="card-header bg-light">
                                                        <h6 class="mb-0">Link Existing Parent</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="input-group input-group-outline mb-3">
                                                                    <select name="parents[]" class="form-control"
                                                                        multiple>
                                                                        @foreach ($parents as $parent)
                                                                            <option value="{{ $parent->parent_id }}"
                                                                                {{ isset($student) && $student->parents->contains('parent_id', $parent->parent_id) ? 'selected' : '' }}>
                                                                                {{ $parent->full_name }}
                                                                                ({{ $parent->parent_code }})
                                                                                -
                                                                                {{ ucfirst($parent->relationship_type) }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <small class="form-text text-muted">Hold Ctrl/Cmd to select
                                                                    multiple parents</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- New Parent Forms Container -->
                                            <div id="parentContainer">
                                                <!-- New parent forms will be added here dynamically -->
                                            </div>

                                            <div class="text-center mt-3">
                                                <small class="text-muted">You can add new parents or link existing parents
                                                    to this student</small>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Submit Buttons -->
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <div class="col-12 text-end">
                                            <a href="{{ route('admin.management.students.index') }}"
                                                class="btn btn-outline-secondary me-2">
                                                <i class="material-symbols-rounded me-1">cancel</i>Cancel
                                            </a>
                                            <button type="button" class="btn btn-outline-warning me-2"
                                                onclick="document.getElementById('studentForm').reset(); resetForm();">
                                                <i class="material-symbols-rounded me-1">restart_alt</i>Reset
                                            </button>
                                            <button type="submit" class="btn btn-success">
                                                <i class="material-symbols-rounded me-1">save</i>
                                                {{ $id ? 'Update' : 'Create' }} Student
                                            </button>
                                        </div>
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
<<<<<<< HEAD
        // Global variables
        let parentCount = 0;

        // Global function declarations - accessible from onclick handlers
        window.addParentForm = function() {
            parentCount++;
            const container = document.getElementById('parentContainer');

            if (!container) {
                console.error('Parent container not found');
                alert('Error: Parent container not found');
                return;
            }

            const parentForm = `
                <div class="card border mb-3 parent-form-border" id="parentForm${parentCount}">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">New Parent ${parentCount}</h6>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeParentForm(${parentCount})">
                                <i class="material-symbols-rounded">delete</i> Remove
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="input-group input-group-outline mb-3">
                                    <label class="form-label">First Name *</label>
                                    <input type="text" name="parent_first_name[]" class="form-control" required maxlength="50">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-outline mb-3">
                                    <label class="form-label">Middle Name</label>
                                    <input type="text" name="parent_middle_name[]" class="form-control" maxlength="50">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-outline mb-3">
                                    <label class="form-label">Last Name *</label>
                                    <input type="text" name="parent_last_name[]" class="form-control" required maxlength="50">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-outline mb-3">
                                    <select name="parent_gender[]" class="form-control" required>
                                        <option value="">Select Gender</option>
                                        <option value="M">Male</option>
                                        <option value="F">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="input-group input-group-outline mb-3">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date" name="parent_date_of_birth[]" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-outline mb-3">
                                    <select name="parent_relationship_type[]" class="form-control" required>
                                        <option value="">Select Relationship</option>
                                        <option value="Father">Father</option>
                                        <option value="Mother">Mother</option>
                                        <option value="Guardian">Guardian</option>
                                        <option value="Stepfather">Stepfather</option>
                                        <option value="Stepmother">Stepmother</option>
                                        <option value="Grandfather">Grandfather</option>
                                        <option value="Grandmother">Grandmother</option>
                                        <option value="Uncle">Uncle</option>
                                        <option value="Aunt">Aunt</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-outline mb-3">
                                    <label class="form-label">Mobile Phone *</label>
                                    <input type="text" name="parent_mobile_phone[]" class="form-control" required maxlength="15">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-outline mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="parent_email[]" class="form-control" maxlength="100">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="input-group input-group-outline mb-3">
                                    <label class="form-label">Occupation</label>
                                    <input type="text" name="parent_occupation[]" class="form-control" maxlength="100">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-outline mb-3">
                                    <label class="form-label">Workplace</label>
                                    <input type="text" name="parent_workplace[]" class="form-control" maxlength="100">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-outline mb-3">
                                    <label class="form-label">Work Phone</label>
                                    <input type="text" name="parent_work_phone[]" class="form-control" maxlength="15">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check form-switch pt-3">
                                    <input class="form-check-input" type="checkbox" name="parent_is_emergency_contact[]" value="1">
                                    <label class="form-check-label">Emergency Contact</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="input-group input-group-outline mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea name="parent_address_line1[]" class="form-control" rows="2" maxlength="255"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', parentForm);

            // Add animation to newly added form
            const newForm = document.getElementById(`parentForm${parentCount}`);
            if (newForm) {
                newForm.style.opacity = '0';
                newForm.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    newForm.style.transition = 'all 0.3s ease';
                    newForm.style.opacity = '1';
                    newForm.style.transform = 'translateY(0)';
                }, 10);
            }
        };

        window.removeParentForm = function(parentId) {
            const parentForm = document.getElementById(`parentForm${parentId}`);
            if (!parentForm) {
                console.error('Parent form not found for removal');
                return;
            }

            // Add fade out animation
            parentForm.style.transition = 'all 0.3s ease';
            parentForm.style.opacity = '0';
            parentForm.style.transform = 'translateY(-20px)';

            setTimeout(() => {
                parentForm.remove();
            }, 300);
        };

        window.unlinkParent = function(parentId) {
            if (confirm('Are you sure you want to unlink this parent from the student?')) {
                const parentElement = document.getElementById(`existingParent${parentId}`);
                if (parentElement) {
                    // Add fade out animation
                    parentElement.style.transition = 'all 0.3s ease';
                    parentElement.style.opacity = '0';
                    parentElement.style.transform = 'translateY(-20px)';

                    setTimeout(() => {
                        parentElement.remove();

                        // Remove from existing_parents input array
                        const existingParentsInputs = document.querySelectorAll(
                            'input[name="existing_parents[]"]');
                        existingParentsInputs.forEach(input => {
                            if (input.value == parentId) {
                                input.remove();
                            }
                        });
                    }, 300);
                }
            }
        };

        window.toggleParentSelector = function() {
            const selector = document.getElementById('parentSelector');
            if (selector) {
                if (selector.style.display === 'none') {
                    selector.style.display = 'block';
                    selector.style.opacity = '0';
                    setTimeout(() => {
                        selector.style.transition = 'all 0.3s ease';
                        selector.style.opacity = '1';
                    }, 10);
                } else {
                    selector.style.transition = 'all 0.3s ease';
                    selector.style.opacity = '0';
                    setTimeout(() => {
                        selector.style.display = 'none';
                    }, 300);
                }
            }
        };

        window.generateStudentCode = function() {
            const studentCodeInput = document.querySelector('input[name="student_code"]');

            fetch('{{ route('admin.management.students.generate-code') }}')
                .then(response => response.json())
                .then(data => {
                    if (data.code) {
                        studentCodeInput.value = data.code;
                        // Mark field as filled for Material Design
                        studentCodeInput.closest('.input-group').classList.add('is-filled');
                    }
                })
                .catch(error => {
                    console.error('Error fetching code:', error);
                    // Fallback: generate a temporary code
                    const timestamp = Date.now();
                    studentCodeInput.value = 'stu-' + String(timestamp).slice(-8).padStart(8, '0');
                    studentCodeInput.closest('.input-group').classList.add('is-filled');
                });
        };

        // DOM Content Loaded event
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-generate student code if creating new student
            const studentCodeInput = document.querySelector('input[name="student_code"]');
            const isEditMode = {{ $id ? 'true' : 'false' }};

            if (!isEditMode && !studentCodeInput.value) {
                // Generate student code
                window.generateStudentCode();
            }

            // Add first parent form by default for new students
            if (!isEditMode) {
                window.addParentForm();
            }
        });

        window.resetForm = function() {
            // Reset parent forms
            document.getElementById('parentContainer').innerHTML = '';
            parentCount = 0;

            // Add one parent form back
            const isEditMode = {{ $id ? 'true' : 'false' }};
            if (!isEditMode) {
                window.addParentForm();
                window.generateStudentCode();
            }

            // Reset Material Design form states
            document.querySelectorAll('.input-group-outline').forEach(group => {
                group.classList.remove('is-filled', 'is-focused');
            });
        };

        // Material Design form field handlers
        document.addEventListener('focus', function(e) {
            if (e.target.matches('.form-control')) {
                e.target.closest('.input-group-outline')?.classList.add('is-focused');
            }
        }, true);

        document.addEventListener('blur', function(e) {
            if (e.target.matches('.form-control')) {
                const group = e.target.closest('.input-group-outline');
                if (group) {
                    group.classList.remove('is-focused');
                    if (e.target.value) {
                        group.classList.add('is-filled');
                    } else {
                        group.classList.remove('is-filled');
                    }
                }
            }
        }, true);

        // Profile Image Preview
        document.getElementById('profileImage').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('profilePreview');
                    preview.innerHTML =
                        `<img src="${e.target.result}" alt="Student Photo" class="w-100 border-radius-lg shadow-sm" style="height: 120px; object-fit: cover;">`;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
=======
        // Set blade template variables for JavaScript
        window.isEditMode = {{ $id ? 'true' : 'false' }};
        window.generateCodeUrl = '{{ route('admin.management.students.generate-code') }}';
    </script>
    @vite('resources/js/admin/student-form.js')
>>>>>>> 4358fa2a22b070c3f048b27b38865b1db4389606
@endsection
