@extends('admin.layouts.app')

@section('css')
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
                                    <h6 class="mb-0">{{ pageTitle() }}</h6>
                                </div>
                                <div class="col-6 text-end">
                                    @if (checkPermission('admin.management.attendance.dashboard'))
                                        <a href="{{ route('admin.management.attendance.dashboard') }}"
                                            class="btn btn-outline-primary mb-0 me-2">
                                            <i class="material-symbols-rounded text-sm me-1">dashboard</i>Dashboard
                                        </a>
                                    @endif
                                    @if (checkPermission('admin.management.attendance.create'))
                                        <a href="{{ route('admin.management.attendance.create') }}"
                                            class="btn bg-gradient-dark mb-0">
                                            <i class="material-symbols-rounded text-sm me-1">add</i>Manual Entry
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Filters -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="student_filter" class="form-label ">Filter by Student</label>
                                    <select id="student_filter" class="form-select p-2" name="student_filter">
                                        <option value="">All Students</option>
                                        @foreach (\App\Models\Student::active()->orderBy('first_name')->get() as $student)
                                            <option value="{{ $student->student_id }}"
                                                {{ request('student_filter') == $student->student_id ? 'selected' : '' }}>
                                                {{ $student->full_name }} ({{ $student->student_code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="status_filter" class="form-label">Filter by Status</label>
                                    <select id="status_filter" class="form-select p-2" name="status_filter">
                                        <option value="">All Status</option>
                                        <option value="present"
                                            {{ request('status_filter') == 'present' ? 'selected' : '' }}>Present</option>
                                        <option value="absent" {{ request('status_filter') == 'absent' ? 'selected' : '' }}>
                                            Absent</option>
                                        <option value="late" {{ request('status_filter') == 'late' ? 'selected' : '' }}>
                                            Late</option>
                                        <option value="excused"
                                            {{ request('status_filter') == 'excused' ? 'selected' : '' }}>Excused</option>
                                    </select>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="button" id="apply_filters" class="btn btn-primary me-2 mb-0">
                                        <i class="material-symbols-rounded text-sm me-1">filter_list</i>Apply Filters
                                    </button>
                                    <button type="button" id="clear_filters" class="btn btn-outline-secondary mb-0">
                                        <i class="material-symbols-rounded text-sm me-1">clear</i>Clear
                                    </button>
                                </div>
                            </div>
                            <div class="custom-table-responsive">
                                {{ $dataTable->table() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@isset($dataTable)
    {{ $dataTable->scripts(attributes: ['type' => 'module', 'class' => 'table table-bordered']) }}
@endisset

@vite(['resources/css/admin/tables.css', 'resources/js/admin/attendance-table.js'])

{{-- Include common scripts if they exist --}}
@if (file_exists(public_path('build/js/common/show.js')))
    @vite(['resources/js/common/show.js'])
@endif

@if (file_exists(public_path('build/js/common/confirm.js')))
    @vite(['resources/js/common/confirm.js'])
@endif

@if (file_exists(public_path('build/js/common/delete.js')))
    @vite(['resources/js/common/delete.js'])
@endif

@if (file_exists(public_path('build/js/common/edit.js')))
    @vite(['resources/js/common/edit.js'])
@endif
