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
                                        <i class="material-symbols-rounded me-2">school</i>
                                        {{ isset($class) ? 'Edit Class' : 'Create Class' }}
                                    </h6>
                                </div>
                                <div class="col-6 text-end">
                                    <a class="btn btn-secondary mb-0" href="{{ route('admin.management.classes.index') }}">
                                        <i class="material-symbols-rounded text-sm me-1">arrow_back</i>
                                        Back to Classes
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <form action="{{ route('admin.management.classes.enroll') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @if (isset($class))
                                    <input type="hidden" name="id" value="{{ $class->id }}">
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
                                                <label class="form-label">Class Code *</label>
                                                <input type="text" name="class_code" class="form-control"
                                                    value="{{ old('class_code', $class->class_code ?? '') }}" required>
                                            </div>
                                            @error('class_code')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Class Name *</label>
                                                <input type="text" name="class_name" class="form-control"
                                                    value="{{ old('class_name', $class->class_name ?? '') }}" required>
                                            </div>
                                            @error('class_name')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Grade Level *</label>
                                                <select name="grade_level" class="form-control" required>
                                                    <option value="">Select Grade Level</option>
                                                    @for ($i = 1; $i <= 12; $i++)
                                                        <option value="Grade {{ $i }}"
                                                            {{ old('grade_level', $class->grade_level ?? '') == "Grade $i" ? 'selected' : '' }}>
                                                            Grade {{ $i }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>
                                            @error('grade_level')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Section</label>
                                                <input type="text" name="section" class="form-control"
                                                    value="{{ old('section', $class->section ?? '') }}"
                                                    placeholder="e.g., A, B, C">
                                            </div>
                                            @error('section')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Academic Year *</label>
                                                <input type="text" name="academic_year" class="form-control"
                                                    value="{{ old('academic_year', $class->academic_year ?? date('Y')) }}"
                                                    placeholder="e.g., 2025" required>
                                            </div>
                                            @error('academic_year')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Class Teacher</label>
                                                <select name="class_teacher_id" class="form-control">
                                                    <option value="">Select Class Teacher</option>
                                                    @foreach ($teachers as $teacher)
                                                        <option value="{{ $teacher->id }}"
                                                            {{ old('class_teacher_id', $class->class_teacher_id ?? '') == $teacher->id ? 'selected' : '' }}>
                                                            {{ $teacher->first_name }} {{ $teacher->last_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @error('class_teacher_id')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Classroom Information Section -->
                                <div class="form-section">
                                    <h6>
                                        <i class="material-symbols-rounded me-2" style="color: #5e72e4;">meeting_room</i>
                                        Classroom Information
                                    </h6>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Room Number</label>
                                                <input type="text" name="room_number" class="form-control"
                                                    value="{{ old('room_number', $class->room_number ?? '') }}"
                                                    placeholder="e.g., 101, A-205">
                                            </div>
                                            @error('room_number')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Capacity</label>
                                                <input type="number" name="capacity" class="form-control"
                                                    value="{{ old('capacity', $class->capacity ?? '') }}" min="1"
                                                    max="100" placeholder="Maximum students">
                                            </div>
                                            @error('capacity')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Status</label>
                                                <select name="status" class="form-control">
                                                    <option value="1"
                                                        {{ old('status', $class->status ?? '1') == '1' ? 'selected' : '' }}>
                                                        Active
                                                    </option>
                                                    <option value="0"
                                                        {{ old('status', $class->status ?? '1') == '0' ? 'selected' : '' }}>
                                                        Inactive
                                                    </option>
                                                </select>
                                            </div>
                                            @error('status')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">Description</label>
                                                <textarea name="description" class="form-control" rows="3"
                                                    placeholder="Additional information about the class">{{ old('description', $class->description ?? '') }}</textarea>
                                            </div>
                                            @error('description')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Subjects Section -->
                                <div class="form-section">
                                    <h6>
                                        <i class="material-symbols-rounded me-2" style="color: #5e72e4;">subject</i>
                                        Subjects
                                    </h6>

                                    <div class="row">
                                        @foreach ($subjects as $subject)
                                            <div class="col-md-4 col-sm-6 mb-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="subjects[]"
                                                        value="{{ $subject->id }}" id="subject_{{ $subject->id }}"
                                                        {{ isset($class) && $class->subjects->contains($subject->id) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="subject_{{ $subject->id }}">
                                                        {{ $subject->subject_name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('subjects')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Submit Buttons -->
                                <div class="row mt-4">
                                    <div class="col-12 text-end">
                                        <a href="{{ route('admin.management.classes.index') }}"
                                            class="btn btn-secondary me-2">
                                            <i class="material-symbols-rounded me-1">cancel</i>
                                            Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-symbols-rounded me-1">
                                                {{ isset($class) ? 'update' : 'add' }}
                                            </i>
                                            {{ isset($class) ? 'Update Class' : 'Create Class' }}
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
