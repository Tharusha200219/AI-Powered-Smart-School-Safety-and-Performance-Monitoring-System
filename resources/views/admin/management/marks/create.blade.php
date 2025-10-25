@extends('admin.layouts.app')

@section('title', 'Add Marks')

@section('css')
    @vite(['resources/css/admin/forms.css', 'resources/css/admin/common-forms.css', 'resources/css/components/utilities.css'])
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
                                    <h6 class="mb-0">{{ __('school.add_student_marks') }}</h6>
                                </div>
                                <div class="col-6 text-end">
                                    <a class="btn btn-outline-dark mb-0 btn-back-auto"
                                        href="{{ route('admin.management.marks.index') }}">
                                        <i
                                            class="material-symbols-rounded me-1 icon-size-md">arrow_back</i>{{ __('common.back') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.management.marks.store') }}" method="POST" id="marksForm">
                                @csrf

                                <!-- Student Selection -->
                                <div class="card mb-4 shadow-sm">
                                    <div class="card-header bg-gradient-primary">
                                        <h6 class="mb-0 d-flex align-items-center text-white">
                                            <i class="material-symbols-rounded me-2 icon-size-sm">person</i>
                                            {{ __('school.select_student') }}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <x-input type="select" name="student_id"
                                                    title="{{ __('school.select_student') }}" :isRequired="true"
                                                    :value="old('student_id', request('student_id'))" placeholder="-- {{ __('school.select_student') }} --"
                                                    :options="$students
                                                        ->mapWithKeys(
                                                            fn($s) => [
                                                                $s->student_id =>
                                                                    $s->student_code .
                                                                    ' - ' .
                                                                    $s->full_name .
                                                                    ' (Grade ' .
                                                                    $s->grade_level .
                                                                    ')',
                                                            ],
                                                        )
                                                        ->toArray()" attr="id=student_id" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Student Details (shown after selection) -->
                                <div id="studentDetails" style="display: none;" class="card mb-4 shadow-sm">
                                    <div class="card-header bg-gradient-success">
                                        <h6 class="mb-0 d-flex align-items-center text-white">
                                            <i class="material-symbols-rounded me-2 icon-size-sm">info</i>
                                            {{ __('school.student_details') }}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="text-muted small">{{ __('school.student_id') }}</label>
                                                <div class="fw-bold" id="display_student_code"></div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="text-muted small">{{ __('common.name') }}</label>
                                                <div class="fw-bold" id="display_full_name"></div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="text-muted small">{{ __('common.grade') }}</label>
                                                <div class="fw-bold" id="display_grade_level"></div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="text-muted small">{{ __('common.class') }}</label>
                                                <div class="fw-bold" id="display_class_name"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Subject Selection (populated after student selection) -->
                                <div class="row mb-4" id="subjectSection" style="display: none;">
                                    <div class="col-md-6">
                                        <x-input type="select" name="subject_id" title="{{ __('common.subject') }}"
                                            :isRequired="true" :value="old('subject_id')"
                                            placeholder="-- {{ __('common.select_subject') }} --" :options="[]"
                                            attr="id=subject_id" />
                                        <small
                                            class="form-text text-muted ms-2">{{ __('school.only_enrolled_subjects_shown') }}</small>
                                    </div>
                                </div>

                                <!-- Academic Details -->
                                <div class="row mb-4" id="markDetailsSection" style="display: none;">
                                    <div class="col-md-4">
                                        <x-input type="select" name="academic_year"
                                            title="{{ __('school.academic_year') }}" :isRequired="true" :value="old('academic_year', $currentAcademicYear)"
                                            :options="array_combine($academicYears, $academicYears)" />
                                    </div>
                                    <div class="col-md-4">
                                        <x-input type="select" name="term" title="{{ __('common.term') }}"
                                            :isRequired="true" :value="old('term')"
                                            placeholder="-- {{ __('common.select_term') }} --" :options="$terms" />
                                    </div>
                                </div>

                                <!-- Marks Entry -->
                                <div class="row mb-4" id="marksEntrySection" style="display: none;">
                                    <div class="col-md-4">
                                        <x-input type="number" name="marks" title="{{ __('school.marks_obtained') }}"
                                            :isRequired="true" :value="old('marks')"
                                            placeholder="{{ __('school.enter_marks_obtained') }}" attr="step=0.01 min=0" />
                                    </div>
                                    <div class="col-md-4">
                                        <x-input type="number" name="total_marks" title="{{ __('school.total_marks') }}"
                                            :isRequired="true" :value="old('total_marks', 100)"
                                            placeholder="{{ __('school.enter_total_marks') }}" attr="step=0.01 min=0" />
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mt-2">
                                            <small class="text-xs">{{ __('common.percentage') }}</small>
                                            <div class="input-group input-group-outline my-1">
                                                <input type="text" id="percentage_display" class="form-control" readonly
                                                    disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Remarks -->
                                <div class="row mb-4" id="remarksSection" style="display: none;">
                                    <div class="col-md-12">
                                        <x-input type="textarea" name="remarks"
                                            title="{{ __('common.remarks') }} ({{ __('common.optional') }})"
                                            :isRequired="false" :value="old('remarks')"
                                            placeholder="{{ __('common.enter_remarks') }}" attr="rows=3 maxlength=500" />
                                        <small
                                            class="form-text text-muted ms-2">{{ __('common.maximum_500_characters') }}</small>
                                    </div>
                                </div>

                                <!-- Form Actions -->
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="material-symbols-rounded me-1">save</i>{{ __('common.save') }}
                                        </button>
                                        <a href="{{ route('admin.management.marks.index') }}" class="btn btn-secondary">
                                            <i class="material-symbols-rounded me-1">cancel</i>{{ __('common.cancel') }}
                                        </a>
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

@push('scripts')
    <script>
        $(document).ready(function() {
            // When student is selected
            $('#student_id').on('change', function() {
                const studentId = $(this).val();

                if (studentId) {
                    // Fetch student details and subjects
                    $.ajax({
                        url: '{{ route('admin.management.marks.student.details') }}',
                        method: 'GET',
                        data: {
                            student_id: studentId
                        },
                        success: function(response) {
                            // Show student details
                            $('#display_student_code').text(response.student_code);
                            $('#display_full_name').text(response.full_name);
                            $('#display_grade_level').text('Grade ' + response.grade_level);
                            $('#display_class_name').text(response.class_name);
                            $('#studentDetails').slideDown();

                            // Populate subjects dropdown
                            const subjectSelect = $('#subject_id');
                            subjectSelect.empty();
                            subjectSelect.append(
                                '<option value="">-- Select Subject --</option>');

                            response.subjects.forEach(function(subject) {
                                subjectSelect.append(
                                    $('<option></option>')
                                    .val(subject.id)
                                    .text(subject.subject_code + ' - ' + subject
                                        .subject_name)
                                );
                            });

                            // Show subject section
                            $('#subjectSection').slideDown();
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX error:', xhr, status, error);
                            alert('{{ __('common.error_fetching_student_details') }}');
                        }
                    });
                } else {
                    // Hide all sections
                    $('#studentDetails, #subjectSection, #markDetailsSection, #marksEntrySection, #remarksSection')
                        .slideUp();
                    $('#subject_id').empty().append('<option value="">-- Select Subject --</option>');
                }
            });

            // When subject is selected
            $('#subject_id').on('change', function() {
                if ($(this).val()) {
                    $('#markDetailsSection, #marksEntrySection, #remarksSection').slideDown();
                } else {
                    $('#markDetailsSection, #marksEntrySection, #remarksSection').slideUp();
                }
            });

            // Calculate percentage on marks change
            $('#marks, #total_marks').on('input', function() {
                const marks = parseFloat($('#marks').val()) || 0;
                const totalMarks = parseFloat($('#total_marks').val()) || 0;

                if (totalMarks > 0) {
                    const percentage = (marks / totalMarks) * 100;
                    $('#percentage_display').val(percentage.toFixed(2) + '%');
                } else {
                    $('#percentage_display').val('');
                }
            });

            // Trigger student details load if student_id is pre-selected
            @if (request('student_id'))
                $('#student_id').trigger('change');
            @endif
        });
    </script>
@endpush

@push('styles')
    <style>
        .required::after {
            content: " *";
            color: red;
        }

        .material-icons-outlined {
            vertical-align: middle;
        }
    </style>
@endpush
