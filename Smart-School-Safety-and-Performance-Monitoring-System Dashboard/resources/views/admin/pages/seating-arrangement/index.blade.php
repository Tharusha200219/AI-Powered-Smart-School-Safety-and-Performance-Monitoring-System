@extends('admin.layouts.app')

@section('content')
    @include('admin.layouts.sidebar')

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        @include('admin.layouts.navbar')

        <div class="container-fluid pt-2">
            <div class="row">
                <div class="col-12">
                    @include('admin.layouts.flash')

                    {{-- API Status Check --}}
                    @if (!$apiStatus)
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="material-symbols-rounded me-2">warning</i>
                            <strong>Seating Arrangement Service Unavailable</strong>
                            <p class="mb-0">The seating arrangement API is not running. To generate seating arrangements:
                            </p>
                            <ol class="mb-0 mt-2">
                                <li>Navigate to: <code>student-seating-arrangement-model</code></li>
                                <li>Run: <code>./start_api.sh</code></li>
                            </ol>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="card my-4">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6 d-flex align-items-center">
                                    <h6 class="mb-0 d-flex align-items-center">
                                        <i class="material-symbols-rounded me-2">event_seat</i>
                                        Seating Arrangements
                                        @if ($apiStatus)
                                            <span class="badge bg-gradient-success badge-sm ms-2">API Online</span>
                                        @else
                                            <span class="badge bg-gradient-danger badge-sm ms-2">API Offline</span>
                                        @endif
                                    </h6>
                                </div>
                                <div class="col-6 text-end">
                                    <a class="btn bg-gradient-primary mb-0 {{ !$apiStatus ? 'disabled' : '' }}"
                                        href="{{ route('admin.seating-arrangement.create') }}"
                                        @if (!$apiStatus) onclick="return confirm('Seating API is not available. Please start the API first.');" @endif>
                                        <i class="material-symbols-rounded text-sm me-1">add</i>
                                        Generate New Arrangement
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="card-body px-0 pb-2">
                            @if ($arrangements->isEmpty())
                                <div class="text-center py-5">
                                    <i class="material-symbols-rounded text-secondary"
                                        style="font-size: 64px;">event_seat</i>
                                    <h5 class="text-secondary mt-3">No Seating Arrangements Yet</h5>
                                    <p class="text-sm text-muted">Create your first seating arrangement to organize
                                        classroom seating.</p>
                                    <a href="{{ route('admin.seating-arrangement.create') }}" class="btn btn-primary mt-3">
                                        <i class="material-symbols-rounded me-1" style="font-size: 18px;">add</i>
                                        Generate Seating Arrangement
                                    </a>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table align-items-center mb-0">
                                        <thead>
                                            <tr>
                                                <th
                                                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                    Class</th>
                                                <th
                                                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                    Academic Year</th>
                                                <th
                                                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                    Term</th>
                                                <th
                                                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                    Seats</th>
                                                <th
                                                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                    Students</th>
                                                <th
                                                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                    Status</th>
                                                <th
                                                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                    Created</th>
                                                <th class="text-secondary opacity-7"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($arrangements as $arrangement)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex px-2 py-1">
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <h6 class="mb-0 text-sm">Grade
                                                                    {{ $arrangement->grade_level }} -
                                                                    {{ $arrangement->section }}</h6>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <p class="text-xs font-weight-bold mb-0">
                                                            {{ $arrangement->academic_year }}</p>
                                                    </td>
                                                    <td>
                                                        <p class="text-xs font-weight-bold mb-0">Term
                                                            {{ $arrangement->term }}</p>
                                                    </td>
                                                    <td>
                                                        <p class="text-xs text-secondary mb-0">
                                                            {{ $arrangement->seats_per_row }} ×
                                                            {{ $arrangement->total_rows }}
                                                            = {{ $arrangement->seats_per_row * $arrangement->total_rows }}
                                                        </p>
                                                    </td>
                                                    <td>
                                                        <p class="text-xs font-weight-bold mb-0">
                                                            {{ $arrangement->seatAssignments->count() }} assigned</p>
                                                    </td>
                                                    <td>
                                                        @if ($arrangement->is_active)
                                                            <span class="badge badge-sm bg-gradient-success">Active</span>
                                                            @if (isset($arrangement->needs_update) && $arrangement->needs_update)
                                                                <br>
                                                                <span class="badge badge-sm bg-gradient-warning mt-1">
                                                                    <i class="material-symbols-rounded"
                                                                        style="font-size: 12px;">warning</i>
                                                                    Marks Changed
                                                                </span>
                                                            @endif
                                                        @else
                                                            <span
                                                                class="badge badge-sm bg-gradient-secondary">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="text-xs text-secondary">{{ $arrangement->created_at->format('M d, Y') }}</span>
                                                    </td>
                                                    <td class="align-middle">
                                                        <a href="{{ route('admin.seating-arrangement.show', $arrangement->id) }}"
                                                            class="btn btn-link text-secondary mb-0 px-2"
                                                            title="View Details">
                                                            <i class="material-symbols-rounded">visibility</i>
                                                        </a>

                                                        <form
                                                            action="{{ route('admin.seating-arrangement.destroy', $arrangement->id) }}"
                                                            method="POST" class="d-inline"
                                                            onsubmit="return confirm('Are you sure you want to delete this seating arrangement?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="btn btn-link text-danger mb-0 px-2"
                                                                title="Delete Arrangement">
                                                                <i class="material-symbols-rounded">delete</i>
                                                            </button>
                                                        </form>

                                                        <div class="dropdown d-inline">
                                                            {{-- <button class="btn btn-link text-secondary mb-0 px-2"
                                                                type="button" id="dropdownMenu{{ $arrangement->id }}"
                                                                data-bs-toggle="dropdown" aria-expanded="false"
                                                                title="More Actions">
                                                                <i class="material-symbols-rounded">more_vert</i>
                                                            </button> --}}
                                                            <ul class="dropdown-menu dropdown-menu-end"
                                                                aria-labelledby="dropdownMenu{{ $arrangement->id }}">
                                                                <li>
                                                                    <a class="dropdown-item"
                                                                        href="{{ route('admin.seating-arrangement.show', $arrangement->id) }}">
                                                                        <i class="material-symbols-rounded me-2"
                                                                            style="font-size: 18px;">visibility</i>
                                                                        View Details
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <form
                                                                        action="{{ route('admin.seating-arrangement.toggle-active', $arrangement->id) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="submit" class="dropdown-item">
                                                                            <i class="material-symbols-rounded me-2"
                                                                                style="font-size: 18px;">
                                                                                {{ $arrangement->is_active ? 'toggle_off' : 'toggle_on' }}
                                                                            </i>
                                                                            {{ $arrangement->is_active ? 'Deactivate' : 'Activate' }}
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                                <li>
                                                                    <hr class="dropdown-divider">
                                                                </li>
                                                                <li>
                                                                    <form
                                                                        action="{{ route('admin.seating-arrangement.destroy', $arrangement->id) }}"
                                                                        method="POST"
                                                                        onsubmit="return confirm('Are you sure you want to delete this seating arrangement?');">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit"
                                                                            class="dropdown-item text-danger">
                                                                            <i class="material-symbols-rounded me-2"
                                                                                style="font-size: 18px;">delete</i>
                                                                            Delete
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Pagination if needed --}}
                                @if ($arrangements->hasPages())
                                    <div class="px-3 mt-3">
                                        {{ $arrangements->links() }}
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        // Ensure dropdowns work properly
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Bootstrap dropdowns manually if needed
            var dropdownElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
            var dropdownList = dropdownElementList.map(function(dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl);
            });
        });
    </script>
@endpush
