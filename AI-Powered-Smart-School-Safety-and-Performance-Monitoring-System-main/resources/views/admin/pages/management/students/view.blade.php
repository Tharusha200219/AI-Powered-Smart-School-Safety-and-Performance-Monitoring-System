@extends('admin.layouts.app')

@section('title', pageTitle())

@section('content')
    @include('admin.layouts.sidebar')

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        @include('admin.layouts.navbar')

        <div class="container-fluid pt-2">
            @include('admin.layouts.flash')
            
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card bg-gradient-dark">
                        <div class="card-body p-3">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h6 class="text-white mb-0 d-flex align-items-center">
                                        <i class="material-symbols-rounded me-2">engineering</i>
                                        {{ pageTitle() }}
                                    </h6>
                                    <p class="text-sm text-white opacity-8 mb-0">Detailed student analytics and information dashboard</p>
                                </div>
                                <div class="col-4 text-end">
                                    <a class="btn btn-outline-white mb-0 btn-sm" href="{{ route('admin.management.students.index') }}">
                                        <i class="material-symbols-rounded me-1" style="font-size: 1rem;">arrow_back</i> Back to List
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Left Sidebar: Profile Summary -->
                <div class="col-lg-4 col-md-5 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center p-4">
                            <!-- Avatar -->
                            <div class="avatar avatar-xxl rounded-circle bg-gradient-primary mx-auto mb-3 shadow">
                                <span class="text-white h3 m-0">{{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}</span>
                            </div>
                            
                            <!-- Basic Details -->
                            <h4 class="mb-1">{{ $student->full_name }}</h4>
                            <p class="text-secondary font-weight-bold mb-2">{{ $student->student_code }}</p>
                            
                            <div class="d-flex justify-content-center gap-2 mb-4">
                                <span class="badge {{ $student->is_active ? 'bg-gradient-success' : 'bg-gradient-danger' }} badge-sm">
                                    {{ $student->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                <span class="badge bg-gradient-info badge-sm">Grade {{ $student->grade_level }}</span>
                            </div>

                            <hr class="horizontal dark my-4">
                            
                            <!-- Quick Info -->
                            <div class="text-start">
                                <p class="text-sm mb-2 text-dark font-weight-bold">QUICK INFORMATION</p>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon icon-shape icon-sm shadow border-radius-sm bg-gradient-primary text-center me-2 d-flex align-items-center justify-content-center">
                                        <i class="material-symbols-rounded opacity-10 text-white" style="font-size:16px;">class</i>
                                    </div>
                                    <div class="text-sm">
                                        <h6 class="mb-0 text-sm">Class</h6>
                                        <p class="mb-0 text-xs text-secondary">{{ $student->schoolClass ? $student->schoolClass->class_name : 'Not assigned' }}</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon icon-shape icon-sm shadow border-radius-sm bg-gradient-primary text-center me-2 d-flex align-items-center justify-content-center">
                                        <i class="material-symbols-rounded opacity-10 text-white" style="font-size:16px;">calendar_today</i>
                                    </div>
                                    <div class="text-sm">
                                        <h6 class="mb-0 text-sm">Admission Date</h6>
                                        <p class="mb-0 text-xs text-secondary">{{ $student->admission_date ? $student->admission_date->format('M d, Y') : 'Not provided' }}</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon icon-shape icon-sm shadow border-radius-sm bg-gradient-primary text-center me-2 d-flex align-items-center justify-content-center">
                                        <i class="material-symbols-rounded opacity-10 text-white" style="font-size:16px;">phone</i>
                                    </div>
                                    <div class="text-sm">
                                        <h6 class="mb-0 text-sm">Phone</h6>
                                        <p class="mb-0 text-xs text-secondary">{{ $student->mobile_phone ?: 'Not provided' }}</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="icon icon-shape icon-sm shadow border-radius-sm bg-gradient-primary text-center me-2 d-flex align-items-center justify-content-center">
                                        <i class="material-symbols-rounded opacity-10 text-white" style="font-size:16px;">email</i>
                                    </div>
                                    <div class="text-sm">
                                        <h6 class="mb-0 text-sm">Email</h6>
                                        <p class="mb-0 text-xs text-secondary">{{ $student->user->email ?? 'Not provided' }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            @if (checkPermission('admin.management.students.edit'))
                                <hr class="horizontal dark my-4">
                                <a href="{{ route('admin.management.students.form', ['id' => $student->student_id]) }}" class="btn btn-outline-primary w-100 mb-0">
                                    <i class="material-symbols-rounded me-1" style="font-size: 1rem;">edit</i> Edit Student Settings
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Content: Tabbed Interface -->
                <div class="col-lg-8 col-md-7 mb-4">
                    <div class="card h-100">
                        <div class="card-header pb-0 p-3">
                            <div class="nav-wrapper position-relative end-0">
                                <ul class="nav nav-pills nav-fill p-1" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link mb-0 px-0 py-1 active" data-bs-toggle="tab" href="#personal-info" role="tab" aria-selected="true">
                                            <i class="material-symbols-rounded align-middle me-1">person</i>
                                            <span class="ms-1">Details</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" href="#academics" role="tab" aria-selected="false">
                                            <i class="material-symbols-rounded align-middle me-1">school</i>
                                            <span class="ms-1">Academics</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" href="#parents" role="tab" aria-selected="false">
                                            <i class="material-symbols-rounded align-middle me-1">family_restroom</i>
                                            <span class="ms-1">Parents</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" href="#security" role="tab" aria-selected="false">
                                            <i class="material-symbols-rounded align-middle me-1">security</i>
                                            <span class="ms-1">Security</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div class="tab-content" id="v-pills-tabContent">
                                <!-- Personal Info Tab -->
                                <div class="tab-pane fade show active" id="personal-info" role="tabpanel">
                                    <h6 class="mb-3 text-uppercase text-body text-xs font-weight-bolder">Personal Information</h6>
                                    <ul class="list-group">
                                        <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Full Name:</strong> &nbsp; {{ $student->full_name }}</li>
                                        <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Date of Birth:</strong> &nbsp; {{ $student->date_of_birth ? $student->date_of_birth->format('M d, Y') : 'Not provided' }}</li>
                                        <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Gender:</strong> &nbsp; {{ $student->gender ?? 'Not specified' }}</li>
                                        <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">NIC Number:</strong> &nbsp; {{ $student->nic_number ?? 'Not provided' }}</li>
                                        <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Address:</strong> &nbsp; 
                                            @if ($student->address_line1)
                                                {{ $student->address_line1 }}
                                                @if ($student->address_line2), {{ $student->address_line2 }}@endif
                                                @if ($student->city), {{ $student->city }}@endif
                                                @if ($student->state), {{ $student->state }}@endif
                                                @if ($student->postal_code) {{ $student->postal_code }}@endif
                                                @if ($student->country), {{ $student->country }}@endif
                                            @else
                                                <span class="text-muted">Not provided</span>
                                            @endif
                                        </li>
                                    </ul>
                                </div>

                                <!-- Academics Tab -->
                                <div class="tab-pane fade" id="academics" role="tabpanel">
                                    <h6 class="mb-3 text-uppercase text-body text-xs font-weight-bolder">Enrolled Subjects</h6>
                                    @if ($student->subjects->count() > 0)
                                        <div class="row">
                                            @foreach ($student->subjects as $subject)
                                                <div class="col-md-6 mb-3">
                                                    <div class="card shadow-none border h-100">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="icon icon-shape icon-sm shadow border-radius-sm bg-gradient-info text-center me-3 d-flex align-items-center justify-content-center">
                                                                    <i class="material-symbols-rounded opacity-10 text-white" style="font-size:16px;">library_books</i>
                                                                </div>
                                                                <div>
                                                                    <h6 class="mb-0 text-sm">{{ $subject->subject_name }}</h6>
                                                                    <span class="text-xs text-secondary">{{ $subject->subject_code }} | {{ $subject->category }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="alert alert-light" role="alert">
                                            No subjects enrolled.
                                        </div>
                                    @endif
                                </div>

                                <!-- Parents Tab -->
                                <div class="tab-pane fade" id="parents" role="tabpanel">
                                    <h6 class="mb-3 text-uppercase text-body text-xs font-weight-bolder">Parent Information</h6>
                                    @if ($student->parents->count() > 0)
                                        <div class="row">
                                            @foreach ($student->parents as $index => $parent)
                                                <div class="col-md-12 mb-3">
                                                    <div class="card shadow-none border">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                                <h6 class="mb-0 d-flex align-items-center">
                                                                    <i class="material-symbols-rounded me-2 text-primary" style="font-size: 1.2rem;">escalator_warning</i>
                                                                    {{ $parent->full_name }}
                                                                </h6>
                                                                @if ($parent->is_emergency_contact)
                                                                    <span class="badge bg-gradient-warning badge-sm">Emergency</span>
                                                                @endif
                                                            </div>
                                                            <div class="row text-sm">
                                                                <div class="col-sm-6 mb-2"><strong>Relationship:</strong> <span class="badge bg-gradient-primary badge-sm ms-1">{{ $parent->relationship_type }}</span></div>
                                                                <div class="col-sm-6 mb-2"><strong>Phone:</strong> <a href="tel:{{ $parent->mobile_phone }}" class="text-secondary">{{ $parent->mobile_phone ?: 'N/A' }}</a></div>
                                                                <div class="col-sm-6 mb-2"><strong>Email:</strong> <a href="mailto:{{ $parent->email }}" class="text-secondary">{{ $parent->email ?: 'N/A' }}</a></div>
                                                                <div class="col-sm-6 mb-2"><strong>Workplace:</strong> <span class="text-secondary">{{ $parent->workplace ?: 'N/A' }}</span></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="alert alert-light" role="alert">
                                            No parent information available.
                                        </div>
                                    @endif
                                </div>

                                <!-- Security Tab -->
                                <div class="tab-pane fade" id="security" role="tabpanel">
                                    <h6 class="mb-3 text-uppercase text-body text-xs font-weight-bolder">Face Recognition Status</h6>
                                    <div class="card shadow-none border mb-4">
                                        <div class="card-body p-3">
                                            @php
                                                $faceDataPath = 'face_data/' . $student->student_id;
                                                $hasFaceData = file_exists(storage_path('app/' . $faceDataPath . '/model.pkl'));
                                            @endphp
                                            
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="icon icon-shape icon-md shadow border-radius-sm {{ $hasFaceData ? 'bg-gradient-success' : 'bg-gradient-secondary' }} text-center me-3 d-flex align-items-center justify-content-center">
                                                    <i class="material-symbols-rounded opacity-10 text-white">face_retouching_natural</i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $hasFaceData ? 'Registered successfully' : 'Not registered' }}</h6>
                                                    <p class="text-sm mb-0 text-secondary">
                                                        {{ $hasFaceData ? 'Face data is stored securely and ready for attendance.' : 'No face data captured yet. Register in the edit menu.' }}
                                                    </p>
                                                </div>
                                            </div>

                                            @if ($hasFaceData)
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-sm btn-outline-warning mb-0"
                                                        onclick="if(confirm('Are you sure you want to remove the face recognition data?')) { window.location.href='{{ route('admin.management.students.remove-face', $student->student_id) }}'; }">
                                                        Remove Data
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-primary mb-0"
                                                        onclick="alert('Please go to Edit Student page to re-capture face data')">
                                                        Re-capture
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <h6 class="mb-3 text-uppercase text-body text-xs font-weight-bolder">System Access</h6>
                                    <div class="card shadow-none border">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <div class="icon icon-shape icon-md shadow border-radius-sm bg-gradient-info text-center me-3 d-flex align-items-center justify-content-center">
                                                    <i class="material-symbols-rounded opacity-10 text-white">how_to_reg</i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">User Account</h6>
                                                    <p class="text-sm mb-0 text-secondary">Student portal access is {{ $student->is_active ? 'active' : 'revoked' }}.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- AI Analytics Section (Full Width Bottom) -->
            <div class="row">
                <div class="col-12">
                    <h5 class="mb-3">AI Analytics & Insights</h5>
                </div>
                <!-- Performance Prediction -->
                <div class="col-lg-8 mb-4">
                    @include('admin.pages.management.students.partials.performance_prediction')
                </div>
                
                <!-- Seating Arrangement -->
                <div class="col-lg-4 mb-4">
                    @include('admin.pages.management.students.partials.seating_card')
                </div>
            </div>

        </div>
    </main>
@endsection