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
                                        <i class="material-symbols-rounded me-2">subject</i>
                                        {{ isset($subject) ? 'Edit Subject' : 'Create Subject' }}
                                    </h6>
                                </div>
                                <div class="col-6 text-end">
                                    <a class="btn btn-secondary mb-0" href="{{ route('admin.management.subjects.index') }}">
                                        <i class="material-symbols-rounded text-sm me-1">arrow_back</i>
                                        Back to Subjects
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <form action="{{ route('admin.management.subjects.enroll') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @if (isset($subject))
                                    <input type="hidden" name="id" value="{{ $subject->id }}">
                                @endif

                                <!-- Basic Information Section -->
                                <div class="form-section">
                                    <h6>
                                        <i class="material-symbols-rounded me-2" style="color: #5e72e4;">info</i>
                                        Basic Information
                                    </h6>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Subject Code *</label>
                                                <input type="text" name="subject_code" class="form-control"
                                                    value="{{ old('subject_code', $subject->subject_code ?? '') }}"
                                                    required>
                                            </div>
                                            @error('subject_code')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Subject Name *</label>
                                                <input type="text" name="subject_name" class="form-control"
                                                    value="{{ old('subject_name', $subject->subject_name ?? '') }}"
                                                    required>
                                            </div>
                                            @error('subject_name')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Grade Level</label>
                                                <select name="grade_level" class="form-control">
                                                    <option value="">Select Grade Level</option>
                                                    @for ($i = 1; $i <= 12; $i++)
                                                        <option value="Grade {{ $i }}"
                                                            {{ old('grade_level', $subject->grade_level ?? '') == "Grade $i" ? 'selected' : '' }}>
                                                            Grade {{ $i }}
                                                        </option>
                                                    @endfor
                                                    <option value="All Grades"
                                                        {{ old('grade_level', $subject->grade_level ?? '') == 'All Grades' ? 'selected' : '' }}>
                                                        All Grades
                                                    </option>
                                                </select>
                                            </div>
                                            @error('grade_level')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Subject Type</label>
                                                <select name="type" class="form-control">
                                                    <option value="">Select Type</option>
                                                    <option value="Core"
                                                        {{ old('type', $subject->type ?? '') == 'Core' ? 'selected' : '' }}>
                                                        Core
                                                    </option>
                                                    <option value="Elective"
                                                        {{ old('type', $subject->type ?? '') == 'Elective' ? 'selected' : '' }}>
                                                        Elective
                                                    </option>
                                                    <option value="Optional"
                                                        {{ old('type', $subject->type ?? '') == 'Optional' ? 'selected' : '' }}>
                                                        Optional
                                                    </option>
                                                    <option value="Extra-curricular"
                                                        {{ old('type', $subject->type ?? '') == 'Extra-curricular' ? 'selected' : '' }}>
                                                        Extra-curricular
                                                    </option>
                                                </select>
                                            </div>
                                            @error('type')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Academic Details Section -->
                                <div class="form-section">
                                    <h6>
                                        <i class="material-symbols-rounded me-2" style="color: #5e72e4;">school</i>
                                        Academic Details
                                    </h6>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Credits</label>
                                                <input type="number" name="credits" class="form-control"
                                                    value="{{ old('credits', $subject->credits ?? '') }}" min="1"
                                                    max="10" placeholder="Subject credits">
                                            </div>
                                            @error('credits')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Status</label>
                                                <select name="status" class="form-control">
                                                    <option value="1"
                                                        {{ old('status', $subject->status ?? '1') == '1' ? 'selected' : '' }}>
                                                        Active
                                                    </option>
                                                    <option value="0"
                                                        {{ old('status', $subject->status ?? '1') == '0' ? 'selected' : '' }}>
                                                        Inactive
                                                    </option>
                                                </select>
                                            </div>
                                            @error('status')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Description</label>
                                                <textarea name="description" class="form-control" rows="4"
                                                    placeholder="Subject description, curriculum details, learning objectives...">{{ old('description', $subject->description ?? '') }}</textarea>
                                            </div>
                                            @error('description')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="row mt-4">
                                    <div class="col-12 text-end">
                                        <a href="{{ route('admin.management.subjects.index') }}"
                                            class="btn btn-secondary me-2">
                                            <i class="material-symbols-rounded me-1">cancel</i>
                                            Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-symbols-rounded me-1">
                                                {{ isset($subject) ? 'update' : 'add' }}
                                            </i>
                                            {{ isset($subject) ? 'Update Subject' : 'Create Subject' }}
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
