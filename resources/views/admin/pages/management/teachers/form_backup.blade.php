@extends('admin.layouts.app')

@section('title', pageTitle())

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
                                        href="{{ route('admin.management.teachers.index') }}">
                                        <i class="material-symbols-rounded me-1 icon-size-md">arrow_back</i>Back
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.management.teachers.enroll') }}" method="POST" id="teacherForm"
                                enctype="multipart/form-data">
                                @csrf
                                @if ($id)
                                    <input type="hidden" name="id" value="{{ $id }}">
                                @endif

                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0 d-flex align-items-center">
                                            <i class="material-symbols-rounded me-2 icon-size-sm">person</i>
                                            Personal Information
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-4">
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <div class="avatar avatar-xl position-relative mb-3">
                                                        @if (isset($teacher) && $teacher->photo_path)
                                                            <img id="profilePreview"
                                                                src="{{ asset('storage/' . $teacher->photo_path) }}"
                                                                alt="Teacher Photo"
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
                                                        <div class="input-group input-group-outline mb-3">
                                                            <label class="form-label">Teacher Code *</label>
                                                            <input type="text" name="teacher_code" class="form-control"
                                                                value="{{ old('teacher_code', $teacher->teacher_code ?? '') }}"
                                                                maxlength="50" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="input-group input-group-outline mb-3">
                                                            <label class="form-label">First Name *</label>
                                                            <input type="text" name="first_name" class="form-control"
                                                                value="{{ old('first_name', $teacher->first_name ?? '') }}"
                                                                maxlength="100" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="input-group input-group-outline mb-3">
                                                            <label class="form-label">Last Name *</label>
                                                            <input type="text" name="last_name" class="form-control"
                                                                value="{{ old('last_name', $teacher->last_name ?? '') }}"
                                                                maxlength="100" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="input-group input-group-outline mb-3">
                                                    <label class="form-label">Date of Birth</label>
                                                    <input type="date" name="date_of_birth" class="form-control"
                                                        value="{{ old('date_of_birth', $teacher->date_of_birth ?? '') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="input-group input-group-outline mb-3">
                                                    <select name="gender" class="form-control">
                                                        <option value="">Select Gender</option>
                                                        <option value="Male"
                                                            {{ old('gender', $teacher->gender ?? '') == 'Male' ? 'selected' : '' }}>
                                                            Male</option>
                                                        <option value="Female"
                                                            {{ old('gender', $teacher->gender ?? '') == 'Female' ? 'selected' : '' }}>
                                                            Female</option>
                                                        <option value="Other"
                                                            {{ old('gender', $teacher->gender ?? '') == 'Other' ? 'selected' : '' }}>
                                                            Other</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="input-group input-group-outline mb-3">
                                                    <label class="form-label">NIC Number</label>
                                                    <input type="text" name="nic_number" class="form-control"
                                                        value="{{ old('nic_number', $teacher->nic_number ?? '') }}"
                                                        maxlength="20">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-3">
                                                    <label class="form-label">Phone Number</label>
                                                    <input type="text" name="phone" class="form-control"
                                                        value="{{ old('phone', $teacher->phone ?? '') }}" maxlength="15">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-3">
                                                    <label class="form-label">Address</label>
                                                    <textarea name="address" class="form-control" rows="3">{{ old('address', $teacher->address ?? '') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0 d-flex align-items-center">
                                            <i class="material-symbols-rounded me-2 icon-size-sm">school</i>
                                            Professional Information
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="input-group input-group-outline mb-3">
                                                    <label class="form-label">Specialization</label>
                                                    <input type="text" name="specialization" class="form-control"
                                                        value="{{ old('specialization', $teacher->specialization ?? '') }}"
                                                        maxlength="255">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="input-group input-group-outline mb-3">
                                                    <label class="form-label">Experience (Years)</label>
                                                    <input type="number" name="experience_years" class="form-control"
                                                        min="0" max="50"
                                                        value="{{ old('experience_years', $teacher->experience_years ?? '') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="input-group input-group-outline mb-3">
                                                    <label class="form-label">Hire Date</label>
                                                    <input type="date" name="hire_date" class="form-control"
                                                        value="{{ old('hire_date', $teacher->hire_date ?? '') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="is_class_teacher" value="1"
                                                        {{ old('is_class_teacher', $teacher->is_class_teacher ?? false) ? 'checked' : '' }}>
                                                    <label class="form-check-label">Is Class Teacher</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label class="form-label">Qualifications</label>
                                                <textarea name="qualifications" class="form-control" rows="3"
                                                    placeholder="Enter qualifications and certifications">{{ old('qualifications', $teacher->qualifications ?? '') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0 d-flex align-items-center">
                                            <i class="material-symbols-rounded me-2 icon-size-sm">subject</i>
                                            Subject Assignment
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach ($subjects as $subject)
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="subjects[]"
                                                            value="{{ $subject->subject_id }}"
                                                            id="subject_{{ $subject->subject_id }}"
                                                            {{ in_array($subject->subject_id, old('subjects', $teacherSubjects ?? [])) ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="subject_{{ $subject->subject_id }}">
                                                            <strong>{{ $subject->subject_name }}</strong>
                                                            <br><small class="text-secondary">{{ $subject->category }} -
                                                                {{ $subject->subject_code }}</small>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0 d-flex align-items-center">
                                            <i class="material-symbols-rounded me-2 icon-size-sm">account_circle</i>
                                            User Account Information
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-group input-group-outline mb-3">
                                                    <label class="form-label">Email Address</label>
                                                    <input type="email" name="email" class="form-control"
                                                        value="{{ old('email', $teacher->user->email ?? '') }}">
                                                </div>
                                            </div>
                                            @if (!$id)
                                                <div class="col-md-6">
                                                    <div class="input-group input-group-outline mb-3">
                                                        <label class="form-label">Password</label>
                                                        <input type="password" name="password" class="form-control"
                                                            minlength="8">
                                                        <small class="form-text text-muted">Leave empty to auto-generate
                                                            password</small>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="is_active"
                                                        value="1"
                                                        {{ old('is_active', $teacher->is_active ?? true) ? 'checked' : '' }}>
                                                    <label class="form-check-label">Active Status</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                        <div class="col-12 text-end">
                                            <a href="{{ route('admin.management.teachers.index') }}"
                                                class="btn btn-outline-secondary">Cancel</a>
                                            <button type="button" class="btn btn-outline-danger"
                                                onclick="document.getElementById('teacherForm').reset()">Reset</button>
                                            <button type="submit" class="btn btn-success">Submit</button>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-generate teacher code if empty
            const teacherCodeInput = document.querySelector('input[name="teacher_code"]');
            if (!teacherCodeInput.value) {
                const currentYear = new Date().getFullYear();
                const randomNum = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
                teacherCodeInput.value = `TEA${currentYear}${randomNum}`;
            }

            // Profile image preview
            const profileInput = document.getElementById('profileImage');
            const profilePreview = document.getElementById('profilePreview');

            profileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Validate file type
                    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                    if (!validTypes.includes(file.type)) {
                        alert('Please select a valid image file (JPEG, PNG, JPG, GIF)');
                        e.target.value = '';
                        return;
                    }

                    // Validate file size (2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        alert('Image size should not exceed 2MB');
                        e.target.value = '';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        profilePreview.innerHTML =
                            `<img src="${e.target.result}" alt="Profile Preview" class="w-100 border-radius-lg shadow-sm">`;
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
@endsection
