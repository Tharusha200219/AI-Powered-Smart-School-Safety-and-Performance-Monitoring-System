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
                                    <a class="btn btn-outline-dark mb-0 btn-back-auto"
                                        href="{{ route('admin.management.students.index') }}">
                                        <i class="material-symbols-rounded me-1 icon-size-md">arrow_back</i>Back
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0 d-flex align-items-center">
                                                <i class="material-symbols-rounded me-2 icon-size-sm">person</i>
                                                Student Profile
                                            </h6>
                                        </div>
                                        <div class="card-body text-center">
                                            <div class="avatar avatar-xl rounded-circle bg-gradient-primary mx-auto mb-3">
                                                <span
                                                    class="text-white text-lg">{{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}</span>
                                            </div>
                                            <h5 class="mb-1">{{ $student->full_name }}</h5>
                                            <p class="text-secondary mb-2">{{ $student->student_code }}</p>
                                            <span
                                                class="badge {{ $student->is_active ? 'bg-gradient-success' : 'bg-gradient-danger' }} badge-sm">
                                                {{ $student->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h6 class="mb-0 d-flex align-items-center">
                                                <i class="material-symbols-rounded me-2 icon-size-sm">info</i>
                                                Personal Information
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Full Name:</label>
                                                        <p class="text-dark font-weight-bold">{{ $student->full_name }}</p>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Student Code:</label>
                                                        <p class="text-dark font-weight-bold">{{ $student->student_code }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Date of Birth:</label>
                                                        <p class="text-dark font-weight-bold">
                                                            {{ $student->date_of_birth ? $student->date_of_birth->format('M d, Y') : 'Not provided' }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Gender:</label>
                                                        <p class="text-dark font-weight-bold">
                                                            {{ $student->gender ?? 'Not specified' }}</p>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Phone:</label>
                                                        <p class="text-dark font-weight-bold">
                                                            {{ $student->phone ?? 'Not provided' }}</p>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">NIC Number:</label>
                                                        <p class="text-dark font-weight-bold">
                                                            {{ $student->nic_number ?? 'Not provided' }}</p>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="mb-3">
                                                        <label class="form-label">Address:</label>
                                                        <p class="text-dark font-weight-bold">
                                                            {{ $student->address ?? 'Not provided' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h6 class="mb-0 d-flex align-items-center">
                                                <i class="material-symbols-rounded me-2 icon-size-sm">school</i>
                                                Academic Information
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Grade Level:</label>
                                                        <p class="text-dark font-weight-bold">
                                                            <span class="badge bg-gradient-primary badge-sm">Grade
                                                                {{ $student->grade_level }}</span>
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Class:</label>
                                                        <p class="text-dark font-weight-bold">
                                                            @if ($student->schoolClass)
                                                                {{ $student->schoolClass->class_name }}
                                                                @if ($student->schoolClass->classTeacher)
                                                                    <br><small class="text-secondary">Class Teacher:
                                                                        {{ $student->schoolClass->classTeacher->full_name }}</small>
                                                                @endif
                                                            @else
                                                                <span class="text-muted">Not assigned</span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Admission Date:</label>
                                                        <p class="text-dark font-weight-bold">
                                                            {{ $student->admission_date ? $student->admission_date->format('M d, Y') : 'Not provided' }}
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Email:</label>
                                                        <p class="text-dark font-weight-bold">
                                                            {{ $student->user->email ?? 'Not provided' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if ($student->parents->count() > 0)
                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <h6 class="mb-0 d-flex align-items-center">
                                                    <i
                                                        class="material-symbols-rounded me-2 icon-size-sm">family_restroom</i>
                                                    Parent Information
                                                    <span
                                                        class="badge bg-gradient-info badge-sm ms-2">{{ $student->parents->count() }}
                                                        Parent{{ $student->parents->count() > 1 ? 's' : '' }}</span>
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    @foreach ($student->parents as $index => $parent)
                                                        <div class="col-md-6 mb-4">
                                                            <div class="card border">
                                                                <div class="card-header bg-light">
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center">
                                                                        <h6 class="mb-0">{{ $parent->full_name }}</h6>
                                                                        @if ($parent->is_emergency_contact)
                                                                            <span
                                                                                class="badge bg-gradient-warning badge-sm">Emergency
                                                                                Contact</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <div class="col-12 mb-2">
                                                                            <strong>Parent Code:</strong>
                                                                            {{ $parent->parent_code }}
                                                                        </div>
                                                                        <div class="col-12 mb-2">
                                                                            <strong>Relationship:</strong>
                                                                            <span
                                                                                class="badge bg-gradient-primary badge-sm">{{ $parent->relationship_type }}</span>
                                                                        </div>
                                                                        <div class="col-12 mb-2">
                                                                            <strong>Gender:</strong>
                                                                            @if ($parent->gender == 'M')
                                                                                <span
                                                                                    class="badge bg-gradient-info badge-sm">Male</span>
                                                                            @elseif($parent->gender == 'F')
                                                                                <span
                                                                                    class="badge bg-gradient-pink badge-sm">Female</span>
                                                                            @else
                                                                                <span
                                                                                    class="badge bg-gradient-secondary badge-sm">{{ $parent->gender }}</span>
                                                                            @endif
                                                                        </div>
                                                                        @if ($parent->date_of_birth)
                                                                            <div class="col-12 mb-2">
                                                                                <strong>Date of Birth:</strong>
                                                                                {{ $parent->date_of_birth->format('M d, Y') }}
                                                                            </div>
                                                                        @endif
                                                                        @if ($parent->mobile_phone)
                                                                            <div class="col-12 mb-2">
                                                                                <strong>Mobile:</strong>
                                                                                <a href="tel:{{ $parent->mobile_phone }}"
                                                                                    class="text-decoration-none">
                                                                                    {{ $parent->mobile_phone }}
                                                                                </a>
                                                                            </div>
                                                                        @endif
                                                                        @if ($parent->email)
                                                                            <div class="col-12 mb-2">
                                                                                <strong>Email:</strong>
                                                                                <a href="mailto:{{ $parent->email }}"
                                                                                    class="text-decoration-none">
                                                                                    {{ $parent->email }}
                                                                                </a>
                                                                            </div>
                                                                        @endif
                                                                        @if ($parent->occupation)
                                                                            <div class="col-12 mb-2">
                                                                                <strong>Occupation:</strong>
                                                                                {{ $parent->occupation }}
                                                                            </div>
                                                                        @endif
                                                                        @if ($parent->workplace)
                                                                            <div class="col-12 mb-2">
                                                                                <strong>Workplace:</strong>
                                                                                {{ $parent->workplace }}
                                                                            </div>
                                                                        @endif
                                                                        @if ($parent->work_phone)
                                                                            <div class="col-12 mb-2">
                                                                                <strong>Work Phone:</strong>
                                                                                <a href="tel:{{ $parent->work_phone }}"
                                                                                    class="text-decoration-none">
                                                                                    {{ $parent->work_phone }}
                                                                                </a>
                                                                            </div>
                                                                        @endif
                                                                        @if ($parent->address_line1)
                                                                            <div class="col-12">
                                                                                <strong>Address:</strong><br>
                                                                                <small class="text-muted">
                                                                                    {{ $parent->address_line1 }}
                                                                                    @if ($parent->address_line2)
                                                                                        , {{ $parent->address_line2 }}
                                                                                    @endif
                                                                                    @if ($parent->city)
                                                                                        <br>{{ $parent->city }}
                                                                                    @endif
                                                                                    @if ($parent->state)
                                                                                        , {{ $parent->state }}
                                                                                    @endif
                                                                                    @if ($parent->postal_code)
                                                                                        {{ $parent->postal_code }}
                                                                                    @endif
                                                                                    @if ($parent->country)
                                                                                        <br>{{ $parent->country }}
                                                                                    @endif
                                                                                </small>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($student->subjects->count() > 0)
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="mb-0 d-flex align-items-center">
                                                    <i class="material-symbols-rounded me-2 icon-size-sm">subject</i>
                                                    Enrolled Subjects
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    @foreach ($student->subjects as $subject)
                                                        <div class="col-md-4 mb-3">
                                                            <div class="border rounded p-3 text-center">
                                                                <h6 class="mb-2">{{ $subject->subject_name }}</h6>
                                                                <p class="mb-1"><strong>Code:</strong>
                                                                    {{ $subject->subject_code }}</p>
                                                                <span
                                                                    class="badge bg-gradient-info badge-sm">{{ $subject->category }}</span>
                                                                @if ($subject->credits)
                                                                    <br><small
                                                                        class="text-secondary">{{ $subject->credits }}
                                                                        Credits</small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- AI Prediction Section --}}
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0 d-flex align-items-center">
                                                <i
                                                    class="material-symbols-rounded me-2 icon-size-sm text-primary">psychology</i>
                                                AI Performance Prediction
                                                @if ($prediction)
                                                    <span class="badge bg-gradient-success badge-sm ms-2">Available</span>
                                                @else
                                                    <span class="badge bg-gradient-warning badge-sm ms-2">Service
                                                        Unavailable</span>
                                                @endif
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            @if ($prediction)
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="card border border-primary">
                                                            <div class="card-header bg-primary text-white">
                                                                <h6 class="mb-0">
                                                                    <i class="material-symbols-rounded me-2">school</i>
                                                                    Recommended Education Track
                                                                </h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="text-center mb-3">
                                                                    <h4 class="text-primary mb-2">
                                                                        {{ $prediction['prediction']['predicted_track'] ?? 'N/A' }}
                                                                    </h4>
                                                                    <div class="progress mb-2" style="height: 8px;">
                                                                        <div class="progress-bar bg-primary"
                                                                            role="progressbar"
                                                                            style="width: {{ ($prediction['prediction']['confidence'] ?? 0) * 100 }}%"
                                                                            aria-valuenow="{{ ($prediction['prediction']['confidence'] ?? 0) * 100 }}"
                                                                            aria-valuemin="0" aria-valuemax="100"></div>
                                                                    </div>
                                                                    <small class="text-muted">
                                                                        Confidence:
                                                                        {{ number_format(($prediction['prediction']['confidence'] ?? 0) * 100, 1) }}%
                                                                    </small>
                                                                </div>
                                                                @if (isset($prediction['prediction']['track_description']))
                                                                    <p class="text-sm text-muted mb-2">
                                                                        {{ $prediction['prediction']['track_description'] }}
                                                                    </p>
                                                                @endif
                                                                @if (isset($prediction['prediction']['overall_prediction']))
                                                                    <div class="alert alert-info mt-2 mb-0">
                                                                        <small><i
                                                                                class="material-symbols-rounded me-1">info</i>{{ $prediction['prediction']['overall_prediction'] }}</small>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="card border border-info">
                                                            <div class="card-header bg-info text-white">
                                                                <h6 class="mb-0">
                                                                    <i class="material-symbols-rounded me-2">analytics</i>
                                                                    Alternative Options
                                                                </h6>
                                                            </div>
                                                            <div class="card-body">
                                                                @if (isset($prediction['prediction']['class_probabilities']))
                                                                    @php
                                                                        arsort(
                                                                            $prediction['prediction'][
                                                                                'class_probabilities'
                                                                            ],
                                                                        );
                                                                        $topAlternatives = array_slice(
                                                                            $prediction['prediction'][
                                                                                'class_probabilities'
                                                                            ],
                                                                            1,
                                                                            2,
                                                                            true,
                                                                        );
                                                                    @endphp
                                                                    @foreach ($topAlternatives as $track => $probability)
                                                                        <div
                                                                            class="d-flex justify-content-between align-items-center mb-2">
                                                                            <span
                                                                                class="text-sm">{{ $track }}</span>
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="progress flex-grow-1 me-2"
                                                                                    style="height: 6px; width: 80px;">
                                                                                    <div class="progress-bar bg-info"
                                                                                        role="progressbar"
                                                                                        style="width: {{ $probability * 100 }}%"
                                                                                        aria-valuenow="{{ $probability * 100 }}"
                                                                                        aria-valuemin="0"
                                                                                        aria-valuemax="100"></div>
                                                                                </div>
                                                                                <small
                                                                                    class="text-muted">{{ number_format($probability * 100, 1) }}%</small>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Subject Analysis Section --}}
                                                @if (isset($prediction['subject_analysis']) && !empty($prediction['subject_analysis']))
                                                    <div class="row mt-4">
                                                        <div class="col-12">
                                                            <div class="card border border-warning">
                                                                <div class="card-header bg-warning text-white">
                                                                    <h6 class="mb-0">
                                                                        <i
                                                                            class="material-symbols-rounded me-2">assessment</i>
                                                                        Subject Performance Analysis
                                                                    </h6>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        @foreach ($prediction['subject_analysis'] as $subject)
                                                                            <div class="col-md-6 col-lg-4 mb-3">
                                                                                <div
                                                                                    class="border rounded p-3
                                                                                    @if (isset($subject['status'])) @if ($subject['status'] == 'good') border-success bg-light-success
                                                                                        @elseif($subject['status'] == 'bad') border-danger bg-light-danger
                                                                                        @else border-warning bg-light-warning @endif
                                                                                    @endif">
                                                                                    <div
                                                                                        class="d-flex justify-content-between align-items-start mb-2">
                                                                                        <h6 class="mb-1">
                                                                                            {{ $subject['subject'] }}</h6>
                                                                                        <div>
                                                                                            <span
                                                                                                class="badge
                                                                                                @if ($subject['grade'] == 'A') bg-gradient-success
                                                                                                @elseif($subject['grade'] == 'B') bg-gradient-info
                                                                                                @elseif($subject['grade'] == 'C') bg-gradient-warning
                                                                                                @elseif($subject['grade'] == 'D') bg-gradient-orange
                                                                                                @else bg-gradient-danger @endif
                                                                                                badge-sm me-1">
                                                                                                {{ $subject['grade'] }}
                                                                                            </span>
                                                                                            @if (isset($subject['status']))
                                                                                                <span
                                                                                                    class="badge
                                                                                                    @if ($subject['status'] == 'good') bg-success
                                                                                                    @elseif($subject['status'] == 'bad') bg-danger
                                                                                                    @else bg-warning @endif
                                                                                                    badge-sm">
                                                                                                    {{ ucfirst($subject['status']) }}
                                                                                                </span>
                                                                                            @endif
                                                                                        </div>
                                                                                    </div>
                                                                                    <p class="mb-2">
                                                                                        <strong>Average:</strong>
                                                                                        {{ number_format($subject['average_mark'], 1) }}%
                                                                                    </p>
                                                                                    <p class="mb-2">
                                                                                        <strong>Level:</strong>
                                                                                        <span
                                                                                            class="badge
                                                                                            @if ($subject['performance_level'] == 'Excellent') bg-gradient-success
                                                                                            @elseif($subject['performance_level'] == 'Good') bg-gradient-info
                                                                                            @elseif($subject['performance_level'] == 'Satisfactory') bg-gradient-warning
                                                                                            @else bg-gradient-danger @endif
                                                                                            badge-sm">
                                                                                            {{ $subject['performance_level'] }}
                                                                                        </span>
                                                                                    </p>
                                                                                    @if (isset($subject['mark_count']))
                                                                                        <p class="mb-2">
                                                                                            <small><strong>Assessments:</strong>
                                                                                                {{ $subject['mark_count'] }}</small>
                                                                                        </p>
                                                                                    @endif
                                                                                    <small class="text-muted">
                                                                                        <strong>Focus:</strong>
                                                                                        {{ $subject['focus_area'] }}
                                                                                    </small>
                                                                                    <strong>Focus:</strong>
                                                                                    {{ $subject['focus_area'] }}
                                                                                    </small>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- Focus Areas and Effort Requirements --}}
                                                @if (
                                                    (isset($prediction['focus_areas']) && !empty($prediction['focus_areas'])) ||
                                                        (isset($prediction['effort_requirements']) && !empty($prediction['effort_requirements'])))
                                                    <div class="row mt-4">
                                                        @if (isset($prediction['focus_areas']) && !empty($prediction['focus_areas']))
                                                            <div class="col-md-6">
                                                                <div class="card border border-danger">
                                                                    <div class="card-header bg-danger text-white">
                                                                        <h6 class="mb-0">
                                                                            <i
                                                                                class="material-symbols-rounded me-2">target</i>
                                                                            Areas Needing Attention
                                                                        </h6>
                                                                    </div>
                                                                    <div class="card-body">
                                                                        <ul class="list-unstyled mb-0">
                                                                            @foreach ($prediction['focus_areas'] as $area)
                                                                                <li class="mb-2">
                                                                                    <i class="material-symbols-rounded text-danger me-2"
                                                                                        style="font-size: 16px;">error</i>
                                                                                    <small>{{ $area }}</small>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if (isset($prediction['effort_requirements']) && !empty($prediction['effort_requirements']))
                                                            <div class="col-md-6">
                                                                <div class="card border border-secondary">
                                                                    <div class="card-header bg-secondary text-white">
                                                                        <h6 class="mb-0">
                                                                            <i
                                                                                class="material-symbols-rounded me-2">schedule</i>
                                                                            Effort Requirements
                                                                        </h6>
                                                                    </div>
                                                                    <div class="card-body">
                                                                        <ul class="list-unstyled mb-0">
                                                                            @foreach ($prediction['effort_requirements'] as $effort)
                                                                                <li class="mb-2">
                                                                                    <i class="material-symbols-rounded text-secondary me-2"
                                                                                        style="font-size: 16px;">access_time</i>
                                                                                    <small>{{ $effort }}</small>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif

                                                {{-- Improvement Targets --}}
                                                @if (isset($prediction['improvement_targets']) && !empty($prediction['improvement_targets']))
                                                    <div class="row mt-4">
                                                        <div class="col-12">
                                                            <div class="card border border-success">
                                                                <div class="card-header bg-success text-white">
                                                                    <h6 class="mb-0">
                                                                        <i
                                                                            class="material-symbols-rounded me-2">trending_up</i>
                                                                        Improvement Targets
                                                                    </h6>
                                                                </div>
                                                                <div class="card-body">
                                                                    @foreach ($prediction['improvement_targets'] as $target)
                                                                        <div class="alert alert-success mb-3">
                                                                            <div
                                                                                class="d-flex justify-content-between align-items-center">
                                                                                <div>
                                                                                    <strong>Target Average:
                                                                                        {{ number_format($target['target_average'], 1) }}%</strong>
                                                                                    <br>
                                                                                    <small class="text-muted">
                                                                                        Effort Level:
                                                                                        {{ $target['effort_level'] }} |
                                                                                        Timeframe:
                                                                                        {{ $target['timeframe'] }}
                                                                                    </small>
                                                                                </div>
                                                                                <span
                                                                                    class="badge bg-gradient-success badge-sm">Recommended</span>
                                                                            </div>
                                                                            <p class="mb-0 mt-2">
                                                                                {{ $target['description'] }}</p>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if (isset($prediction['features_used']))
                                                    <div class="row mt-4">
                                                        <div class="col-12">
                                                            <div class="card border border-success">
                                                                <div class="card-header bg-success text-white">
                                                                    <h6 class="mb-0">
                                                                        <i
                                                                            class="material-symbols-rounded me-2">data_usage</i>
                                                                        Prediction Factors Analyzed
                                                                    </h6>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        @foreach ($prediction['features_used'] as $feature => $value)
                                                                            <div class="col-md-3 col-sm-6 mb-3">
                                                                                <div class="text-center">
                                                                                    <div class="bg-light rounded p-2">
                                                                                        <small
                                                                                            class="text-muted d-block">{{ $feature }}</small>
                                                                                        <strong
                                                                                            class="text-success">{{ $value }}</strong>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="row mt-3">
                                                    <div class="col-12">
                                                        <div class="alert alert-info">
                                                            <i class="material-symbols-rounded me-2">info</i>
                                                            <strong>AI Prediction Note:</strong> This comprehensive analysis
                                                            includes subject-specific performance evaluation, personalized
                                                            improvement recommendations, and effort assessments using
                                                            advanced
                                                            machine learning algorithms. Predictions update automatically
                                                            when
                                                            student marks are modified.
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-center py-5">
                                                    <i class="material-symbols-rounded text-muted"
                                                        style="font-size: 4rem;">psychology_alt</i>
                                                    <h5 class="text-muted mt-3">AI Prediction Unavailable</h5>
                                                    <p class="text-muted mb-3">The AI prediction service is currently
                                                        unavailable. This could be due to:</p>
                                                    <ul class="text-muted text-start d-inline-block">
                                                        <li>Prediction API server not running</li>
                                                        <li>Insufficient student data for analysis</li>
                                                        <li>Network connectivity issues</li>
                                                    </ul>
                                                    <br>
                                                    <small class="text-muted">Please ensure both the Laravel application
                                                        and AI prediction API are running. In the meantime, the system
                                                        will use demo predictions based on basic performance
                                                        metrics.</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if (checkPermission('admin.management.students.edit'))
                                <div class="text-end mt-4">
                                    <a href="{{ route('admin.management.students.form', ['id' => $student->student_id]) }}"
                                        class="btn btn-primary">
                                        <i class="material-symbols-rounded me-1">edit</i>Edit Student
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
