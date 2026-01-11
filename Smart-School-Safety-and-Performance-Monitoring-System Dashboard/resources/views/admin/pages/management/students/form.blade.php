@extends('admin.layouts.app')

@section('title', pageTitle())

@section('css')
    @vite(['resources/css/admin/forms.css', 'resources/css/admin/common-forms.css', 'resources/css/components/utilities.css'])
    <style>
        /* Face Capture Styles */
        .face-capture-container {
            position: relative;
            width: 100%;
            max-width: 480px;
            margin: 0 auto;
        }

        .face-capture-video {
            width: 100%;
            height: 360px;
            border-radius: 12px;
            background: #1a1a2e;
            object-fit: cover;
            transform: scaleX(-1);
            /* Mirror the camera */
        }

        .face-capture-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 200px;
            height: 250px;
            border: 3px dashed rgba(76, 175, 80, 0.8);
            border-radius: 50% 50% 45% 45%;
            pointer-events: none;
        }

        .face-capture-overlay.detecting {
            border-color: #4CAF50;
            animation: pulse-border 1.5s infinite;
        }

        .face-capture-overlay.no-face {
            border-color: #f44336;
        }

        @keyframes pulse-border {

            0%,
            100% {
                opacity: 0.6;
                transform: translate(-50%, -50%) scale(1);
            }

            50% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1.02);
            }
        }

        .face-thumbnails {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            max-height: 150px;
            overflow-y: auto;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .face-thumbnail {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            border: 2px solid #4CAF50;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .face-thumbnail:hover {
            transform: scale(1.1);
        }

        .face-progress-bar {
            height: 8px;
            border-radius: 4px;
            background: #e0e0e0;
            overflow: hidden;
        }

        .face-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #4CAF50, #8BC34A);
            transition: width 0.3s ease;
        }

        .face-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
        }

        .face-status-ready {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .face-status-capturing {
            background: #fff3e0;
            color: #e65100;
        }

        .face-status-complete {
            background: #e3f2fd;
            color: #1565c0;
        }

        .face-status-error {
            background: #ffebee;
            color: #c62828;
        }

        .capture-count {
            font-size: 28px;
            font-weight: bold;
            color: #4CAF50;
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
                                    <a class="btn btn-outline-dark mb-0 btn-back-auto"
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
                                                    <div class="avatar avatar-xl position-relative mb-1">
                                                        @if (isset($student) && $student->photo_path)
                                                            <img id="profilePreview"
                                                                src="{{ asset('storage/' . $student->photo_path) }}"
                                                                alt="Student Photo"
                                                                class="w-100 h-100 border-radius-lg shadow-sm object-fit-cover"
                                                                style="border-radius: 50%;">
                                                        @else
                                                            <div id="profilePreview"
                                                                class="w-100 h-100 border-radius-lg shadow-sm bg-gradient-primary d-flex align-items-center justify-content-center"
                                                                style="border-radius: 50%;">
                                                                <i
                                                                    class="material-symbols-rounded text-white text-lg">person</i>
                                                            </div>
                                                        @endif
                                                        <label for="profileImage"
                                                            class="btn btn-sm btn-icon-only bg-gradient-light position-absolute bottom-20 end-0 mb-n2 me-n2 cursor-pointer">
                                                            <i class="material-symbols-rounded text-xs">edit</i>
                                                        </label>
                                                        <input type="file" id="profileImage" name="profile_image"
                                                            accept="image/*" style="display: none;">
                                                    </div>
                                                    <small class="text-muted" style="margin-left: 8px">Click the edit icon
                                                        to upload a photo</small>
                                                </div>
                                            </div>
                                            <div class="col-md-9">
                                                <!-- 4-column layout for first row -->
                                                <div class="row">
                                                    <div class="col-md-3">
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
                                                    <div class="col-md-3">
                                                        <x-input name="first_name" title="First Name" :isRequired="true"
                                                            attr="maxlength='50'" :value="old('first_name', $student->first_name ?? '')" />
                                                    </div>
                                                    <div class="col-md-3">
                                                        <x-input name="middle_name" title="Middle Name"
                                                            attr="maxlength='50'" :value="old('middle_name', $student->middle_name ?? '')" />
                                                    </div>
                                                    <div class="col-md-3">
                                                        <x-input name="last_name" title="Last Name" :isRequired="true"
                                                            attr="maxlength='50'" :value="old('last_name', $student->last_name ?? '')" />
                                                    </div>
                                                </div>

                                                <!--  4-column layout for second row -->
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <x-input name="date_of_birth" type="date" title="Date of Birth"
                                                            :isRequired="true" :value="old(
                                                                'date_of_birth',
                                                                $student?->date_of_birth?->format('Y-m-d') ?: '',
                                                            )" />
                                                    </div>
                                                    <div class="col-md-3">
                                                        <x-input name="gender" type="select" title="Gender"
                                                            :isRequired="true" placeholder="Select Gender" :options="[
                                                                'M' => 'Male',
                                                                'F' => 'Female',
                                                                'Other' => 'Other',
                                                            ]"
                                                            :value="old('gender', $student->gender ?? '')" />
                                                    </div>
                                                    <div class="col-md-3">
                                                        <x-input name="nationality" title="Nationality"
                                                            attr="maxlength='50'" :value="old('nationality', $student->nationality ?? '')" />
                                                    </div>
                                                    <div class="col-md-3">
                                                        <x-input name="religion" title="Religion" attr="maxlength='50'"
                                                            :value="old('religion', $student->religion ?? '')" />
                                                    </div>
                                                </div>

                                                <!-- Updated: 4-column layout for third row  -->
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <x-input name="home_language" title="Home Language"
                                                            attr="maxlength='50'" :value="old(
                                                                'home_language',
                                                                $student->home_language ?? '',
                                                            )" />
                                                    </div>
                                                    <div class="col-md-3">
                                                        <x-input name="mobile_phone" title="Mobile Phone"
                                                            attr="maxlength='15'" :value="old(
                                                                'mobile_phone',
                                                                $student->mobile_phone ?? '',
                                                            )" />
                                                    </div>
                                                    <div class="col-md-3">
                                                        <x-input name="email" type="email" title="Email Address"
                                                            attr="maxlength='100'" :value="old('email', $student->email ?? '')" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Face Recognition Training (Capture 20+ Images) -->
                                <div class="card mb-4 shadow-sm">
                                    <div class="card-header bg-gradient-dark">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 d-flex align-items-center text-white">
                                                <i class="material-symbols-rounded me-2 icon-size-sm">face</i>
                                                Face Recognition Training
                                            </h6>
                                            <span id="faceStatusBadge" class="face-status-badge face-status-ready">
                                                <i class="material-symbols-rounded"
                                                    style="font-size: 16px;">camera_front</i>
                                                <span id="faceStatusText">Ready to Capture</span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="face-capture-container">
                                                    <video id="faceCaptureVideo" class="face-capture-video" autoplay muted
                                                        playsinline></video>
                                                    <div id="faceOverlay" class="face-capture-overlay"></div>
                                                </div>
                                                <div class="mt-3 d-flex justify-content-center gap-2">
                                                    <button type="button" id="startFaceCaptureBtn"
                                                        class="btn btn-success">
                                                        <i class="material-symbols-rounded me-1">videocam</i>
                                                        Start Camera
                                                    </button>
                                                    <button type="button" id="stopFaceCaptureBtn" class="btn btn-danger"
                                                        disabled>
                                                        <i class="material-symbols-rounded me-1">videocam_off</i>
                                                        Stop Camera
                                                    </button>
                                                    <button type="button" id="autoCaptureFaceBtn"
                                                        class="btn btn-primary" disabled>
                                                        <i class="material-symbols-rounded me-1">auto_awesome</i>
                                                        Auto Capture (30)
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex flex-column h-100">
                                                    <div class="mb-3">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-2">
                                                            <span class="text-muted">Captured Images</span>
                                                            <span><span id="captureCount" class="capture-count">0</span> /
                                                                30</span>
                                                        </div>
                                                        <div class="face-progress-bar">
                                                            <div id="faceProgressFill" class="face-progress-fill"
                                                                style="width: 0%"></div>
                                                        </div>
                                                    </div>

                                                    <div class="alert alert-info py-2 mb-3" style="font-size: 13px;">
                                                        <i class="material-symbols-rounded me-1"
                                                            style="font-size: 16px; vertical-align: middle;">info</i>
                                                        <strong>Tips for better accuracy:</strong>
                                                        <ul class="mb-0 mt-1 ps-3">
                                                            <li>Look straight at the camera</li>
                                                            <li>Turn your head slightly left and right</li>
                                                            <li>Ensure good lighting</li>
                                                            <li>Remove glasses if possible</li>
                                                        </ul>
                                                    </div>

                                                    <label class="form-label fw-bold">Captured Face Images</label>
                                                    <div id="faceThumbnails" class="face-thumbnails flex-grow-1">
                                                        <div class="text-muted text-center w-100 py-4">
                                                            <i class="material-symbols-rounded"
                                                                style="font-size: 32px;">add_a_photo</i>
                                                            <p class="mb-0 mt-2" style="font-size: 12px;">No face images
                                                                captured yet</p>
                                                        </div>
                                                    </div>

                                                    <div class="mt-3 d-flex gap-2">
                                                        <button type="button" id="clearFaceImagesBtn"
                                                            class="btn btn-outline-danger btn-sm" disabled>
                                                            <i class="material-symbols-rounded me-1">delete</i>
                                                            Clear All
                                                        </button>
                                                        <button type="button" id="trainFaceModelBtn"
                                                            class="btn btn-warning btn-sm" disabled>
                                                            <i class="material-symbols-rounded me-1">model_training</i>
                                                            Train Model Now
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Hidden input to store face data status -->
                                        <input type="hidden" name="face_images_captured" id="faceImagesCaptured"
                                            value="0">
                                        <input type="hidden" name="face_data_json" id="faceDataJson" value="">
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
                                                <x-input name="grade_level" type="select" id="grade_level"
                                                    title="Grade Level" :isRequired="true"
                                                    placeholder="Select Grade Level" :options="$grades"
                                                    :value="old('grade_level', $student->grade_level ?? '')" />
                                            </div>

                                            <div class="col-md-4">
                                                <x-input name="class_id" type="select" id="class_id" title="Class"
                                                    :isRequired="true" placeholder="Select Class" :options="$formattedClasses"
                                                    :value="old('class_id', $student?->class_id ?? '')"
                                                    data-initial-class="{{ old('class_id', $student?->class_id ?? '') }}" />
                                                <small class="text-muted">Select the class for this student</small>
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
                                                        $student?->enrollment_date?->format('Y-m-d') ?: date('Y-m-d'),
                                                    )" />
                                            </div>

                                            <div class="col-md-6">
                                                <x-input name="is_active" type="select" title="Active Status"
                                                    :isRequired="true" :options="['1' => 'Yes', '0' => 'No']" :value="old('is_active', $student->is_active ?? '1')" />
                                            </div>
                                        </div>

                                        <!-- Subject Selection (Dynamic based on grade) -->
                                        <div class="row" id="subjectSelectionContainer" style="display: none;">
                                            <div class="col-md-12">
                                                <div class="alert alert-info mb-3" id="subjectSelectionInfo">
                                                    <i class="material-symbols-rounded me-2">info</i>
                                                    <span id="educationLevelText"></span>
                                                </div>

                                                <!-- Primary Education (Grades 1-5) -->
                                                <div id="primarySubjects" style="display: none;">
                                                    <h6 class="text-primary mb-3">Primary Education Subject Selection</h6>

                                                    <!-- First Language (Required - Choose 1) -->
                                                    <div class="mb-4">
                                                        <label class="form-label fw-bold">First Language <span
                                                                class="text-danger">*</span> (Choose 1)</label>
                                                        <div id="firstLanguagePrimary" class="row g-2"></div>
                                                        <small class="text-danger d-none"
                                                            id="firstLanguagePrimaryError">Please select one first
                                                            language</small>
                                                    </div>

                                                    <!-- Religion (Required - Choose 1) -->
                                                    <div class="mb-4">
                                                        <label class="form-label fw-bold">Religion <span
                                                                class="text-danger">*</span> (Choose 1)</label>
                                                        <div id="religionPrimary" class="row g-2"></div>
                                                        <small class="text-danger d-none" id="religionPrimaryError">Please
                                                            select one religion</small>
                                                    </div>

                                                    <!-- Aesthetic Studies (Required - Choose 1) -->
                                                    <div class="mb-4">
                                                        <label class="form-label fw-bold">Aesthetic Studies <span
                                                                class="text-danger">*</span> (Choose 1)</label>
                                                        <div id="aestheticPrimary" class="row g-2"></div>
                                                        <small class="text-danger d-none"
                                                            id="aestheticPrimaryError">Please select one aesthetic
                                                            study</small>
                                                    </div>

                                                    <!-- Core Subjects (Auto-assigned) -->
                                                    <div class="mb-4">
                                                        <label class="form-label fw-bold">Core Subjects
                                                            (Auto-assigned)</label>
                                                        <div id="corePrimary" class="alert alert-success">
                                                            <ul id="corePrimaryList" class="mb-0"></ul>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Secondary Education (Grades 6-11) -->
                                                <div id="secondarySubjects" style="display: none;">
                                                    <h6 class="text-primary mb-3">Secondary Education Subject Selection
                                                    </h6>

                                                    <!-- First Language (Required - Choose 1) -->
                                                    <div class="mb-4">
                                                        <label class="form-label fw-bold">First Language <span
                                                                class="text-danger">*</span> (Choose 1)</label>
                                                        <div id="firstLanguageSecondary" class="row g-2"></div>
                                                        <small class="text-danger d-none"
                                                            id="firstLanguageSecondaryError">Please select one first
                                                            language</small>
                                                    </div>

                                                    <!-- Religion (Required - Choose 1) -->
                                                    <div class="mb-4">
                                                        <label class="form-label fw-bold">Religion <span
                                                                class="text-danger">*</span> (Choose 1)</label>
                                                        <div id="religionSecondary" class="row g-2"></div>
                                                        <small class="text-danger d-none"
                                                            id="religionSecondaryError">Please select one religion</small>
                                                    </div>

                                                    <!-- Core Subjects (Auto-assigned) -->
                                                    <div class="mb-4">
                                                        <label class="form-label fw-bold">Core Subjects
                                                            (Auto-assigned)</label>
                                                        <div id="coreSecondary" class="alert alert-success">
                                                            <ul id="coreSecondaryList" class="mb-0"></ul>
                                                        </div>
                                                    </div>

                                                    <!-- Elective Subjects (Choose 3) -->
                                                    <div class="mb-4">
                                                        <label class="form-label fw-bold">Elective Subjects <span
                                                                class="text-danger">*</span> (Choose exactly 3)</label>
                                                        <div id="electiveSecondary" class="row g-2"></div>
                                                        <small class="text-danger d-none"
                                                            id="electiveSecondaryError">Please select exactly 3 elective
                                                            subjects</small>
                                                        <small class="text-muted d-block mt-1">Selected: <span
                                                                id="electiveCount" class="fw-bold">0</span>/3</small>
                                                    </div>
                                                </div>

                                                <!-- Advanced Level (Grades 12-13) -->
                                                <div id="advancedSubjects" style="display: none;">
                                                    <h6 class="text-primary mb-3">Advanced Level Subject Selection</h6>

                                                    <!-- Stream Selection -->
                                                    <div class="mb-4">
                                                        <label class="form-label fw-bold">Select Stream <span
                                                                class="text-danger">*</span></label>
                                                        <div class="row g-3" id="streamSelection">
                                                            <div class="col-md-3">
                                                                <div class="form-check border rounded p-3 stream-card"
                                                                    data-stream="Arts">
                                                                    <input class="form-check-input" type="radio"
                                                                        name="stream" id="streamArts" value="Arts">
                                                                    <label class="form-check-label fw-bold"
                                                                        for="streamArts">
                                                                        <i class="material-symbols-rounded">palette</i>
                                                                        Arts Stream
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-check border rounded p-3 stream-card"
                                                                    data-stream="Commerce">
                                                                    <input class="form-check-input" type="radio"
                                                                        name="stream" id="streamCommerce"
                                                                        value="Commerce">
                                                                    <label class="form-check-label fw-bold"
                                                                        for="streamCommerce">
                                                                        <i
                                                                            class="material-symbols-rounded">business_center</i>
                                                                        Commerce Stream
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-check border rounded p-3 stream-card"
                                                                    data-stream="Science">
                                                                    <input class="form-check-input" type="radio"
                                                                        name="stream" id="streamScience"
                                                                        value="Science">
                                                                    <label class="form-check-label fw-bold"
                                                                        for="streamScience">
                                                                        <i class="material-symbols-rounded">science</i>
                                                                        Science Stream
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-check border rounded p-3 stream-card"
                                                                    data-stream="Technology">
                                                                    <input class="form-check-input" type="radio"
                                                                        name="stream" id="streamTechnology"
                                                                        value="Technology">
                                                                    <label class="form-check-label fw-bold"
                                                                        for="streamTechnology">
                                                                        <i class="material-symbols-rounded">computer</i>
                                                                        Technology Stream
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <small class="text-danger d-none" id="streamError">Please select a
                                                            stream</small>
                                                    </div>

                                                    <!-- Stream Subjects (Choose 3 after stream selection) -->
                                                    <div class="mb-4" id="streamSubjectsContainer"
                                                        style="display: none;">
                                                        <label class="form-label fw-bold">Stream Subjects <span
                                                                class="text-danger">*</span> (Choose exactly 3)</label>
                                                        <div id="streamSubjects" class="row g-2"></div>
                                                        <small class="text-danger d-none" id="streamSubjectsError">Please
                                                            select exactly 3 subjects from your chosen stream</small>
                                                        <small class="text-muted d-block mt-1">Selected: <span
                                                                id="streamSubjectCount" class="fw-bold">0</span>/3</small>
                                                    </div>
                                                </div>

                                                <!-- Hidden inputs to store selected subjects -->
                                                <input type="hidden" name="subject_ids" id="subject_ids">
                                                <input type="hidden" name="core_subject_ids" id="core_subject_ids">
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
                                                <div class="col-md-6 password-field">
                                                    <x-input name="password" type="password" title="Password"
                                                        :isRequired="true" attr="minlength='8'"
                                                        placeholder="Enter password (min 8 characters)" />
                                                </div>
                                                <div class="col-md-6 password-field">
                                                    <x-input name="password_confirmation" type="password"
                                                        title="Confirm Password" :isRequired="true"
                                                        placeholder="Confirm your password" attr="minlength='8'" />
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
                                                </div>
                                                <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple
                                                    roles</small>
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
                                                    <h6 class="text-primary mb-3 d-flex align-items-center">
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
                                <div class="card">
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

            <!-- NFC Modal -->
            <div class="modal fade" id="nfcModal" tabindex="-1" aria-labelledby="nfcModalLabel" aria-hidden="true"
                data-bs-backdrop="static" data-bs-keyboard="false">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-gradient-primary">
                            <h5 class="modal-title text-white" id="nfcModalLabel">
                                <i class="material-symbols-rounded me-2">nfc</i>
                                NFC Wristband Registration
                            </h5>
                        </div>
                        <div class="modal-body text-center py-5">
                            <div id="nfcWaiting">
                                <div class="nfc-animation mb-4">
                                    <i class="material-symbols-rounded text-primary"
                                        style="font-size: 80px; animation: pulse 2s infinite;">nfc</i>
                                </div>
                                <h5 class="mb-3">Put NFC Wristband to Copy Student Data</h5>
                                <p class="text-muted">Please hold the NFC wristband near your device to write student
                                    information to the tag.</p>
                                <div class="spinner-border text-primary mt-3" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>

                            <div id="nfcSuccess" style="display: none;">
                                <div class="mb-4">
                                    <i class="material-symbols-rounded text-success"
                                        style="font-size: 80px;">check_circle</i>
                                </div>
                                <h5 class="text-success mb-3">Data Written Successfully!</h5>
                                <p class="text-muted">Student data has been successfully written to the NFC wristband.</p>
                            </div>

                            <div id="nfcError" style="display: none;">
                                <div class="mb-4">
                                    <i class="material-symbols-rounded text-danger" style="font-size: 80px;">error</i>
                                </div>
                                <h5 class="text-danger mb-3">Error Writing Data</h5>
                                <p class="text-muted" id="nfcErrorMessage">Failed to write data to NFC tag. Please try
                                    again.</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" id="nfcSkipBtn">
                                <i class="material-symbols-rounded me-1">skip_next</i>
                                Skip NFC & Submit
                            </button>
                            <button type="button" class="btn btn-danger" id="nfcCancelBtn">
                                <i class="material-symbols-rounded me-1">cancel</i>
                                Cancel
                            </button>
                            <button type="button" class="btn btn-success" id="nfcContinueBtn" style="display: none;">
                                <i class="material-symbols-rounded me-1">done</i>
                                Continue & Submit
                            </button>
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
        window.subjectsByGradeUrl = '{{ route('admin.management.students.subjects-by-grade') }}';
        window.classesByGradeUrl = '{{ route('admin.management.students.classes-by-grade') }}';
        window.selectedSubjects = @json(isset($student) ? $student->subjects->pluck('id')->toArray() : []);
        window.initialClassId = @json(old('class_id', $student?->class_id ?? ''));
        window.allClasses = @json($classesArray);
        window.faceRecognitionApiUrl = '{{ env('FACE_RECOGNITION_API_URL', 'http://localhost:5004') }}';
        window.studentId = {{ $id ?? 'null' }};
        window.studentCode = '{{ $student->student_code ?? '' }}';
    </script>

    <!-- Face Capture JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Face Capture Elements
            const faceCaptureVideo = document.getElementById('faceCaptureVideo');
            const faceOverlay = document.getElementById('faceOverlay');
            const startFaceCaptureBtn = document.getElementById('startFaceCaptureBtn');
            const stopFaceCaptureBtn = document.getElementById('stopFaceCaptureBtn');
            const autoCaptureFaceBtn = document.getElementById('autoCaptureFaceBtn');
            const clearFaceImagesBtn = document.getElementById('clearFaceImagesBtn');
            const trainFaceModelBtn = document.getElementById('trainFaceModelBtn');
            const captureCountEl = document.getElementById('captureCount');
            const faceProgressFill = document.getElementById('faceProgressFill');
            const faceThumbnails = document.getElementById('faceThumbnails');
            const faceStatusBadge = document.getElementById('faceStatusBadge');
            const faceStatusText = document.getElementById('faceStatusText');
            const faceImagesCapturedInput = document.getElementById('faceImagesCaptured');
            const faceDataJsonInput = document.getElementById('faceDataJson');

            let faceStream = null;
            let capturedFaceImages = [];
            let autoCaptureInterval = null;
            let isAutoCapturing = false;
            const MAX_FACE_IMAGES = 40;
            const MIN_FACE_IMAGES = 30;

            // Update face capture status
            function updateFaceStatus(status, text) {
                faceStatusBadge.className = 'face-status-badge face-status-' + status;
                faceStatusText.textContent = text;
            }

            // Update capture count display
            function updateCaptureCount() {
                const count = capturedFaceImages.length;
                captureCountEl.textContent = count;
                faceProgressFill.style.width = Math.min((count / MIN_FACE_IMAGES) * 100, 100) + '%';
                faceImagesCapturedInput.value = count;

                // Update button states
                clearFaceImagesBtn.disabled = count === 0;
                trainFaceModelBtn.disabled = count < MIN_FACE_IMAGES;

                if (count >= MIN_FACE_IMAGES) {
                    updateFaceStatus('complete', `${count} images captured - Ready to train!`);
                    faceProgressFill.style.background = 'linear-gradient(90deg, #2196F3, #03A9F4)';
                }
            }

            // Add thumbnail to gallery
            function addFaceThumbnail(imageData, index) {
                // Clear placeholder if first image
                if (capturedFaceImages.length === 1) {
                    faceThumbnails.innerHTML = '';
                }

                const img = document.createElement('img');
                img.src = imageData;
                img.className = 'face-thumbnail';
                img.title = `Face image ${index + 1}`;
                img.onclick = function() {
                    // Remove this image on click
                    if (confirm('Remove this face image?')) {
                        capturedFaceImages.splice(index, 1);
                        renderAllThumbnails();
                        updateCaptureCount();
                    }
                };
                faceThumbnails.appendChild(img);
                faceThumbnails.scrollTop = faceThumbnails.scrollHeight;
            }

            // Render all thumbnails (for refresh after delete)
            function renderAllThumbnails() {
                if (capturedFaceImages.length === 0) {
                    faceThumbnails.innerHTML = `
                    <div class="text-muted text-center w-100 py-4">
                        <i class="material-symbols-rounded" style="font-size: 32px;">add_a_photo</i>
                        <p class="mb-0 mt-2" style="font-size: 12px;">No face images captured yet</p>
                    </div>`;
                    return;
                }

                faceThumbnails.innerHTML = '';
                capturedFaceImages.forEach((imgData, index) => {
                    const img = document.createElement('img');
                    img.src = imgData;
                    img.className = 'face-thumbnail';
                    img.title = `Face image ${index + 1}`;
                    img.onclick = function() {
                        if (confirm('Remove this face image?')) {
                            capturedFaceImages.splice(index, 1);
                            renderAllThumbnails();
                            updateCaptureCount();
                        }
                    };
                    faceThumbnails.appendChild(img);
                });
            }

            // Start face capture camera
            startFaceCaptureBtn.addEventListener('click', async function() {
                try {
                    faceStream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            width: 640,
                            height: 480,
                            facingMode: 'user'
                        }
                    });
                    faceCaptureVideo.srcObject = faceStream;

                    startFaceCaptureBtn.disabled = true;
                    stopFaceCaptureBtn.disabled = false;
                    autoCaptureFaceBtn.disabled = false;

                    updateFaceStatus('ready', 'Camera active - Position your face');
                    faceOverlay.classList.remove('no-face');
                    faceOverlay.classList.add('detecting');

                } catch (error) {
                    console.error('Error accessing camera:', error);
                    updateFaceStatus('error', 'Camera access denied');
                    alert('Unable to access camera. Please allow camera permissions.');
                }
            });

            // Stop face capture camera
            stopFaceCaptureBtn.addEventListener('click', function() {
                stopFaceCamera();
            });

            function stopFaceCamera() {
                if (faceStream) {
                    faceStream.getTracks().forEach(track => track.stop());
                    faceCaptureVideo.srcObject = null;
                    faceStream = null;
                }

                stopAutoCapture();

                startFaceCaptureBtn.disabled = false;
                stopFaceCaptureBtn.disabled = true;
                autoCaptureFaceBtn.disabled = true;

                if (capturedFaceImages.length >= MIN_FACE_IMAGES) {
                    updateFaceStatus('complete', `${capturedFaceImages.length} images captured - Ready!`);
                } else {
                    updateFaceStatus('ready', 'Camera stopped');
                }
                faceOverlay.classList.remove('detecting', 'no-face');
            }

            // Auto capture faces
            autoCaptureFaceBtn.addEventListener('click', function() {
                if (isAutoCapturing) {
                    stopAutoCapture();
                } else {
                    startAutoCapture();
                }
            });

            function startAutoCapture() {
                if (!faceStream) return;

                isAutoCapturing = true;
                autoCaptureFaceBtn.innerHTML = '<i class="material-symbols-rounded me-1">stop</i> Stop Capture';
                autoCaptureFaceBtn.classList.remove('btn-primary');
                autoCaptureFaceBtn.classList.add('btn-warning');
                updateFaceStatus('capturing', 'Auto-capturing... Move your head slowly');

                // Capture every 500ms
                autoCaptureInterval = setInterval(async () => {
                    if (capturedFaceImages.length >= MAX_FACE_IMAGES) {
                        stopAutoCapture();
                        updateFaceStatus('complete', 'Maximum images captured!');
                        return;
                    }

                    await captureFaceImage();
                }, 500);
            }

            function stopAutoCapture() {
                if (autoCaptureInterval) {
                    clearInterval(autoCaptureInterval);
                    autoCaptureInterval = null;
                }
                isAutoCapturing = false;
                autoCaptureFaceBtn.innerHTML =
                    '<i class="material-symbols-rounded me-1">auto_awesome</i> Auto Capture (30)';
                autoCaptureFaceBtn.classList.remove('btn-warning');
                autoCaptureFaceBtn.classList.add('btn-primary');

                if (capturedFaceImages.length >= MIN_FACE_IMAGES) {
                    updateFaceStatus('complete', `${capturedFaceImages.length} images - Ready to train!`);
                } else if (faceStream) {
                    updateFaceStatus('ready',
                        `${capturedFaceImages.length} images - Need ${MIN_FACE_IMAGES - capturedFaceImages.length} more`
                        );
                }
            }

            // Capture single face image
            async function captureFaceImage() {
                if (!faceStream || !faceCaptureVideo.videoWidth) return;

                const canvas = document.createElement('canvas');
                canvas.width = faceCaptureVideo.videoWidth;
                canvas.height = faceCaptureVideo.videoHeight;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(faceCaptureVideo, 0, 0);

                const imageData = canvas.toDataURL('image/jpeg', 0.9);
                capturedFaceImages.push(imageData);
                addFaceThumbnail(imageData, capturedFaceImages.length - 1);
                updateCaptureCount();

                // Flash effect
                faceOverlay.style.borderColor = '#fff';
                setTimeout(() => {
                    faceOverlay.style.borderColor = '';
                }, 100);
            }

            // Clear all face images
            clearFaceImagesBtn.addEventListener('click', function() {
                if (confirm('Are you sure you want to clear all captured face images?')) {
                    capturedFaceImages = [];
                    renderAllThumbnails();
                    updateCaptureCount();
                    updateFaceStatus('ready', 'All images cleared');
                }
            });

            // Train face model
            trainFaceModelBtn.addEventListener('click', async function() {
                if (capturedFaceImages.length < MIN_FACE_IMAGES) {
                    alert(`Please capture at least ${MIN_FACE_IMAGES} face images before training.`);
                    return;
                }

                const studentNameFirst = document.querySelector('input[name="first_name"]')?.value ||
                '';
                const studentNameLast = document.querySelector('input[name="last_name"]')?.value || '';
                const studentCode = document.querySelector('input[name="student_code"]')?.value || '';

                if (!studentNameFirst || !studentNameLast) {
                    alert('Please enter student name first.');
                    return;
                }

                if (!studentCode) {
                    alert('Please wait for student code to be generated.');
                    return;
                }

                const studentName = `${studentNameFirst} ${studentNameLast}`;

                // Disable buttons during training
                trainFaceModelBtn.disabled = true;
                clearFaceImagesBtn.disabled = true;

                // Show training progress
                const progressContainer = document.createElement('div');
                progressContainer.id = 'trainingProgressContainer';
                progressContainer.className = 'mt-3';
                progressContainer.innerHTML = `
                <div class="card border-warning">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="spinner-border spinner-border-sm text-warning me-2" role="status"></div>
                            <strong id="trainingStepText">Preparing images...</strong>
                        </div>
                        <div class="progress" style="height: 20px;">
                            <div id="trainingProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-warning"
                                 role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                <span id="trainingProgressText">0%</span>
                            </div>
                        </div>
                        <div class="mt-2 small text-muted">
                            <span id="trainingDetails">Uploading ${capturedFaceImages.length} face images to server...</span>
                        </div>
                    </div>
                </div>
            `;
                trainFaceModelBtn.parentNode.appendChild(progressContainer);

                const trainingStepText = document.getElementById('trainingStepText');
                const trainingProgressBar = document.getElementById('trainingProgressBar');
                const trainingProgressText = document.getElementById('trainingProgressText');
                const trainingDetails = document.getElementById('trainingDetails');

                // Update progress function
                function updateTrainingProgress(percent, step, details) {
                    trainingProgressBar.style.width = percent + '%';
                    trainingProgressBar.setAttribute('aria-valuenow', percent);
                    trainingProgressText.textContent = percent + '%';
                    if (step) trainingStepText.textContent = step;
                    if (details) trainingDetails.textContent = details;
                }

                trainFaceModelBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2"></span>Training...';
                updateFaceStatus('capturing', 'Training in progress...');

                try {
                    // Step 1: Preparing (0-10%)
                    updateTrainingProgress(5, 'Preparing images...',
                        `Processing ${capturedFaceImages.length} captured images`);
                    await new Promise(r => setTimeout(r, 300));

                    // Step 2: Uploading (10-30%)
                    updateTrainingProgress(15, 'Uploading to server...',
                        `Sending ${capturedFaceImages.length} images to Face Recognition API`);

                    // Send face images to Face Recognition API
                    const response = await fetch(window.faceRecognitionApiUrl +
                        '/api/students/register', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                student_id: studentCode,
                                name: studentName,
                                images: capturedFaceImages
                            })
                        });

                    // Step 3: Processing (30-60%)
                    updateTrainingProgress(40, 'Processing faces...',
                        'Detecting and extracting face features');
                    await new Promise(r => setTimeout(r, 500));

                    updateTrainingProgress(60, 'Generating embeddings...',
                        'Creating face recognition vectors');
                    await new Promise(r => setTimeout(r, 300));

                    const result = await response.json();

                    // Step 4: Training (60-90%)
                    updateTrainingProgress(80, 'Training model...',
                        'Building recognition model for student');
                    await new Promise(r => setTimeout(r, 400));

                    if (result.success || (response.ok && !result.error)) {
                        // Step 5: Complete (100%)
                        updateTrainingProgress(100, 'Training Complete!',
                            `Successfully trained with ${result.face_count || capturedFaceImages.length} valid faces`
                            );
                        trainingProgressBar.classList.remove('bg-warning', 'progress-bar-animated');
                        trainingProgressBar.classList.add('bg-success');

                        updateFaceStatus('complete', 'Face model trained successfully!');
                        faceDataJsonInput.value = JSON.stringify({
                            student_id: studentCode,
                            images_count: result.face_count || capturedFaceImages.length,
                            trained: true,
                            trained_at: new Date().toISOString()
                        });

                        // Update progress container to success state
                        setTimeout(() => {
                            progressContainer.innerHTML = `
                            <div class="alert alert-success d-flex align-items-center">
                                <i class="material-symbols-rounded me-2" style="font-size: 32px;">check_circle</i>
                                <div>
                                    <strong>Face Recognition Model Trained Successfully!</strong><br>
                                    <small>Processed ${result.face_count || capturedFaceImages.length} face images.
                                    ${result.failed_count ? `(${result.failed_count} images failed)` : ''}
                                    This student can now be recognized for attendance.</small>
                                </div>
                            </div>
                        `;
                        }, 1000);

                    } else {
                        throw new Error(result.error || result.message || 'Training failed');
                    }

                } catch (error) {
                    console.error('Error training face model:', error);

                    // Show error state
                    trainingProgressBar.classList.remove('bg-warning', 'progress-bar-animated');
                    trainingProgressBar.classList.add('bg-danger');
                    updateTrainingProgress(100, 'Training Failed', error.message);

                    updateFaceStatus('error', 'Training failed - ' + error.message);

                    setTimeout(() => {
                        progressContainer.innerHTML = `
                        <div class="alert alert-danger d-flex align-items-center">
                            <i class="material-symbols-rounded me-2" style="font-size: 32px;">error</i>
                            <div>
                                <strong>Training Failed</strong><br>
                                <small>${error.message}</small>
                            </div>
                        </div>
                    `;
                    }, 500);

                    clearFaceImagesBtn.disabled = false;
                } finally {
                    trainFaceModelBtn.disabled = false;
                    trainFaceModelBtn.innerHTML =
                        '<i class="material-symbols-rounded me-1">model_training</i> Train Model Now';
                }
            });

            // Update hidden input before form submit
            const studentForm = document.getElementById('studentForm');
            if (studentForm) {
                studentForm.addEventListener('submit', function(e) {
                    if (capturedFaceImages.length > 0 && capturedFaceImages.length < MIN_FACE_IMAGES) {
                        if (!confirm(
                                `You have captured ${capturedFaceImages.length} face images but need at least ${MIN_FACE_IMAGES} for good accuracy. Continue anyway?`
                                )) {
                            e.preventDefault();
                            return false;
                        }
                    }

                    // Store face data
                    faceDataJsonInput.value = JSON.stringify({
                        images_count: capturedFaceImages.length,
                        captured_at: new Date().toISOString()
                    });
                });
            }

            // Cleanup on page unload
            window.addEventListener('beforeunload', function() {
                stopFaceCamera();
            });
        });
    </script>
    @vite('resources/js/admin/student-form.js')
@endsection
