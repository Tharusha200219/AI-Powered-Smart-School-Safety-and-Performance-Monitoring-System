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
                            <h4 class="mb-0">Live Event Attendance</h4>
                            <p class="text-sm text-secondary mb-0">Event: <strong>{{ $event->name }}</strong> | Date:
                                {{ $event->event_date->format('M d, Y') }}
                            </p>
                        </div>
                        <a href="{{ route('admin.management.events.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="material-symbols-rounded text-sm">arrow_back</i> Back to List
                        </a>
                    </div>

                    <div class="row g-3">
                        {{-- RFID Scan Status Card --}}
                        <div class="col-lg-7">
                            <div class="card h-100">
                                <div
                                    class="card-header bg-gradient-primary d-flex justify-content-between align-items-center py-3">
                                    <h6 class="text-white mb-0">RFID Scanner Status</h6>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="status-dot-active" id="scanStatusDot"></span>
                                        <span class="text-white small fw-bold" id="scanStatusLabel">WAITING FOR SCANS</span>
                                    </div>
                                </div>
                                <div class="card-body text-center py-5">
                                    <div id="scanWaiting">
                                        <div class="rfid-wave-container mb-4 mx-auto">
                                            <div class="rfid-wave rfid-wave-1"></div>
                                            <div class="rfid-wave rfid-wave-2"></div>
                                            <div class="rfid-wave rfid-wave-3"></div>
                                            <i
                                                class="material-symbols-rounded rfid-center-icon text-primary">contactless</i>
                                        </div>
                                        <h5 class="fw-bold">Ready to Scan</h5>
                                        <p class="text-muted">Tap a student's wristband on the reader to record attendance.
                                        </p>
                                    </div>

                                    <div id="scanResult" class="d-none">
                                        <div
                                            class="avatar avatar-xl bg-gradient-success shadow-success border-radius-xl mx-auto mb-3">
                                            <span id="resInitials" class="text-white text-lg fw-bold">?</span>
                                        </div>
                                        <h4 class="fw-bold mb-1" id="resName">Student Name</h4>
                                        <p class="text-muted small mb-3">
                                            <span class="badge bg-light text-dark border me-1" id="resCode">CODE</span>
                                            <span id="resMeta">Grade · Class</span>
                                        </p>
                                        <div class="mb-4">
                                            <span class="badge bg-gradient-success px-4 py-2" style="font-size: 0.9rem;">
                                                <i class="material-symbols-rounded align-middle me-1">check_circle</i>
                                                RECORDED
                                            </span>
                                        </div>
                                        <div class="p-3 bg-light rounded-3 d-inline-block">
                                            <div class="text-xs text-secondary text-uppercase fw-bold mb-1">Time Profiled
                                            </div>
                                            <div class="h5 mb-0" id="resTime">00:00:00</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Attendee List Card --}}
                        <div class="col-lg-5">
                            <div class="card h-100">
                                <div class="card-header pb-0 d-flex justify-content-between">
                                    <h6>Attendees Today</h6>
                                    <span class="badge bg-primary rounded-pill h-fit"
                                        id="attendeeCount">{{ $attendances->count() }}</span>
                                </div>
                                <div class="card-body px-0 pb-2">
                                    <div class="table-responsive p-0" style="max-height: 400px; overflow-y: auto;">
                                        <table class="table align-items-center mb-0">
                                            <thead>
                                                <tr>
                                                    <th
                                                        class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                        Student</th>
                                                    <th
                                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                        Check-in</th>
                                                    <th
                                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                        Check-out</th>
                                                </tr>
                                            </thead>
                                            <tbody id="attendeeList">
                                                @forelse($attendances as $att)
                                                    <tr id="student-{{ $att->student_id }}">
                                                        <td>
                                                            <div class="d-flex px-3 py-1">
                                                                <div class="d-flex flex-column justify-content-center">
                                                                    <h6 class="mb-0 text-xs">{{ $att->student->first_name }}
                                                                        {{ $att->student->last_name }}
                                                                    </h6>
                                                                    <p class="text-xxs text-secondary mb-0">
                                                                        {{ $att->student->student_code }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <span class="text-secondary text-xs font-weight-bold"
                                                                id="check-in-{{ $att->student_id }}">{{ $att->check_in_time->format('h:i:s A') }}</span>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <span class="text-secondary text-xs font-weight-bold"
                                                                id="check-out-{{ $att->student_id }}">{{ $att->check_out_time ? $att->check_out_time->format('h:i:s A') : '—' }}</span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr id="emptyMsg">
                                                        <td colspan="3" class="text-center py-4">
                                                            <p class="text-secondary text-xs mb-0">No one has scanned yet.</p>
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
            </div>
        </div>
    </main>

    <style>
        .status-dot-active {
            width: 10px;
            height: 10px;
            background-color: #4CAF50;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 8px #4CAF50;
            animation: pulse-green 2s infinite;
        }

        @keyframes pulse-green {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(76, 175, 80, 0.7);
            }

            70% {
                transform: scale(1);
                box-shadow: 0 0 0 10px rgba(76, 175, 80, 0);
            }

            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(76, 175, 80, 0);
            }
        }

        .rfid-wave-container {
            width: 100px;
            height: 100px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .rfid-wave {
            position: absolute;
            width: 100%;
            height: 100%;
            border: 2px solid var(--bs-primary);
            border-radius: 50%;
            opacity: 0;
            animation: rfid-wave-anim 3s infinite;
        }

        .rfid-wave-2 {
            animation-delay: 1s;
        }

        .rfid-wave-3 {
            animation-delay: 2s;
        }

        .rfid-center-icon {
            font-size: 3rem;
            z-index: 2;
            position: relative;
        }

        @keyframes rfid-wave-anim {
            0% {
                transform: scale(0.5);
                opacity: 0;
            }

            50% {
                opacity: 0.5;
            }

            100% {
                transform: scale(1.5);
                opacity: 0;
            }
        }

        .avatar-xl {
            width: 74px;
            height: 74px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let lastScanId = null;
            let pollInterval = setInterval(pollScans, 1000);
            let uiResetTimeout = null;

            async function pollScans() {
                try {
                    const response = await fetch("{{ route('admin.management.events.poll-scans', $event->id) }}");
                    if (!response.ok) return;
                    const data = await response.json();

                    if (data.found && data.scan.scan_id !== lastScanId) {
                        lastScanId = data.scan.scan_id;
                        updateUI(data.scan);
                    }
                } catch (e) {
                    console.error("Polling error", e);
                }
            }

            function updateUI(scan) {
                // Clear any pending reset
                if (uiResetTimeout) {
                    clearTimeout(uiResetTimeout);
                }

                // Flash the scanner status
                const bar = document.getElementById('scanStatusDot');
                const label = document.getElementById('scanStatusLabel');

                if (scan.status === 'success') {
                    bar.className = 'status-dot-active bg-success';
                    label.textContent = scan.message.toUpperCase();
                } else {
                    bar.className = 'status-dot-active bg-danger';
                    label.textContent = scan.message.toUpperCase();
                }

                // Show result card
                const scanWaiting = document.getElementById('scanWaiting');
                const scanResult = document.getElementById('scanResult');
                
                scanWaiting.classList.add('d-none');
                scanResult.classList.remove('d-none');
                
                // Trigger/Re-trigger animation on the result card
                scanResult.classList.remove('animate__animated', 'animate__fadeInDown');
                void scanResult.offsetWidth; // Force reflow
                scanResult.classList.add('animate__animated', 'animate__fadeInDown');

                const resCard = document.getElementById('scanResult');
                const avatar = resCard.querySelector('.avatar');
                const badge = resCard.querySelector('.badge');

                if (scan.status === 'success') {
                    avatar.className = 'avatar avatar-xl bg-gradient-success shadow-success border-radius-xl mx-auto mb-3';
                    badge.className = 'badge bg-gradient-success px-4 py-2';
                    badge.innerHTML = `<i class="material-symbols-rounded align-middle me-1">check_circle</i> ${scan.message.toUpperCase()}`;
                } else {
                    avatar.className = 'avatar avatar-xl bg-gradient-warning shadow-warning border-radius-xl mx-auto mb-3';
                    badge.className = 'badge bg-gradient-warning px-4 py-2';
                    badge.innerHTML = `<i class="material-symbols-rounded align-middle me-1">warning</i> ${scan.message.toUpperCase()}`;
                }

                document.getElementById('resName').textContent = scan.student_name;
                document.getElementById('resCode').textContent = scan.student_code;
                document.getElementById('resMeta').textContent = `Grade ${scan.grade} · ${scan.class}`;
                document.getElementById('resTime').textContent = scan.time;
                document.getElementById('resInitials').textContent = scan.student_name.split(' ').map(n => n[0]).join('').toUpperCase();

                // Update list
                const list = document.getElementById('attendeeList');
                const emptyMsg = document.getElementById('emptyMsg');
                if (emptyMsg) emptyMsg.remove();

                let studentRow = document.getElementById(`student-${scan.student_id}`);

                if (studentRow) {
                    // Update existing row
                    document.getElementById(`check-in-${scan.student_id}`).textContent = scan.check_in;
                    document.getElementById(`check-out-${scan.student_id}`).textContent = scan.check_out || '—';

                    // Re-trigger row animation
                    studentRow.classList.remove('animate__animated', 'animate__fadeInDown', 'bg-light');
                    void studentRow.offsetWidth;
                    studentRow.classList.add('animate__animated', 'animate__fadeInDown', 'bg-light');
                    setTimeout(() => studentRow.classList.remove('bg-light'), 2000);
                } else if (scan.status === 'success') {
                    // Add new row
                    const row = `
                                    <tr id="student-${scan.student_id}" class="animate__animated animate__fadeInDown">
                                        <td>
                                            <div class="d-flex px-3 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-xs">${scan.student_name}</h6>
                                                    <p class="text-xxs text-secondary mb-0">${scan.student_code}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold" id="check-in-${scan.student_id}">${scan.check_in}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold" id="check-out-${scan.student_id}">${scan.check_out || '—'}</span>
                                        </td>
                                    </tr>
                                `;
                    list.insertAdjacentHTML('afterbegin', row);

                    const countEl = document.getElementById('attendeeCount');
                    countEl.textContent = parseInt(countEl.textContent) + 1;
                }

                // Reset back to waiting after 5 seconds
                uiResetTimeout = setTimeout(() => {
                    document.getElementById('scanResult').classList.add('d-none');
                    document.getElementById('scanWaiting').classList.remove('d-none');
                    bar.className = 'status-dot-active';
                    label.textContent = 'WAITING FOR SCANS';
                }, 5000);
            }
        });
    </script>
@endsection