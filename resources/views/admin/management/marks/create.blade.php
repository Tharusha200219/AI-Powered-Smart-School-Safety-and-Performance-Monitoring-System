@extends('admin.layouts.app')

@section('title', 'Add Marks')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="material-icons-outlined me-2">add_circle</i>
                            Add Student Marks
                        </h3>
                        <a href="{{ route('admin.management.marks.index') }}" class="btn btn-secondary">
                            <i class="material-icons-outlined me-1">arrow_back</i>
                            Back to List
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.management.marks.store') }}" method="POST" id="marksForm">
                            @csrf

                            <!-- Student Selection -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <x-input type="select" name="student_id" title="Select Student" :isRequired="true"
                                        :value="old('student_id', request('student_id'))" placeholder="-- Select Student --" :options="$students
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
                                            ->toArray()" />
                                </div>
                            </div>

                            <!-- Student Details (shown after selection) -->
                            <div id="studentDetails" style="display: none;" class="mb-4">
                                <div class="alert alert-info">
                                    <h5>Student Details</h5>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>Student ID:</strong> <span id="display_student_code"></span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Name:</strong> <span id="display_full_name"></span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Grade:</strong> <span id="display_grade_level"></span>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Class:</strong> <span id="display_class_name"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Subject Selection (populated after student selection) -->
                            <div class="row mb-4" id="subjectSection" style="display: none;">
                                <div class="col-md-6">
                                    <x-input type="select" name="subject_id" title="Select Subject" :isRequired="true"
                                        :value="old('subject_id')" placeholder="-- Select Subject --" :options="[]"
                                        attr="id=subject_id" />
                                    <small class="form-text text-muted ms-2">Only subjects enrolled by this student are
                                        shown.</small>
                                </div>
                            </div>

                            <!-- Academic Details -->
                            <div class="row mb-4" id="markDetailsSection" style="display: none;">
                                <div class="col-md-4">
                                    <x-input type="select" name="academic_year" title="Academic Year" :isRequired="true"
                                        :value="old('academic_year', $currentAcademicYear)" :options="array_combine($academicYears, $academicYears)" />
                                </div>
                                <div class="col-md-4">
                                    <x-input type="select" name="term" title="Term" :isRequired="true"
                                        :value="old('term')" placeholder="-- Select Term --" :options="$terms" />
                                </div>
                            </div>

                            <!-- Marks Entry -->
                            <div class="row mb-4" id="marksEntrySection" style="display: none;">
                                <div class="col-md-4">
                                    <x-input type="number" name="marks" title="Marks Obtained" :isRequired="true"
                                        :value="old('marks')" placeholder="Enter marks obtained" attr="step=0.01 min=0" />
                                </div>
                                <div class="col-md-4">
                                    <x-input type="number" name="total_marks" title="Total Marks" :isRequired="true"
                                        :value="old('total_marks', 100)" placeholder="Enter total marks" attr="step=0.01 min=0" />
                                </div>
                                <div class="col-md-4">
                                    <div class="mt-2">
                                        <small class="text-xs">Percentage</small>
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
                                    <x-input type="textarea" name="remarks" title="Remarks (Optional)" :isRequired="false"
                                        :value="old('remarks')" placeholder="Enter remarks..." attr="rows=3 maxlength=500" />
                                    <small class="form-text text-muted ms-2">Maximum 500 characters</small>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="material-icons-outlined me-1">save</i>
                                        Save Marks
                                    </button>
                                    <a href="{{ route('admin.management.marks.index') }}" class="btn btn-secondary">
                                        <i class="material-icons-outlined me-1">cancel</i>
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                        error: function() {
                            notificationManager.error('Error',
                                'Error fetching student details. Please try again.');
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
