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
                                            <x-input name="class_code" title="Class Code" :isRequired="true"
                                                :value="old('class_code', $class->class_code ?? '')" />
                                        </div>

                                        <div class="col-md-6">
                                            <x-input name="class_name" title="Class Name" :isRequired="true"
                                                :value="old('class_name', $class->class_name ?? '')" />
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <x-input name="grade_level" type="select" title="Grade Level" :isRequired="true"
                                                placeholder="Select Grade Level" :options="collect(range(1, 12))
                                                    ->mapWithKeys(fn($i) => ['Grade ' . $i => 'Grade ' . $i])
                                                    ->toArray()" :value="old('grade_level', $class->grade_level ?? '')" />
                                        </div>

                                        <div class="col-md-6">
                                            <x-input name="section" title="Section" placeholder="e.g., A, B, C"
                                                :value="old('section', $class->section ?? '')" />
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <x-input name="academic_year" title="Academic Year" :isRequired="true"
                                                placeholder="e.g., 2025" :value="old('academic_year', $class->academic_year ?? date('Y'))" />
                                        </div>

                                        <div class="col-md-6">
                                            <x-input name="class_teacher_id" type="select" title="Class Teacher"
                                                placeholder="Select Class Teacher" :options="collect($teachers)
                                                    ->mapWithKeys(
                                                        fn($teacher) => [
                                                            $teacher->id =>
                                                                $teacher->first_name . ' ' . $teacher->last_name,
                                                        ],
                                                    )
                                                    ->toArray()" :value="old('class_teacher_id', $class->class_teacher_id ?? '')" />
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
                                            <x-input name="room_number" title="Room Number" placeholder="e.g., 101, A-205"
                                                :value="old('room_number', $class->room_number ?? '')" />
                                        </div>

                                        <div class="col-md-6">
                                            <x-input name="capacity" type="number" title="Capacity"
                                                placeholder="Maximum students" attr="min='1' max='100'"
                                                :value="old('capacity', $class->capacity ?? '')" />
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <x-input name="status" type="select" title="Status" :options="['1' => 'Active', '0' => 'Inactive']"
                                                :value="old('status', $class->status ?? '1')" />
                                        </div>

                                        <div class="col-md-6">
                                            <x-input name="description" type="textarea" title="Description"
                                                placeholder="Additional information about the class" attr="rows='3'"
                                                :value="old('description', $class->description ?? '')" />
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
