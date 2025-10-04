@extends('admin.layouts.app')

@section('title', 'School Information')

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

        .school-logo-display {
            max-height: 120px;
            max-width: 200px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .no-logo-placeholder {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
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
                                    <h6 class="mb-0">School Information</h6>
                                </div>
                                <div class="d-flex flex-column align-items-end col-6 text-end">
                                    <a href="{{ route('admin.setup.settings.index') }}" class="mb-2 btn-edit-settings ">
                                        <i class="material-symbols-rounded me-1">edit</i>
                                        Edit in Settings
                                    </a>
                                    <a class="btn btn-outline-dark mb-0 d-flex align-items-center justify-content-center btn-back-auto"
                                        href="{{ route('admin.dashboard.index') }}">
                                        <i class="material-symbols-rounded me-1 icon-size-md">arrow_back</i>Back to
                                        Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Basic School Information -->
                            <div class="info-section">
                                <h6 class="d-flex align-items-center">
                                    <i class="material-symbols-rounded me-2" style="color: #5e72e4;">school</i>
                                    Basic School Information
                                </h6>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-label">School Name</div>
                                            <div class="info-value {{ empty($setting->school_name) ? 'empty' : '' }}">
                                                {{ $setting->school_name ?? 'Not set' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-label">School Type</div>
                                            <div class="info-value {{ empty($setting->school_type) ? 'empty' : '' }}">
                                                {{ $setting->school_type ?? 'Not set' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-label">Principal Name</div>
                                            <div class="info-value {{ empty($setting->principal_name) ? 'empty' : '' }}">
                                                {{ $setting->principal_name ?? 'Not set' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-label">Vice Principal Name</div>
                                            <div
                                                class="info-value {{ empty($setting->vice_principal_name) ? 'empty' : '' }}">
                                                {{ $setting->vice_principal_name ?? 'Not set' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-label">Established Year</div>
                                            <div class="info-value {{ empty($setting->established_year) ? 'empty' : '' }}">
                                                {{ $setting->established_year ?? 'Not set' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-label">Total Capacity</div>
                                            <div class="info-value {{ empty($setting->total_capacity) ? 'empty' : '' }}">
                                                {{ $setting->total_capacity ? number_format($setting->total_capacity) . ' students' : 'Not set' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="info-item">
                                            <div class="info-label">School Motto</div>
                                            <div class="info-value {{ empty($setting->school_motto) ? 'empty' : '' }}">
                                                {{ $setting->school_motto ?? 'Not set' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="info-section">
                                <h6 class="d-flex align-items-center">
                                    <i class="material-symbols-rounded me-2" style="color: #5e72e4;">contact_phone</i>
                                    Contact Information
                                </h6>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-label">Phone Number</div>
                                            <div class="info-value {{ empty($setting->company_phone) ? 'empty' : '' }}">
                                                {{ $setting->company_phone ?? 'Not set' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <div class="info-label">Email Address</div>
                                            <div class="info-value {{ empty($setting->company_email) ? 'empty' : '' }}">
                                                {{ $setting->company_email ?? 'Not set' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="info-item">
                                            <div class="info-label">Website</div>
                                            <div class="info-value {{ empty($setting->website_url) ? 'empty' : '' }}">
                                                @if ($setting->website_url)
                                                    <a href="{{ $setting->website_url }}" target="_blank"
                                                        class="text-primary">
                                                        {{ $setting->website_url }}
                                                    </a>
                                                @else
                                                    Not set
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Address Information -->
                            <div class="info-section">
                                <h6 class="d-flex align-items-center">
                                    <i class="material-symbols-rounded me-2" style="color: #5e72e4;">location_on</i>
                                    Address Information
                                </h6>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="info-item">
                                            <div class="info-label">Address</div>
                                            <div class="info-value {{ empty($setting->company_address) ? 'empty' : '' }}">
                                                {{ $setting->company_address ?? 'Not set' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row mt-4">
                                <div class="d-flex col-12 text-center">
                                    <a href="{{ route('admin.setup.settings.index') }}" class="btn-edit-settings">
                                        <i class="material-symbols-rounded me-1">edit</i>
                                        Edit School Information
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
