@extends('admin.layouts.app')

@section('title', 'Edit Marks')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="material-icons-outlined me-2">edit</i>
                            Edit Student Marks
                        </h3>
                        <a href="{{ route('admin.management.marks.index') }}" class="btn btn-secondary">
                            <i class="material-icons-outlined me-1">arrow_back</i>
                            Back to List
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.management.marks.update', $mark->mark_id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Student Details (Read-only) -->
                            <div class="alert alert-info mb-4">
                                <h5>Student Details</h5>
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>Student ID:</strong> {{ $mark->student->student_code }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Name:</strong> {{ $mark->student->full_name }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Grade:</strong> Grade {{ $mark->grade_level }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Class:</strong>
                                        {{ $mark->student->schoolClass ? $mark->student->schoolClass->class_name : 'N/A' }}
                                    </div>
                                </div>
                                <hr>
                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <strong>Subject:</strong> {{ $mark->subject->subject_name }}
                                        ({{ $mark->subject->subject_code }})
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Academic Year:</strong> {{ $mark->academic_year }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Term:</strong> Term {{ $mark->term }}
                                    </div>
                                </div>
                            </div>

                            <!-- Marks Entry -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <x-input type="number" name="marks" title="Marks Obtained" :isRequired="true"
                                        :value="old('marks', $mark->marks)" placeholder="Enter marks obtained" attr="step=0.01 min=0" />
                                </div>
                                <div class="col-md-4">
                                    <x-input type="number" name="total_marks" title="Total Marks" :isRequired="true"
                                        :value="old('total_marks', $mark->total_marks)" placeholder="Enter total marks" attr="step=0.01 min=0" />
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

                            <!-- Current Grade Display -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="alert alert-secondary">
                                        <strong>Current Grade:</strong>
                                        <span
                                            class="badge
                                            @if (in_array($mark->grade, ['A+', 'A', 'A-'])) bg-success
                                            @elseif(in_array($mark->grade, ['B+', 'B', 'B-'])) bg-primary
                                            @elseif(in_array($mark->grade, ['C+', 'C', 'C-'])) bg-warning
                                            @else bg-danger @endif"
                                            style="font-size: 1.2em;">
                                            {{ $mark->grade }}
                                        </span>
                                        <span class="ms-3">
                                            <strong>Percentage:</strong> {{ number_format($mark->percentage, 2) }}%
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Remarks -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <x-input type="textarea" name="remarks" title="Remarks (Optional)" :isRequired="false"
                                        :value="old('remarks', $mark->remarks)" placeholder="Enter remarks..." attr="rows=3 maxlength=500" />
                                    <small class="form-text text-muted ms-2">Maximum 500 characters</small>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="material-icons-outlined me-1">save</i>
                                        Update Marks
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
            // Calculate percentage on marks change
            function calculatePercentage() {
                const marks = parseFloat($('#marks').val()) || 0;
                const totalMarks = parseFloat($('#total_marks').val()) || 0;

                if (totalMarks > 0) {
                    const percentage = (marks / totalMarks) * 100;
                    $('#percentage_display').val(percentage.toFixed(2) + '%');
                } else {
                    $('#percentage_display').val('');
                }
            }

            $('#marks, #total_marks').on('input', calculatePercentage);

            // Calculate initial percentage
            calculatePercentage();
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
