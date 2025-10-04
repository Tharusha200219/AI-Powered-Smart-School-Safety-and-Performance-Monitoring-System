@extends('admin.layouts.app')

@section('title', 'Academic Year Information')

@section('css')
    @vite('resources/css/admin/forms.css')
    <style>
        .card {
            border-radius: 12px !important;
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
        }

        .info-section {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid #e9ecef;
        }

        .info-section h6 {
            color: #5e72e4;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .info-item {
            margin-bottom: 0.75rem;
        }

        .info-label {
            font-weight: 600;
            color: #495057;
            font-size: 0.875rem;
        }

        .info-value {
            color: #6c757d;
            font-size: 0.95rem;
            margin-top: 0.25rem;
        }

        .info-value.empty {
            color: #adb5bd;
            font-style: italic;
        }

        .btn-edit-settings {
            background: linear-gradient(135deg, #5e72e4, #825ee4);
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .btn-edit-settings:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(94, 114, 228, 0.3);
            color: white;
        }

        .current-year-badge {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            display: inline-block;
        }

        .status-active {
            color: #28a745;
            font-weight: 600;
        }

        .status-inactive {
            color: #6c757d;
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
                                    <h6 class="mb-0">Academic Year Information</h6>
                                </div>
                             <div class="col-6 text-end">
    <a href="{{ route('admin.setup.settings.index') }}" 
       class="btn btn-outline-dark d-inline-flex align-items-center justify-content-center btn-custom me-2">
        <i class="material-symbols-rounded me-1">edit</i>
        Edit in Settings
    </a>

    <a href="{{ route('admin.dashboard.index') }}" 
       class="btn btn-outline-dark d-inline-flex align-items-center justify-content-center btn-custom">
        <i class="material-symbols-rounded me-1 icon-size-md">arrow_back</i>
        Back to Dashboard
    </a>
</div>

                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Current Academic Year -->
                            <div class="info-section">
                                <h6 class="section-title d-flex align-items-center">
                                    <i class="material-symbols-rounded me-2" style="color: #5e72e4;">calendar_today</i>
                                    Current Academic Year
                                </h6>

                                <div class="row">
                                    <div class="col-md-12 text-center mb-4">
                                        @if ($setting->current_session)
                                            <div class="current-year-badge">
                                                <i class="material-symbols-rounded me-1">school</i>
                                                {{ $setting->current_session }}
                                            </div>
                                            <p class="text-muted mt-2">Active Academic Year</p>
                                        @else
                                            <div class="text-muted">
                                                <i class="material-symbols-rounded"
                                                    style="font-size: 3rem; color: #dee2e6;">calendar_today</i>
                                                <p>No academic year set</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="info-item">
                                            <div class="info-label">Academic Year Period</div>
                                            <div class="info-value {{ empty($setting->current_session) ? 'empty' : '' }}">
                                                {{ $setting->current_session ?? 'Not configured' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Academic Calendar -->
                            <div class="info-section">
                                <h6 class="section-title d-flex align-items-center">
                                    <i class="material-symbols-rounded me-2" style="color: #5e72e4;">event_note</i>
                                    Academic Calendar
                                </h6>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-label">Academic Year Starts</div>
                                            <div
                                                class="info-value {{ empty($setting->session_start_month) ? 'empty' : '' }}">
                                                {{ $setting->session_start_month ?? 'Not set' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-label">Academic Year Ends</div>
                                            <div class="info-value {{ empty($setting->session_end_month) ? 'empty' : '' }}">
                                                {{ $setting->session_end_month ?? 'Not set' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-label">Terms/Semesters per Year</div>
                                            <div class="info-value {{ empty($setting->terms_per_year) ? 'empty' : '' }}">
                                                @if ($setting->terms_per_year)
                                                    @switch($setting->terms_per_year)
                                                        @case('2')
                                                            2 Semesters
                                                        @break

                                                        @case('3')
                                                            3 Terms
                                                        @break

                                                        @case('4')
                                                            4 Quarters
                                                        @break

                                                        @default
                                                            {{ $setting->terms_per_year }} Terms
                                                    @endswitch
                                                @else
                                                    Not set
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-label">Calendar Status</div>
                                            <div class="info-value">
                                                @if ($setting->current_session && $setting->session_start_month && $setting->session_end_month)
                                                    <span class="status-active">
                                                        <i class="material-symbols-rounded me-1"
                                                            style="font-size: 1rem;">check_circle</i>
                                                        Fully Configured
                                                    </span>
                                                @else
                                                    <span class="status-inactive">
                                                        <i class="material-symbols-rounded me-1"
                                                            style="font-size: 1rem;">radio_button_unchecked</i>
                                                        Partially Configured
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- School Hours -->
                            <div class="info-section">
                                <h6 class="section-title d-flex align-items-center">
                                    <i class="material-symbols-rounded me-2" style="color: #5e72e4;">schedule</i>
                                    School Hours
                                </h6>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-label">School Start Time</div>
                                            <div
                                                class="info-value {{ empty($setting->school_start_time) ? 'empty' : '' }}">
                                                @if ($setting->school_start_time)
                                                    {{ date('h:i A', strtotime($setting->school_start_time)) }}
                                                @else
                                                    Not set
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-label">School End Time</div>
                                            <div class="info-value {{ empty($setting->school_end_time) ? 'empty' : '' }}">
                                                @if ($setting->school_end_time)
                                                    {{ date('h:i A', strtotime($setting->school_end_time)) }}
                                                @else
                                                    Not set
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="info-item">
                                            <div class="info-label">Daily Hours</div>
                                            <div class="info-value">
                                                @if ($setting->school_start_time && $setting->school_end_time)
                                                    @php
                                                        $start = new DateTime($setting->school_start_time);
                                                        $end = new DateTime($setting->school_end_time);
                                                        $interval = $start->diff($end);
                                                        $hours = $interval->h + $interval->i / 60;
                                                    @endphp
                                                    {{ number_format($hours, 1) }} hours
                                                    ({{ date('h:i A', strtotime($setting->school_start_time)) }} -
                                                    {{ date('h:i A', strtotime($setting->school_end_time)) }})
                                                @else
                                                    <span class="empty">School hours not configured</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Academic Year Timeline -->
                            <div class="info-section">
                                <h6 class="section-title d-flex align-items-center">
                                    <i class="material-symbols-rounded me-2" style="color: #5e72e4;">timeline</i>
                                    Academic Timeline
                                </h6>

                                <div class="row">
                                    <div class="col-md-12">
                                        @if ($setting->session_start_month && $setting->session_end_month && $setting->terms_per_year)
                                            <div class="timeline-info">
                                                <p class="text-muted mb-3">Based on your academic calendar configuration:
                                                </p>
                                                <div class="row">
                                                    <div class="col-md-4 text-center">
                                                        <div class="timeline-item">
                                                            <i class="material-symbols-rounded text-success"
                                                                style="font-size: 2rem;">play_arrow</i>
                                                            <h6 class="mt-2">Start</h6>
                                                            <p class="text-muted">{{ $setting->session_start_month }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 text-center">
                                                        <div class="timeline-item">
                                                            <i class="material-symbols-rounded text-primary"
                                                                style="font-size: 2rem;">timeline</i>
                                                            <h6 class="mt-2">Duration</h6>
                                                            <p class="text-muted">
                                                                @switch($setting->terms_per_year)
                                                                    @case('2')
                                                                        2 Semesters
                                                                    @break

                                                                    @case('3')
                                                                        3 Terms
                                                                    @break

                                                                    @case('4')
                                                                        4 Quarters
                                                                    @break

                                                                    @default
                                                                        {{ $setting->terms_per_year }} Terms
                                                                @endswitch
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 text-center">
                                                        <div class="timeline-item">
                                                            <i class="material-symbols-rounded text-danger"
                                                                style="font-size: 2rem;">stop</i>
                                                            <h6 class="mt-2">End</h6>
                                                            <p class="text-muted">{{ $setting->session_end_month }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-center text-muted">
                                                <i class="material-symbols-rounded"
                                                    style="font-size: 3rem; color: #dee2e6;">timeline</i>
                                                <p class="mt-2">Academic timeline will appear here once calendar is
                                                    configured</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row mt-4">
                                <div class=" d-flex col-12 text-center">
                                    <a href="{{ route('admin.setup.settings.index') }}" class="btn-edit-settings">
                                        <i class="material-symbols-rounded me-1">edit</i>
                                        Configure Academic Year
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
