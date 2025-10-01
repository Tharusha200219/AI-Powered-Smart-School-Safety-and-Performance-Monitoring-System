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

        .form-section-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
        }

        /* Reduce spacing for fields with helper text */
        .field-with-helper .input-group-outline {
            margin-bottom: 0.5rem !important;
        }

        .field-with-helper .form-text {
            margin-top: -0.5rem;
            margin-bottom: 1rem;
            font-size: 0.75rem;
            color: #28a745 !important;
            /* Keep green color */
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
                                    <h6 class="mb-0 d-flex align-items-center">
                                        <i class="material-symbols-rounded me-2 icon-size-md">add_circle</i>
                                        {{ isset($class) ? 'Edit Class' : 'Create Class' }}
                                    </h6>
                                </div>
                                <div class="col-6 text-end">
                                    <a class="btn btn-outline-dark mb-0 btn-back-auto"
                                        href="{{ route('admin.management.classes.index') }}">
                                        <i
                                            class="material-symbols-rounded me-1 icon-size-md">arrow_back</i>{{ __('common.back') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.management.classes.enroll') }}" method="POST" id="classForm"
                                enctype="multipart/form-data">
                                @csrf
                                @if (isset($class))
                                    <input type="hidden" name="id" value="{{ $class->id }}">
                                @endif

                                <!-- Basic Information -->
                                <div class="card mb-4 shadow-sm">
                                    <div class="card-header bg-gradient-primary">
                                        <h6 class="mb-0 d-flex align-items-center text-white">
                                            <i class="material-symbols-rounded me-2" style="font-size: 1.2rem;">info</i>
                                            Basic Information
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 field-with-helper">
                                                <x-input name="class_code" title="Class Code" :isRequired="true"
                                                    attr="maxlength='50' readonly style='background-color: #f8f9fa; cursor: not-allowed;'"
                                                    :value="old('class_code', $class->class_code ?? '')" />
                                                @if (!isset($class))
                                                    <small
                                                        class="form-text text-muted">{{ __('common.auto_generated') }}</small>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <x-input name="class_name" title="Class Name" :isRequired="true"
                                                    attr="maxlength='100'" :value="old('class_name', $class->class_name ?? '')" />
                                            </div>
                                        </div>

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
                                                    :value="old('grade_level', $class->grade_level ?? '')" />
                                            </div>
                                            <div class="col-md-4 field-with-helper">
                                                <x-input name="section" title="Section" attr="maxlength='10'"
                                                    :value="old('section', $class->section ?? '')" />
                                                <small class="form-text text-muted" style="padding-left: 8px;">e.g., A, B,
                                                    C</small>
                                            </div>
                                            <div class="col-md-4 field-with-helper">
                                                <x-input name="academic_year" title="Academic Year" :isRequired="true"
                                                    attr="maxlength='10'" :value="old('academic_year', $class->academic_year ?? date('Y'))" />
                                                <small class="form-text text-muted" style="padding-left: 8px;">e.g.,
                                                    2025</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Classroom Information -->
                                <div class="card mb-4 shadow-sm">
                                    <div class="card-header bg-gradient-info">
                                        <h6 class="mb-0 d-flex align-items-center text-white">
                                            <i class="material-symbols-rounded me-2"
                                                style="font-size: 1.2rem;">meeting_room</i>
                                            Classroom Information
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4 field-with-helper">
                                                <x-input name="room_number" title="Room Number" attr="maxlength='50'"
                                                    :value="old('room_number', $class->room_number ?? '')" />
                                                <small class="form-text text-muted" style="padding-left: 8px;">e.g., 101,
                                                    A-205</small>
                                            </div>
                                            <div class="col-md-4 field-with-helper">
                                                <x-input name="capacity" type="number" title="Capacity"
                                                    attr="min='1' max='100'" :value="old('capacity', $class->capacity ?? '')" />
                                                <small class="form-text text-muted" style="padding-left: 8px;">Maximum
                                                    students</small>
                                            </div>
                                            <div class="col-md-4">
                                                <x-input name="class_teacher_id" type="select" title="Class Teacher"
                                                    placeholder="Select Class Teacher" :options="collect($teachers)
                                                        ->mapWithKeys(
                                                            fn($teacher) => [
                                                                $teacher->id =>
                                                                    $teacher->first_name . ' ' . $teacher->last_name,
                                                            ],
                                                        )
                                                        ->toArray()"
                                                    :value="old('class_teacher_id', $class->class_teacher_id ?? '')" />
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <x-input name="status" type="select" title="Status" :isRequired="true"
                                                    :options="['1' => 'Active', '0' => 'Inactive']" :value="old('status', $class->status ?? '1')" />
                                            </div>
                                            <div class="col-md-6 field-with-helper">
                                                <x-input name="description" type="textarea" title="Description"
                                                    attr="rows='3'" :value="old('description', $class->description ?? '')" />
                                                <small class="form-text text-muted" style="padding-left: 8px;">Additional
                                                    information about the
                                                    class</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Subject Assignment -->
                                <div class="card mb-4 shadow-sm">
                                    <div class="card-header bg-gradient-success">
                                        <h6 class="mb-0 d-flex align-items-center text-white">
                                            <i class="material-symbols-rounded me-2" style="font-size: 1.2rem;">subject</i>
                                            Subject Assignment
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach ($subjects as $subject)
                                                <div class="col-md-4 mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="subjects[]"
                                                            value="{{ $subject->id }}" id="subject_{{ $subject->id }}"
                                                            {{ isset($class) && $class->subjects->contains($subject->id) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="subject_{{ $subject->id }}">
                                                            <strong>{{ $subject->subject_name }}</strong>
                                                            <br><small
                                                                class="text-secondary">{{ $subject->category ?? 'N/A' }} -
                                                                {{ $subject->subject_code ?? 'N/A' }}</small>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        @error('subjects')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="card">
                                    <div class="card-body">
                                        <div class="col-12 text-end">
                                            <a href="{{ route('admin.management.classes.index') }}"
                                                class="btn btn-outline-secondary me-2">
                                                <i class="material-symbols-rounded me-1">cancel</i>Cancel
                                            </a>
                                            <button type="button" class="btn btn-outline-warning me-2"
                                                onclick="document.getElementById('classForm').reset(); resetForm();">
                                                <i class="material-symbols-rounded me-1">restart_alt</i>Reset
                                            </button>
                                            <button type="submit" class="btn btn-success">
                                                <i class="material-symbols-rounded me-1">save</i>
                                                {{ isset($class) ? 'Update' : 'Create' }} Class
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
        window.isEditMode = {{ isset($class) ? 'true' : 'false' }};

        document.addEventListener('DOMContentLoaded', function() {
            // Auto-generate class code if empty and not in edit mode
            const classCodeInput = document.querySelector('input[name="class_code"]');
            if (!classCodeInput.value && !window.isEditMode) {
                generateClassCode();
            }

            // Reset form function
            window.resetForm = function() {
                // Reset all form fields
                const form = document.getElementById('classForm');
                form.reset();

                // Re-generate class code if not in edit mode
                if (!window.isEditMode) {
                    generateClassCode();
                }
            };

            function generateClassCode() {
                const currentYear = new Date().getFullYear();
                const randomNum = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
                const classCodeInput = document.querySelector('input[name="class_code"]');
                classCodeInput.value = `CLS${currentYear}${randomNum}`;
            }
        });
    </script>
@endsection
