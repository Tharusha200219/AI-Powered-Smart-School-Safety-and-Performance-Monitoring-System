@extends('admin.layouts.app')

@section('content')
    @include('admin.layouts.sidebar')

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        @include('admin.layouts.navbar')

        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="mb-0">Event Management</h4>
                            <p class="text-sm text-secondary mb-0">Manage school events and track attendance via RFID</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.management.events.create') }}" class="btn btn-primary">
                                <i class="material-symbols-rounded text-sm">add</i> Create New Event
                            </a>
                        </div>
                    </div>

                    @include('admin.layouts.flash')

                    <div class="card my-4">
                        <div class="card-header pb-0">
                            <h6>Ongoing & Upcoming Events</h6>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Event Name</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Date</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Time</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Location</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Organizer</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($events as $event)
                                            <tr>
                                                <td>
                                                    <div class="d-flex px-3 py-1">
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm">{{ $event->name }}</h6>
                                                            <p class="text-xs text-secondary mb-0">{{ Str::limit($event->description, 50) }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">{{ $event->event_date->format('M d, Y') }}</p>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">
                                                        @if($event->start_time)
                                                            {{ $event->start_time->format('h:i A') }} - {{ $event->end_time ? $event->end_time->format('h:i A') : 'End' }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </p>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">{{ $event->location ?? 'N/A' }}</p>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <p class="text-xs font-weight-bold mb-0">{{ $event->creator->name }}</p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <a href="{{ route('admin.management.events.attendance', $event->id) }}" class="btn btn-link text-info text-gradient px-3 mb-0" title="Take Attendance">
                                                        <i class="material-symbols-rounded">sensors</i> Scan
                                                    </a>
                                                    <a href="{{ route('admin.management.events.edit', $event->id) }}" class="btn btn-link text-dark px-3 mb-0">
                                                        <i class="material-symbols-rounded">edit</i> Edit
                                                    </a>
                                                    <form action="{{ route('admin.management.events.destroy', $event->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link text-danger text-gradient px-3 mb-0" onclick="return confirm('Are you sure you want to delete this event?')">
                                                            <i class="material-symbols-rounded">delete</i> Delete
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4">
                                                    <p class="text-secondary mb-0">No events found. Create one to get started!</p>
                                                </td>
                                            </tr>
                                        @endforelse
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
