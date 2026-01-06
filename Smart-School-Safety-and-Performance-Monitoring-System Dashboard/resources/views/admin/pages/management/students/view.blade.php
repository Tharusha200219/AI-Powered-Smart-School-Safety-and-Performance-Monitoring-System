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

                                    {{-- Seat Assignment Section --}}
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <h6 class="mb-0 d-flex align-items-center">
                                                <i class="material-symbols-rounded me-2 icon-size-sm">event_seat</i>
                                                Seat Assignment
                                                @if (isset($needsSeatingUpdate) && $needsSeatingUpdate)
                                                    <span class="badge bg-gradient-warning badge-sm ms-2">⚠️ Marks
                                                        Changed</span>
                                                @endif
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            {{-- Warning if marks changed --}}
                                            @if (isset($needsSeatingUpdate) && $needsSeatingUpdate)
                                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                                    <i class="material-symbols-rounded me-2">warning</i>
                                                    <strong>⚠️ Student Marks Have Changed</strong>
                                                    <p class="mb-2">This student's marks have been updated since the last
                                                        seating arrangement was generated.</p>
                                                    <p class="mb-0">
                                                        <strong>Action Required:</strong>
                                                        <a href="{{ route('admin.seating-arrangement.index') }}"
                                                            class="alert-link">
                                                            Regenerate the seating arrangement
                                                        </a> to reflect the latest performance data.
                                                    </p>
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                        aria-label="Close"></button>
                                                </div>
                                            @endif

                                            @if (isset($seatAssignment) && $seatAssignment)
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="d-flex align-items-center mb-3">
                                                            <div
                                                                class="icon icon-shape bg-gradient-info shadow text-center border-radius-md me-3">
                                                                <i
                                                                    class="material-symbols-rounded opacity-10">event_seat</i>
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-0">{{ $seatAssignment->seat_position }}
                                                                </h6>
                                                                <small class="text-muted">Row
                                                                    {{ $seatAssignment->row_number }}, Seat
                                                                    {{ $seatAssignment->seat_number }}</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        @if ($seatAssignment->seatingArrangement)
                                                            <p class="mb-1"><strong>Academic Year:</strong>
                                                                {{ $seatAssignment->seatingArrangement->academic_year }}
                                                            </p>
                                                            <p class="mb-1"><strong>Term:</strong>
                                                                {{ $seatAssignment->seatingArrangement->term }}</p>
                                                            <p class="mb-0"><strong>Generated:</strong>
                                                                {{ $seatAssignment->seatingArrangement->generated_at->format('M d, Y') }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <div class="alert alert-light text-center mb-0">
                                                    <i class="material-symbols-rounded opacity-10">event_seat</i>
                                                    <p class="mb-0">No seat assignment available. Generate a seating
                                                        arrangement from the
                                                        <a href="{{ route('admin.seating-arrangement.index') }}">Seat
                                                            Arrangement</a> section.
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Performance Predictions Section --}}
                                    <div class="card mb-4">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 d-flex align-items-center">
                                                <i class="material-symbols-rounded me-2 icon-size-sm">trending_up</i>
                                                Performance Predictions
                                                @if (isset($predictionApiStatus) && $predictionApiStatus && isset($predictions) && $predictions->count() > 0)
                                                    <span class="badge bg-gradient-success badge-sm ms-2">Live</span>
                                                @else
                                                    <span class="badge bg-gradient-danger badge-sm ms-2"> API Not
                                                        Connected</span>
                                                @endif
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            {{-- Always check if API is running --}}
                                            @if (!isset($predictionApiStatus) || !$predictionApiStatus)
                                                <div class="alert alert-danger alert-dismissible fade show"
                                                    role="alert">
                                                    <i class="material-symbols-rounded me-2">error</i>
                                                    <strong>⚠️ API Not Connected</strong>
                                                    <p class="mb-2">The performance prediction API is not running.
                                                        Predictions cannot be generated.</p>
                                                    <p class="mb-0"><strong>To start the API:</strong></p>
                                                    <ol class="mb-2 mt-1">
                                                        <li>Open a new terminal</li>
                                                        <li>Navigate to: <code>student-performance-prediction-model</code>
                                                        </li>
                                                        <li>Run: <code>./start_api.sh</code></li>
                                                        <li>Refresh this page</li>
                                                    </ol>
                                                    <div class="alert alert-info mb-0">
                                                        <i class="material-symbols-rounded me-2">info</i>
                                                        <strong>Live Predictions:</strong> Predictions are generated in
                                                        real-time based on current marks and attendance. When you update
                                                        marks, refresh this page to see new predictions instantly.
                                                    </div>
                                                </div>
                                            @endif

                                            <div id="predictionsContainer">
                                                @if (isset($predictionApiStatus) && $predictionApiStatus && isset($predictions) && $predictions->count() > 0)
                                                    <div class="table-responsive">
                                                        <table class="table align-items-center mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th
                                                                        class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                                        Subject</th>
                                                                    <th
                                                                        class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                                        Attendance</th>
                                                                    <th
                                                                        class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                                        Current</th>
                                                                    <th
                                                                        class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                                        Predicted</th>
                                                                    <th
                                                                        class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                                        Trend</th>
                                                                    {{-- <th
                                                                        class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                                        Confidence</th> --}}
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($predictions as $prediction)
                                                                    <tr>
                                                                        <td>
                                                                            <div class="d-flex px-2 py-1">
                                                                                <div
                                                                                    class="d-flex flex-column justify-content-center">
                                                                                    <h6 class="mb-0 text-sm">
                                                                                        {{ $prediction->subject->subject_name }}
                                                                                    </h6>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="d-flex align-items-center">
                                                                                <span
                                                                                    class="text-xs font-weight-bold">{{ number_format($prediction->current_attendance ?? 0, 0) }}%</span>
                                                                                @if ($prediction->current_attendance >= 80)
                                                                                    <i class="material-symbols-rounded text-success ms-1"
                                                                                        style="font-size: 16px;">check_circle</i>
                                                                                @elseif($prediction->current_attendance >= 60)
                                                                                    <i class="material-symbols-rounded text-warning ms-1"
                                                                                        style="font-size: 16px;">warning</i>
                                                                                @else
                                                                                    <i class="material-symbols-rounded text-danger ms-1"
                                                                                        style="font-size: 16px;">cancel</i>
                                                                                @endif
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <p class="text-xs font-weight-bold mb-0">
                                                                                {{ number_format($prediction->current_performance, 1) }}%
                                                                            </p>
                                                                        </td>
                                                                        <td>
                                                                            <p class="text-xs font-weight-bold mb-0">
                                                                                {{ number_format($prediction->predicted_performance, 1) }}%
                                                                            </p>
                                                                        </td>
                                                                        <td>
                                                                            <span
                                                                                class="badge badge-sm bg-gradient-{{ $prediction->trend_color }}">
                                                                                <i class="material-symbols-rounded"
                                                                                    style="font-size: 14px;">{{ $prediction->trend_icon }}</i>
                                                                                {{ ucfirst($prediction->prediction_trend) }}
                                                                            </span>
                                                                        </td>
                                                                        {{-- <td>
                                                                            <div class="progress-wrapper w-75 mx-auto">
                                                                                <div class="progress-info">
                                                                                    <div class="progress-percentage">
                                                                                        <span
                                                                                            class="text-xs font-weight-bold">{{ number_format($prediction->confidence, 0) }}%</span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="progress">
                                                                                    <div class="progress-bar bg-gradient-info"
                                                                                        style="width: {{ $prediction->confidence }}%">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </td> --}}
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    {{-- Recommendations Section --}}
                                                    @if ($predictions->isNotEmpty() && $predictions->first()->recommendations)
                                                        <div class="mt-4">
                                                            <h6 class="text-sm font-weight-bold mb-3">
                                                                <i class="material-symbols-rounded me-1"
                                                                    style="font-size: 18px;">psychology</i>
                                                                AI Recommendations
                                                            </h6>
                                                            <div class="row">
                                                                @foreach ($predictions as $prediction)
                                                                    <div class="col-md-6 mb-3">
                                                                        <div class="card border">
                                                                            <div class="card-body p-3">
                                                                                <div
                                                                                    class="d-flex align-items-center mb-2">
                                                                                    <span
                                                                                        class="badge bg-gradient-{{ $prediction->trend_color }} me-2">
                                                                                        {{ ucfirst($prediction->prediction_trend) }}
                                                                                    </span>
                                                                                    <h6 class="text-sm mb-0">
                                                                                        {{ $prediction->subject->subject_name }}
                                                                                    </h6>
                                                                                </div>
                                                                                <p class="text-xs text-muted mb-0">
                                                                                    <i class="material-symbols-rounded"
                                                                                        style="font-size: 14px;">lightbulb</i>
                                                                                    {{ $prediction->recommendations }}
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <div class="mt-3">
                                                        <small class="text-muted">
                                                            <i class="material-symbols-rounded"
                                                                style="font-size: 14px;">info</i>
                                                            Predictions automatically update when marks are changed. Based
                                                            on current marks, attendance, and historical performance data.
                                                            Last updated:
                                                            {{ $predictions->first()->predicted_at->format('M d, Y H:i') }}
                                                        </small>
                                                    </div>
                                                @else
                                                    <div class="alert alert-light text-center mb-0">
                                                        <i class="material-symbols-rounded opacity-10"
                                                            style="font-size: 48px;">psychology</i>
                                                        <p class="mb-0 mt-2">
                                                            @if ($marks->isEmpty())
                                                                No marks available yet. Predictions will appear
                                                                automatically once marks are entered.
                                                            @else
                                                                Predictions are being generated automatically...
                                                            @endif
                                                        </p>
                                                        <small class="text-muted">
                                                            <i class="material-symbols-rounded"
                                                                style="font-size: 14px;">bolt</i>
                                                            Predictions update instantly when marks change
                                                        </small>
                                                    </div>
                                                @endif
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
