@extends('admin.layouts.app')

@section('content')
    @include('admin.layouts.sidebar')

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        @include('admin.layouts.navbar')

        <div class="container-fluid pt-2">
            <div class="row">
                <div class="col-12">
                    @include('admin.layouts.flash')
                    
                    {{-- Header --}}
                    <div class="card my-4">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6 d-flex align-items-center">
                                    <a href="{{ route('admin.seating-arrangement.index') }}" class="btn btn-sm btn-secondary me-2">
                                        <i class="material-symbols-rounded" style="font-size: 18px;">arrow_back</i>
                                    </a>
                                    <h6 class="mb-0">
                                        <i class="material-symbols-rounded me-2">event_seat</i>
                                        Seating Arrangement Details
                                    </h6>
                                </div>
                                <div class="col-6 text-end">
                                    @if($arrangement->is_active)
                                        <span class="badge bg-gradient-success">Active</span>
                                    @else
                                        <span class="badge bg-gradient-secondary">Inactive</span>
                                    @endif
                                    
                                    {{-- Delete Button --}}
                                    <form action="{{ route('admin.seating-arrangement.destroy', $arrangement->id) }}" 
                                          method="POST" 
                                          class="d-inline-block ms-2"
                                          onsubmit="return confirm('Are you sure you want to delete this seating arrangement? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="material-symbols-rounded" style="font-size: 18px;">delete</i>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Info Cards --}}
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card">
                                <div class="card-body p-3">
                                    <div class="d-flex">
                                        <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                            <i class="material-symbols-rounded opacity-10" style="font-size: 24px;">school</i>
                                        </div>
                                        <div class="ms-3">
                                            <div class="numbers">
                                                <p class="text-sm mb-0 text-capitalize font-weight-bold opacity-7">Class</p>
                                                <h6 class="font-weight-bolder mb-0">Grade {{ $arrangement->grade_level }} - {{ $arrangement->section }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card">
                                <div class="card-body p-3">
                                    <div class="d-flex">
                                        <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                            <i class="material-symbols-rounded opacity-10" style="font-size: 24px;">groups</i>
                                        </div>
                                        <div class="ms-3">
                                            <div class="numbers">
                                                <p class="text-sm mb-0 text-capitalize font-weight-bold opacity-7">Students</p>
                                                <h6 class="font-weight-bolder mb-0">{{ $arrangement->seatAssignments->count() }} Assigned</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card">
                                <div class="card-body p-3">
                                    <div class="d-flex">
                                        <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                            <i class="material-symbols-rounded opacity-10" style="font-size: 24px;">event_seat</i>
                                        </div>
                                        <div class="ms-3">
                                            <div class="numbers">
                                                <p class="text-sm mb-0 text-capitalize font-weight-bold opacity-7">Total Seats</p>
                                                <h6 class="font-weight-bolder mb-0">{{ $arrangement->seats_per_row * $arrangement->total_rows }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card">
                                <div class="card-body p-3">
                                    <div class="d-flex">
                                        <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                            <i class="material-symbols-rounded opacity-10" style="font-size: 24px;">calendar_today</i>
                                        </div>
                                        <div class="ms-3">
                                            <div class="numbers">
                                                <p class="text-sm mb-0 text-capitalize font-weight-bold opacity-7">Academic Year</p>
                                                <h6 class="font-weight-bolder mb-0">{{ $arrangement->academic_year }}</h6>
                                                <small class="text-xs">Term {{ $arrangement->term }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Seating Chart --}}
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="material-symbols-rounded me-2">grid_view</i>
                                Seating Chart
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="classroom-layout">
                                {{-- Teacher's Desk --}}
                                <div class="text-center mb-4 p-3 bg-light border rounded">
                                    <i class="material-symbols-rounded" style="font-size: 32px;">desk</i>
                                    <div class="text-sm font-weight-bold">Teacher's Desk</div>
                                </div>
                                
                                {{-- Seating Grid --}}
                                <div class="seating-grid">
                                    @for($row = 1; $row <= $arrangement->total_rows; $row++)
                                        <div class="seat-row mb-3 d-flex justify-content-center gap-2">
                                            <span class="row-label text-sm font-weight-bold me-2">Row {{ $row }}</span>
                                            @for($seatInRow = 1; $seatInRow <= $arrangement->seats_per_row; $seatInRow++)
                                                @php
                                                    // Calculate the actual seat number (sequential across all rows)
                                                    $actualSeatNumber = (($row - 1) * $arrangement->seats_per_row) + $seatInRow;
                                                    
                                                    // Find assignment by row and actual seat number
                                                    $assignment = $arrangement->seatAssignments->first(function($item) use ($row, $actualSeatNumber) {
                                                        return (int)$item->row_number === (int)$row && (int)$item->seat_number === (int)$actualSeatNumber;
                                                    });
                                                @endphp
                                                
                                                <div class="seat-card {{ $assignment ? 'occupied' : 'empty' }}" 
                                                     style="width: 120px;">
                                                    @if($assignment)
                                                        <div class="card bg-gradient-primary text-white">
                                                            <div class="card-body p-2 text-center">
                                                                <i class="material-symbols-rounded" style="font-size: 20px;">person</i>
                                                                <div class="text-xs font-weight-bold mt-1">
                                                                    {{ $assignment->student->first_name }} {{ $assignment->student->last_name }}
                                                                </div>
                                                                <small class="text-xxs">Seat {{ $seatInRow }}</small>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="card border">
                                                            <div class="card-body p-2 text-center">
                                                                <i class="material-symbols-rounded text-secondary" style="font-size: 20px;">event_seat</i>
                                                                <div class="text-xs text-secondary mt-1">Empty</div>
                                                                <small class="text-xxs text-secondary">Seat {{ $seatInRow }}</small>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endfor
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Student List --}}
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="material-symbols-rounded me-2">list</i>
                                Student Seat Assignments
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Student</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Position</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Average Marks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($arrangement->seatAssignments->sortBy(['row_number', 'seat_number']) as $assignment)
                                            <tr>
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm">
                                                                {{ $assignment->student->first_name }} {{ $assignment->student->last_name }}
                                                            </h6>
                                                            <p class="text-xs text-secondary mb-0">{{ $assignment->student->student_code }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-gradient-info">
                                                        Row {{ $assignment->row_number }}, Seat {{ $assignment->seat_number }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($assignment->student->marks->isNotEmpty())
                                                        <p class="text-xs font-weight-bold mb-0">
                                                            {{ number_format($assignment->student->marks->avg('percentage'), 1) }}%
                                                        </p>
                                                    @else
                                                        <span class="text-xs text-secondary">N/A</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('styles')
<style>
.classroom-layout {
    max-width: 900px;
    margin: 0 auto;
}

.seating-grid {
    margin-top: 2rem;
}

.seat-row {
    margin-bottom: 1rem;
}

.row-label {
    width: 60px;
    display: flex;
    align-items: center;
}

.seat-card {
    transition: transform 0.2s;
}

.seat-card:hover {
    transform: translateY(-2px);
}
</style>
@endpush
