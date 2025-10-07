@extends('admin.layouts.app')

@section('title', __('Attendance Records'))

@section('css')
    @vite('resources/css/admin/tables.css')
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
                                    <a href="{{ route('admin.management.attendance.dashboard') }}"
                                        class="btn btn-outline-primary mb-0 me-2">
                                        <i class="material-symbols-rounded text-sm me-1">dashboard</i>{{ __('Dashboard') }}
                                    </a>
                                    <a href="{{ route('admin.management.attendance.create') }}"
                                        class="btn bg-gradient-dark mb-0">
                                        <i class="material-symbols-rounded text-sm me-1">add</i>{{ __('Manual Entry') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Filters -->
                            <div class="mb-4">
                                <form method="GET" action="{{ route('admin.management.attendance.index') }}">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <div class="input-group input-group-outline">
                                                <label class="form-label">{{ __('Date') }}</label>
                                                <input type="date" class="form-control" name="date"
                                                    value="{{ request('date', $date) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group input-group-outline">
                                                <select class="form-control" name="status">
                                                    <option value="">{{ __('All Status') }}</option>
                                                    <option value="present"
                                                        {{ request('status') === 'present' ? 'selected' : '' }}>
                                                        {{ __('Present') }}</option>
                                                    <option value="absent"
                                                        {{ request('status') === 'absent' ? 'selected' : '' }}>
                                                        {{ __('Absent') }}</option>
                                                    <option value="late"
                                                        {{ request('status') === 'late' ? 'selected' : '' }}>
                                                        {{ __('Late') }}</option>
                                                    <option value="excused"
                                                        {{ request('status') === 'excused' ? 'selected' : '' }}>
                                                        {{ __('Excused') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group input-group-outline">
                                                <select class="form-control" name="class_id">
                                                    <option value="">{{ __('All Classes') }}</option>
                                                    @foreach (\App\Models\SchoolClass::all() as $class)
                                                        <option value="{{ $class->class_id }}"
                                                            {{ request('class_id') == $class->class_id ? 'selected' : '' }}>
                                                            {{ $class->class_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn bg-gradient-dark mb-0 me-2">
                                                <i class="material-symbols-rounded text-sm me-1">search</i>{{ __('Filter') }}
                                            </button>
                                            <a href="{{ route('admin.management.attendance.index') }}"
                                                class="btn btn-outline-secondary mb-0">
                                                <i class="material-symbols-rounded text-sm me-1">clear</i>{{ __('Clear') }}
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Attendance Table -->
                            <div class="custom-table-responsive">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                {{ __('Student') }}</th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                {{ __('Class') }}</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                {{ __('Date') }}</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                {{ __('Status') }}</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                {{ __('Check In') }}</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                {{ __('Check Out') }}</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                {{ __('Duration') }}</th>
                                            <th class="text-secondary opacity-7"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($attendances as $attendance)
                                            <tr>
                                                <td>
                                                    <div class="d-flex px-3 py-1">
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm">{{ $attendance->student->first_name }}
                                                                {{ $attendance->student->last_name }}</h6>
                                                            <p class="text-xs text-secondary mb-0">
                                                                {{ $attendance->student->student_code }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">
                                                        {{ $attendance->student->schoolClass->class_name ?? 'N/A' }}</p>
                                                    <p class="text-xs text-secondary mb-0">{{ __('Grade') }}
                                                        {{ $attendance->student->grade_level }}</p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span class="text-secondary text-xs font-weight-bold">
                                                        {{ $attendance->attendance_date->format('M d, Y') }}
                                                    </span>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    @if ($attendance->status === 'present')
                                                        <span
                                                            class="badge badge-sm bg-gradient-success">{{ __('Present') }}</span>
                                                    @elseif($attendance->status === 'absent')
                                                        <span
                                                            class="badge badge-sm bg-gradient-danger">{{ __('Absent') }}</span>
                                                    @elseif($attendance->status === 'late')
                                                        <span
                                                            class="badge badge-sm bg-gradient-warning">{{ __('Late') }}</span>
                                                    @else
                                                        <span
                                                            class="badge badge-sm bg-gradient-info">{{ ucfirst($attendance->status) }}</span>
                                                    @endif
                                                    @if ($attendance->is_late)
                                                        <br><small class="text-warning">{{ __('Late') }}</small>
                                                    @endif
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span class="text-secondary text-xs font-weight-bold">
                                                        {{ $attendance->check_in_time ? $attendance->check_in_time->format('h:i A') : '-' }}
                                                    </span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span class="text-secondary text-xs font-weight-bold">
                                                        {{ $attendance->check_out_time ? $attendance->check_out_time->format('h:i A') : '-' }}
                                                    </span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span class="text-secondary text-xs font-weight-bold">
                                                        {{ $attendance->duration ?? '-' }}
                                                    </span>
                                                </td>
                                                <td class="align-middle">
                                                    @if ($attendance->device_id === 'nfc')
                                                        <span class="badge badge-sm bg-gradient-primary">NFC</span>
                                                    @else
                                                        <span
                                                            class="badge badge-sm bg-gradient-secondary">{{ __('Manual') }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-4">
                                                    <p class="text-secondary mb-0">{{ __('No attendance records found') }}
                                                    </p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if ($attendances->count() > 0)
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="text-sm text-secondary mb-0">
                                            {{ __('Showing') }} {{ $attendances->count() }} {{ __('records') }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
