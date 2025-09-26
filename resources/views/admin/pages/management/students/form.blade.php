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
                                        <i
                                            class="material-symbols-rounded me-1 icon-size-md">arrow_back</i>{{ __('common.back') }}
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
                                            {{ __('school.student_information') }}
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
                                                    <small
                                                        class="text-muted">{{ __('common.click_edit_icon_upload_photo') }}</small>
                                                </div>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <x-input name="student_code" title="{{ __('school.student_code') }}"
                                                            :isRequired="true"
                                                            attr="maxlength='50' readonly style='background-color: #f8f9fa; cursor: not-allowed;'"
                                                            :value="old(
                                                                'student_code',
                                                                $student->student_code ?? '',
                                                            )" />
                                                        @if (!$id)
                                                            <small
                                                                class="form-text text-muted">{{ __('common.auto_generated') }}</small>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-4">
                                                        <x-input name="first_name" title="First Name" :isRequired="true"
                                                            attr="maxlength='50'" :value="old('first_name', $student->first_name ?? '')" />
                                                    </div>
                                                    <div class="col-md-4">
                                                        <x-input name="middle_name" title="Middle Name"
                                                            attr="maxlength='50'" :value="old('middle_name', $student->middle_name ?? '')" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-3">
                                                <x-input name="last_name" title="Last Name" :isRequired="true"
                                                    attr="maxlength='50'" :value="old('last_name', $student->last_name ?? '')" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <x-input name="date_of_birth" type="date" title="Date of Birth"
                                                    :isRequired="true" :value="old('date_of_birth', $student->date_of_birth ?? '')" />
                                            </div>
                                            <div class="col-md-3">
                                                <x-input name="gender" type="select" title="Gender" :isRequired="true"
                                                    placeholder="Select Gender" :options="['M' => 'Male', 'F' => 'Female', 'Other' => 'Other']" :value="old('gender', $student->gender ?? '')" />
                                            </div>
                                            <div class="col-md-3">
                                                <x-input name="nationality" title="Nationality" attr="maxlength='50'"
                                                    :value="old('nationality', $student->nationality ?? '')" />
                                            </div>
                                            <div class="col-md-3">
                                                <x-input name="religion" title="Religion" attr="maxlength='50'"
                                                    :value="old('religion', $student->religion ?? '')" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <x-input name="home_language" title="Home Language" attr="maxlength='50'"
                                                    :value="old('home_language', $student->home_language ?? '')" />
                                            </div>
                                            <div class="col-md-4">
                                                <x-input name="mobile_phone" title="Mobile Phone" attr="maxlength='15'"
                                                    :value="old('mobile_phone', $student->mobile_phone ?? '')" />
                                            </div>
                                            <div class="col-md-4">
                                                <x-input name="email" type="email" title="Email Address"
                                                    attr="maxlength='100'" :value="old('email', $student->email ?? '')" />
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
                                                <x-input name="address_line1" title="Address Line 1"
                                                    attr="maxlength='255'" :value="old('address_line1', $student->address_line1 ?? '')" />
                                            </div>
                                            <div class="col-md-6">
                                                <x-input name="address_line2" title="Address Line 2"
                                                    attr="maxlength='255'" :value="old('address_line2', $student->address_line2 ?? '')" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <x-input name="city" title="City" attr="maxlength='100'"
                                                    :value="old('city', $student->city ?? '')" />
                                            </div>
                                            <div class="col-md-3">
                                                <x-input name="state" title="State/Province" attr="maxlength='100'"
                                                    :value="old('state', $student->state ?? '')" />
                                            </div>
                                            <div class="col-md-3">
                                                <x-input name="postal_code" title="Postal Code" attr="maxlength='20'"
                                                    :value="old('postal_code', $student->postal_code ?? '')" />
                                            </div>
                                            <div class="col-md-3">
                                                <x-input name="country" title="Country" attr="maxlength='100'"
                                                    :value="old('country', $student->country ?? '')" />
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
                                                <x-input name="grade_level" type="select" title="Grade Level"
                                                    :isRequired="true" placeholder="Select Grade Level" :options="[
                                                        '1' => 'Grade 1',
                                                        '2' => 'Grade 2',
                                                        '3' => 'Grade 3',
                                                        '4' => 'Grade 4',
                                                        '5' => 'Grade 5',
                                                        '6' => 'Grade 6',
                                                        '7' => 'Grade 7',
                                                        '8' => 'Grade 8',
                                                        '9' => 'Grade 9',
                                                        '10' => 'Grade 10',
                                                        '11' => 'Grade 11',
                                                        '12' => 'Grade 12',
                                                        '13' => 'Grade 13',
                                                    ]"
                                                    :value="old('grade_level', $student->grade_level ?? '')" />
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
                                                <x-input name="section" title="Section" attr="maxlength='10'"
                                                    :value="old('section', $student->section ?? '')" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <x-input name="enrollment_date" type="date" title="Enrollment Date"
                                                    :isRequired="true" :value="old(
                                                        'enrollment_date',
                                                        $student->enrollment_date ?? date('Y-m-d'),
                                                    )" />
                                            </div>
                                            <div class="col-md-6">
                                                <x-input name="is_active" type="checkbox" title="Active Status"
                                                    :value="old('is_active', $student->is_active ?? true)
                                                        ? '1'
                                                        : '0'" attr="id='isActiveSwitch'" />
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
                                                    <x-input name="password" type="password" title="Password"
                                                        :isRequired="true" attr="minlength='8'"
                                                        placeholder="Enter password (min 8 characters)" />
                                                </div>
                                                <div class="col-md-6">
                                                    <x-input name="password_confirmation" type="password"
                                                        title="Confirm Password" :isRequired="true" attr="minlength='8'"
                                                        placeholder="Confirm your password" />
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
        // Set blade template variables for JavaScript
        window.isEditMode = {{ $id ? 'true' : 'false' }};
        window.generateCodeUrl = '{{ route('admin.management.students.generate-code') }}';
    </script>
    @vite('resources/js/admin/student-form.js')
@endsection
