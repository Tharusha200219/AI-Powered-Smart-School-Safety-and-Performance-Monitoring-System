@extends('admin.layouts.app')

@section('css')
    @vite(['resources/css/components/utilities.css'])
    <style>
        .rfid-page-wrapper {
            background: linear-gradient(135deg, #f0f4ff 0%, #fafbff 100%);
            min-height: 100vh;
        }

        .rfid-terminal-card {
            border: none;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(33, 150, 243, 0.12);
            overflow: hidden;
        }

        .rfid-terminal-card .card-header {
            background: linear-gradient(135deg, #1565C0 0%, #2196F3 100%);
            color: white;
            padding: 20px 24px;
            border: none;
        }

        .rfid-container {
            position: relative;
            width: 100%;
            max-width: 360px;
            margin: 0 auto;
            text-align: center;
            padding: 48px 32px;
            background: #f8faff;
            border-radius: 20px;
            border: 2.5px dashed #90CAF9;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .rfid-container.scanning {
            border-color: #2196F3;
            background: linear-gradient(135deg, #E3F2FD, #f0f8ff);
            box-shadow: 0 0 0 8px rgba(33, 150, 243, 0.08);
        }

        .rfid-container.success {
            border-color: #4CAF50;
            border-style: solid;
            background: linear-gradient(135deg, #E8F5E9, #f0fff4);
            box-shadow: 0 0 0 8px rgba(76, 175, 80, 0.08);
        }

        .rfid-container.error {
            border-color: #f44336;
            border-style: solid;
            background: linear-gradient(135deg, #FFEBEE, #fff5f5);
            box-shadow: 0 0 0 8px rgba(244, 67, 54, 0.08);
        }

        .rfid-icon-wrap {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #E3F2FD, #BBDEFB);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            transition: all 0.4s ease;
        }

        .rfid-container.scanning .rfid-icon-wrap {
            background: linear-gradient(135deg, #2196F3, #1976D2);
            animation: icon-pulse 1.5s ease-in-out infinite;
        }

        .rfid-container.success .rfid-icon-wrap {
            background: linear-gradient(135deg, #4CAF50, #2E7D32);
            animation: none;
        }

        .rfid-container.error .rfid-icon-wrap {
            background: linear-gradient(135deg, #f44336, #c62828);
            animation: shake 0.5s;
        }

        .rfid-icon {
            font-size: 56px;
            color: #2196F3;
            transition: color 0.3s ease;
        }

        .rfid-container.scanning .rfid-icon,
        .rfid-container.success .rfid-icon,
        .rfid-container.error .rfid-icon {
            color: white;
        }

        @@keyframes icon-pulse {

            0%,
            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(33, 150, 243, 0.4);
            }

            50% {
                transform: scale(1.06);
                box-shadow: 0 0 0 16px rgba(33, 150, 243, 0);
            }
        }

        @@keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            10%,
            30%,
            50%,
            70%,
            90% {
                transform: translateX(-6px);
            }

            20%,
            40%,
            60%,
            80% {
                transform: translateX(6px);
            }
        }

        @@keyframes scan-line {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(100%);
            }
        }

        .processing-pulse {
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, transparent, #2196F3, transparent);
            position: absolute;
            bottom: 0;
            left: 0;
            border-radius: 0 0 20px 20px;
            animation: scan-line 1.5s linear infinite;
            display: none;
        }

        .status-badge {
            font-size: 0.85rem;
            font-weight: 600;
            padding: 6px 18px;
            border-radius: 30px;
            margin-bottom: 12px;
            display: inline-block;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .student-details {
            margin-top: 20px;
            padding: 16px;
            background: white;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            display: none;
            text-align: left;
        }

        .recent-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid #f0f0f0;
            margin-bottom: 8px;
            background: white;
            transition: box-shadow 0.2s;
        }

        .recent-item:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
        }

        .recent-item-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #E3F2FD, #BBDEFB);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-right: 12px;
            font-size: 20px;
            color: #2196F3;
        }

        .stat-mini-card {
            border-radius: 16px;
            border: none;
            padding: 16px;
            text-align: center;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
        }
    </style>
@endsection

@section('content')
    @include('admin.layouts.sidebar')

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        @include('admin.layouts.navbar')

        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="mb-0">RFID Attendance</h4>
                            <p class="text-sm text-secondary mb-0">Tap student smart cards to record attendance</p>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <a href="{{ route('admin.management.attendance.dashboard') }}"
                                class="btn btn-outline-secondary btn-sm mb-0">
                                <i class="material-symbols-rounded text-sm">arrow_back</i> Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 mx-auto">
                    <div class="card rfid-terminal-card mb-4">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <i class="material-symbols-rounded" style="font-size:22px;">contactless</i>
                                <div>
                                    <h6 class="mb-0 text-white">RFID Scanner Terminal</h6>
                                    <p class="text-xs mb-0" style="opacity:0.75;">Tap a student smart card on the reader</p>
                                </div>
                            </div>
                            <span class="badge bg-white text-primary fw-semibold px-3 py-2" id="terminalStatusBadge">
                                <i class="material-symbols-rounded align-middle" style="font-size:14px;">wifi</i> Active
                            </span>
                        </div>
                        <div class="card-body p-4">
                            <div class="rfid-container scanning" id="rfidTerminal">
                                <div class="processing-pulse" id="scannerPulse"></div>

                                <div class="rfid-icon-wrap" id="rfidIconWrap">
                                    <i class="material-symbols-rounded rfid-icon" id="rfidIcon">contactless</i>
                                </div>

                                <div id="statusBadge" class="status-badge bg-primary text-white mb-2">
                                    Ready to Scan
                                </div>
                                <h5 id="statusText" class="mb-1 fw-semibold" style="font-size:1rem;">Tap your smart card on
                                    the reader</h5>
                                <p class="text-muted text-sm mb-0" id="subStatusText">Listening for card taps...</p>

                                <div class="student-details" id="studentDetails">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="rfid-icon-wrap me-3" style="width:48px;height:48px;min-width:48px;">
                                            <i class="material-symbols-rounded"
                                                style="font-size:24px;color:white;">person</i>
                                        </div>
                                        <div class="text-start">
                                            <h5 id="studentName" class="mb-0 fw-bold">-</h5>
                                            <div class="d-flex gap-2 text-xs text-secondary mt-1">
                                                <span><i class="material-symbols-rounded align-middle"
                                                        style="font-size:13px;">tag</i> <span
                                                        id="studentCode">-</span></span>
                                                <span><i class="material-symbols-rounded align-middle"
                                                        style="font-size:13px;">school</i> <span
                                                        id="studentClass">-</span></span>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="my-2">
                                    <div class="d-flex justify-content-around text-center">
                                        <div>
                                            <p class="text-xs text-secondary mb-0">Action</p>
                                            <h6 class="mb-0 fw-bold" id="attendanceAction">-</h6>
                                        </div>
                                        <div>
                                            <p class="text-xs text-secondary mb-0">Time</p>
                                            <h6 class="mb-0 fw-bold" id="attendanceTime">-</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-lg-8 mx-auto">
                    <div class="card" style="border-radius:20px;border:none;box-shadow:0 8px 32px rgba(0,0,0,0.07);">
                        <div class="card-header d-flex align-items-center justify-content-between pb-0">
                            <h6 class="mb-0"><i class="material-symbols-rounded align-middle text-primary me-1"
                                    style="font-size:18px;">history</i> Recent Scans</h6>
                            <button class="btn btn-sm btn-outline-primary" onclick="updateRecentAttendance()"
                                style="border-radius:8px;font-size:12px;">
                                <i class="material-symbols-rounded" style="font-size:14px;">refresh</i> Refresh
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="recentAttendance">
                                <div class="text-center py-4 text-muted">
                                    <i class="material-symbols-rounded" style="font-size:40px;opacity:0.3;">nfc</i>
                                    <p class="mt-2 mb-0">No recent scans yet</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cameraFeed = document.getElementById('cameraFeed');
            const faceOverlay = document.getElementById('faceDetectionOverlay');
            const scanningLine = document.getElementById('scanningLine');
            const startCameraBtn = document.getElementById('startCamera');
            const stopCameraBtn = document.getElementById('stopCamera');
            const captureBtn = document.getElementById('captureBtn');
            const statusIndicator = document.getElementById('statusIndicator');
            const recognitionStatus = document.getElementById('recognitionStatus');
            const recognitionText = document.getElementById('recognitionText');
            const studentInfo = document.getElementById('studentInfo');
            const recentAttendance = document.getElementById('recentAttendance');
            const attendanceModeSelect = document.getElementById('attendanceMode');

            let stream = null;
            let isCapturing = false;
            let recognitionInterval = null;
            let rfidInterval = null;
            let isAutoRecognizing = false;
            let isRfidPolling = false;
            let lastRecognizedStudent = null;
            let faceBoxData = null;

            // Start camera
            startCameraBtn.addEventListener('click', async function () {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            width: 640,
                            height: 480,
                            facingMode: 'user'
                        }
                    });

                    cameraFeed.srcObject = stream;
                    startCameraBtn.disabled = true;
                    stopCameraBtn.disabled = false;
                    captureBtn.disabled = true;
                    scanningLine.style.display = 'block';

                    // Setup canvas overlay
                    cameraFeed.onloadedmetadata = () => {
                        faceOverlay.width = cameraFeed.videoWidth;
                        faceOverlay.height = cameraFeed.videoHeight;
                    };

                    showStatus('Camera started', 'success');
                    showRecognitionStatus('Scanning for faces...', 'processing');

                    // Start automatic recognition if mode allows
                    if (attendanceModeSelect.value === 'both' || attendanceModeSelect.value === 'face') {
                        startAutoRecognition();
                    }

                } catch (error) {
                    console.error('Error accessing camera:', error);
                    showStatus('Camera access denied', 'error');
                    showRecognitionStatus('Camera error', 'error');
                }
            });

            // Stop camera
            stopCameraBtn.addEventListener('click', function () {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                    cameraFeed.srcObject = null;
                    stream = null;
                }

                // Stop automatic recognition
                stopAutoRecognition();
                clearFaceOverlay();
                scanningLine.style.display = 'none';

                startCameraBtn.disabled = false;
                stopCameraBtn.disabled = true;
                captureBtn.disabled = true;

                showStatus('Camera stopped', 'error');
                hideRecognitionStatus();

                // Keep RFID polling if the mode is rfid or both
                if (attendanceModeSelect.value === 'both' || attendanceModeSelect.value === 'rfid') {
                    showStatus('RFID Scanner Active', 'success');
                    showRecognitionStatus('Waiting for RFID Tag...', 'processing');
                }
            });

            // Manual capture (disabled in auto mode)
            captureBtn.addEventListener('click', function () {
                showStatus('Manual capture disabled - Auto recognition active', 'error');
            });

            // Start automatic recognition
            function startAutoRecognition() {
                if (recognitionInterval) {
                    clearInterval(recognitionInterval);
                }

                // Capture and recognize every 500ms for real-time response
                recognitionInterval = setInterval(async () => {
                    if (!isAutoRecognizing && stream && cameraFeed.readyState === 4) {
                        await performAutoRecognition();
                    }
                }, 500);
            }

            // Stop automatic recognition
            function stopAutoRecognition() {
                if (recognitionInterval) {
                    clearInterval(recognitionInterval);
                    recognitionInterval = null;
                }
                isAutoRecognizing = false;
            }

            // Track last face position for smooth tracking
            let lastFaceBox = null;
            let faceBoxVisible = false;

            // Draw face box on canvas (coordinates are mirrored since video is mirrored)
            function drawFaceBox(name, confidence, isRecognized, bbox = null) {
                const ctx = faceOverlay.getContext('2d');
                ctx.clearRect(0, 0, faceOverlay.width, faceOverlay.height);

                // Calculate scale factor between video and canvas
                const scaleX = faceOverlay.width / cameraFeed.videoWidth;
                const scaleY = faceOverlay.height / cameraFeed.videoHeight;

                let boxWidth, boxHeight, x, y;

                if (bbox && bbox.width && bbox.height) {
                    // Use actual bbox from API - scale and mirror it
                    boxWidth = bbox.width * scaleX;
                    boxHeight = bbox.height * scaleY;
                    // Mirror the x coordinate since video is mirrored with CSS
                    x = faceOverlay.width - (bbox.x * scaleX) - boxWidth;
                    y = bbox.y * scaleY;

                    // Add some padding around the face
                    const padding = 20;
                    x -= padding;
                    y -= padding;
                    boxWidth += padding * 2;
                    boxHeight += padding * 2;
                } else {
                    // Default center position if no bbox provided
                    boxWidth = 180;
                    boxHeight = 220;
                    x = (faceOverlay.width - boxWidth) / 2;
                    y = (faceOverlay.height - boxHeight) / 2 - 20;
                }

                // Ensure box stays within canvas bounds
                x = Math.max(10, Math.min(x, faceOverlay.width - boxWidth - 10));
                y = Math.max(30, Math.min(y, faceOverlay.height - boxHeight - 40));

                // Store for tracking
                lastFaceBox = {
                    x,
                    y,
                    width: boxWidth,
                    height: boxHeight,
                    name,
                    confidence,
                    isRecognized
                };
                faceBoxVisible = true;

                // Draw face box
                ctx.strokeStyle = isRecognized ? '#4CAF50' : '#f44336';
                ctx.lineWidth = 3;
                ctx.shadowColor = isRecognized ? 'rgba(76, 175, 80, 0.5)' : 'rgba(244, 67, 54, 0.5)';
                ctx.shadowBlur = 20;

                // Rounded rectangle
                const radius = 10;
                ctx.beginPath();
                ctx.moveTo(x + radius, y);
                ctx.lineTo(x + boxWidth - radius, y);
                ctx.quadraticCurveTo(x + boxWidth, y, x + boxWidth, y + radius);
                ctx.lineTo(x + boxWidth, y + boxHeight - radius);
                ctx.quadraticCurveTo(x + boxWidth, y + boxHeight, x + boxWidth - radius, y + boxHeight);
                ctx.lineTo(x + radius, y + boxHeight);
                ctx.quadraticCurveTo(x, y + boxHeight, x, y + boxHeight - radius);
                ctx.lineTo(x, y + radius);
                ctx.quadraticCurveTo(x, y, x + radius, y);
                ctx.closePath();
                ctx.stroke();

                // Draw corner markers
                ctx.shadowBlur = 0;
                ctx.lineWidth = 4;
                const cornerLength = 20;

                // Top-left
                ctx.beginPath();
                ctx.moveTo(x, y + cornerLength);
                ctx.lineTo(x, y);
                ctx.lineTo(x + cornerLength, y);
                ctx.stroke();

                // Top-right
                ctx.beginPath();
                ctx.moveTo(x + boxWidth - cornerLength, y);
                ctx.lineTo(x + boxWidth, y);
                ctx.lineTo(x + boxWidth, y + cornerLength);
                ctx.stroke();

                // Bottom-left
                ctx.beginPath();
                ctx.moveTo(x, y + boxHeight - cornerLength);
                ctx.lineTo(x, y + boxHeight);
                ctx.lineTo(x + cornerLength, y + boxHeight);
                ctx.stroke();

                // Bottom-right
                ctx.beginPath();
                ctx.moveTo(x + boxWidth - cornerLength, y + boxHeight);
                ctx.lineTo(x + boxWidth, y + boxHeight);
                ctx.lineTo(x + boxWidth, y + boxHeight - cornerLength);
                ctx.stroke();

                // Draw confidence badge at top
                if (confidence > 0) {
                    const confText = `${Math.round(confidence * 100)}% Match`;
                    ctx.font = 'bold 12px Arial';
                    const textWidth = ctx.measureText(confText).width;
                    const badgeX = x + (boxWidth - textWidth - 16) / 2;
                    const badgeY = y - 25;

                    ctx.fillStyle = 'rgba(33, 150, 243, 0.9)';
                    ctx.beginPath();
                    ctx.roundRect(badgeX, badgeY, textWidth + 16, 20, 10);
                    ctx.fill();

                    ctx.fillStyle = 'white';
                    ctx.fillText(confText, badgeX + 8, badgeY + 14);
                }

                // Draw name label at bottom
                if (name) {
                    ctx.font = 'bold 14px Arial';
                    const nameWidth = ctx.measureText(name).width;
                    const labelX = x + (boxWidth - nameWidth - 20) / 2;
                    const labelY = y + boxHeight + 8;

                    ctx.fillStyle = isRecognized ? 'rgba(76, 175, 80, 0.9)' : 'rgba(244, 67, 54, 0.9)';
                    ctx.beginPath();
                    ctx.roundRect(labelX, labelY, nameWidth + 20, 24, 4);
                    ctx.fill();

                    ctx.fillStyle = 'white';
                    ctx.fillText(name, labelX + 10, labelY + 17);
                }
            }

            function clearFaceOverlay() {
                const ctx = faceOverlay.getContext('2d');
                ctx.clearRect(0, 0, faceOverlay.width, faceOverlay.height);
                faceBoxVisible = false;
                lastFaceBox = null;
            }

            // Perform automatic recognition
            async function performAutoRecognition() {
                if (isAutoRecognizing) return;

                if (attendanceModeSelect.value === 'rfid') {
                    isAutoRecognizing = false;
                    return; // Skip face recognition in RFID-only mode
                }

                isAutoRecognizing = true;

                try {
                    // Capture frame from video (flip horizontally to match mirror view)
                    const canvas = document.createElement('canvas');
                    // Use higher resolution for better accuracy
                    canvas.width = cameraFeed.videoWidth;
                    canvas.height = cameraFeed.videoHeight;
                    const ctx = canvas.getContext('2d');

                    // Flip horizontally to send non-mirrored image to API
                    ctx.translate(canvas.width, 0);
                    ctx.scale(-1, 1);
                    ctx.drawImage(cameraFeed, 0, 0);

                    // Convert to blob with higher quality
                    canvas.toBlob(async function (blob) {
                        const formData = new FormData();
                        formData.append('image', blob, 'auto_capture.jpg');

                        // Send to auto recognition API
                        const response = await fetch(
                            '/admin/management/attendance/api/face/auto-recognize', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        const result = await response.json();

                        if (result.success) {
                            // Student recognized and attendance marked
                            showRecognitionStatus('✓ Attendance Marked!', 'success');
                            showStudentInfo(result.student_name, result.status, result.is_late,
                                result.check_in_time);
                            drawFaceBox(result.student_name, result.confidence || 0.95, true, result
                                .bbox);
                            updateRecentAttendance();

                            // Pause recognition for 10 seconds after successful marking
                            stopAutoRecognition();
                            setTimeout(() => {
                                if (stream) {
                                    clearFaceOverlay();
                                    startAutoRecognition();
                                }
                            }, 10000);

                        } else if (result.face_detected === false || result.no_face) {
                            // No face detected - clear overlay immediately
                            showRecognitionStatus('Scanning...', 'processing');
                            clearFaceOverlay();
                            hideStudentInfo();
                        } else if (result.recognized === false && result.face_detected) {
                            // Face detected but not recognized
                            showRecognitionStatus('Face not recognized', 'error');
                            drawFaceBox('Unknown', result.confidence || 0, false, result.bbox);
                            hideStudentInfo();
                        } else if (result.already_marked) {
                            // Already marked today
                            showRecognitionStatus(`Already marked today`, 'success');
                            showStudentInfo(result.student_name, 'present', false,
                                'Already marked');
                            drawFaceBox(result.student_name, result.confidence || 0.95, true, result
                                .bbox);

                            // Brief pause then continue scanning
                            stopAutoRecognition();
                            setTimeout(() => {
                                if (stream) {
                                    startAutoRecognition();
                                }
                            }, 5000);
                        } else {
                            // Other message (still scanning)
                            showRecognitionStatus(result.message || 'Scanning...', 'processing');
                            // Clear if no face detected
                            if (!result.bbox) {
                                clearFaceOverlay();
                            }
                            hideStudentInfo();
                        }
                    }, 'image/jpeg');
                } catch (error) {
                    console.error('Error during auto recognition:', error);
                    showRecognitionStatus('Recognition error', 'error');
                    clearFaceOverlay();
                    hideStudentInfo();
                } finally {
                    isAutoRecognizing = false;
                }
            }

            function showStatus(message, type) {
                statusIndicator.textContent = message;
                statusIndicator.className = `status-indicator status-${type}`;
                statusIndicator.style.display = 'block';

                setTimeout(() => {
                    statusIndicator.style.display = 'none';
                }, 3000);
            }

            function showRecognitionStatus(message, type) {
                recognitionText.textContent = message;
                recognitionStatus.className = `recognition-status recognition-${type}`;
                recognitionStatus.style.display = 'block';
            }

            function hideRecognitionStatus() {
                recognitionStatus.style.display = 'none';
            }

            function showStudentInfo(name, status, isLate, time) {
                let info = `${name} - ${status.toUpperCase()}`;
                if (isLate) {
                    info += ' (LATE)';
                    studentInfo.classList.add('late-indicator');
                } else {
                    studentInfo.classList.remove('late-indicator');
                }
                info += ` - ${time}`;
                studentInfo.textContent = info;
                studentInfo.style.display = 'block';
            }

            function hideStudentInfo() {
                studentInfo.style.display = 'none';
            }

            async function updateRecentAttendance() {
                try {
                    const response = await fetch('/admin/management/attendance/api/today');
                    const data = await response.json();

                    if (data.success && data.data.length > 0) {
                        const recent = data.data.slice(0, 8);
                        const methodIcon = (method) => method === 'nfc' ? 'contactless' : method === 'face' || (method || '').includes('FACE') ? 'face' : 'edit_note';
                        const methodLabel = (method) => method === 'nfc' ? 'RFID' : method === 'face' || (method || '').includes('FACE') ? 'Face' : 'Manual';
                        recentAttendance.innerHTML = recent.map(attendance => `
                                                <div class="recent-item">
                                                    <div class="recent-item-avatar">
                                                        <i class="material-symbols-rounded">${methodIcon(attendance.device_id)}</i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div>
                                                                <strong class="text-sm">${attendance.student.first_name} ${attendance.student.last_name}</strong>
                                                                <small class="d-block text-muted">${attendance.student.student_code} &bull; ${methodLabel(attendance.device_id)}</small>
                                                            </div>
                                                            <div class="text-end">
                                                                <span class="badge bg-gradient-${attendance.status === 'present' ? 'success' : attendance.status === 'late' ? 'warning' : 'secondary'}">${attendance.status}</span>
                                                                <small class="d-block text-muted mt-1">${attendance.check_in_time || '-'}</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            `).join('');
                    } else if (data.success) {
                        recentAttendance.innerHTML = `<div class="text-center py-4 text-muted"><i class="material-symbols-rounded" style="font-size:40px;opacity:0.3;">nfc</i><p class="mt-2 mb-0">No scans yet today</p></div>`;
                    }
                } catch (error) {
                    console.error('Error updating recent attendance:', error);
                }
            }

            // Mode selection handler
            attendanceModeSelect.addEventListener('change', function () {
                const mode = this.value;

                if (mode === 'rfid' || mode === 'both') {
                    if (!rfidInterval) startRfidPolling();
                    if (mode === 'rfid') {
                        if (stream) stopCameraBtn.click();
                        showStatus('RFID Scanner Active', 'success');
                        showRecognitionStatus('Waiting for RFID Tag...', 'processing');
                    }
                } else {
                    stopRfidPolling();
                }

                if (mode === 'face' || mode === 'both') {
                    if (stream && cameraFeed.srcObject) {
                        startAutoRecognition();
                        showStatus('Camera Feed Active', 'success');
                        showRecognitionStatus('Scanning for faces...', 'processing');
                    }
                } else {
                    stopAutoRecognition();
                }
            });

            // ── Terminal state helpers ─────────────────────────────────────────
            function setTerminalState(state, title, subtitle) {
                const terminal = document.getElementById('rfidTerminal');
                const iconWrap = document.getElementById('rfidIconWrap');
                const icon = document.getElementById('rfidIcon');
                const badge = document.getElementById('statusBadge');
                const titleEl = document.getElementById('statusText');
                const subtitleEl = document.getElementById('subStatusText');
                const pulse = document.getElementById('scannerPulse');

                // Remove all state classes
                terminal.classList.remove('scanning', 'success', 'error');

                if (state === 'scanning') {
                    terminal.classList.add('scanning');
                    icon.textContent = 'contactless';
                    badge.className = 'status-badge bg-primary text-white mb-2';
                    badge.textContent = 'Ready to Scan';
                    pulse.style.display = 'none';
                } else if (state === 'success') {
                    terminal.classList.add('success');
                    icon.textContent = 'check_circle';
                    badge.className = 'status-badge bg-success text-white mb-2';
                    badge.textContent = 'Success';
                    pulse.style.display = 'none';
                } else if (state === 'error') {
                    terminal.classList.add('error');
                    icon.textContent = 'wifi_off';
                    badge.className = 'status-badge bg-danger text-white mb-2';
                    badge.textContent = 'Error';
                    pulse.style.display = 'none';
                }

                if (title) titleEl.textContent = title;
                if (subtitle) subtitleEl.textContent = subtitle;
            }

            function showStudentDetail(data, action) {
                if (!data) return;
                const details = document.getElementById('studentDetails');
                document.getElementById('studentName').textContent = data.student || '-';
                document.getElementById('studentCode').textContent = data.code || '-';
                document.getElementById('studentClass').textContent = data.class || '-';
                document.getElementById('attendanceAction').textContent = action === 'check_in' ? '✓ Check In' : '✓ Check Out';
                document.getElementById('attendanceTime').textContent = data.time || data.check_out || '-';

                // Color the action
                const actionEl = document.getElementById('attendanceAction');
                actionEl.className = action === 'check_in' ? 'mb-0 fw-bold text-success' : 'mb-0 fw-bold text-warning';

                details.style.display = 'block';
            }

            function hideStudentDetail() {
                const details = document.getElementById('studentDetails');
                if (details) details.style.display = 'none';
            }
            // ──────────────────────────────────────────────────────────────────

            function startRfidPolling() {
                if (rfidInterval) clearInterval(rfidInterval);

                let consecutiveDeviceErrors = 0;

                rfidInterval = setInterval(async () => {
                    if (isRfidPolling) return;
                    isRfidPolling = true;

                    try {
                        const response = await fetch('/admin/management/attendance/api/nfc/scan', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        // HTTP-level error
                        if (!response.ok) {
                            if (response.status === 404) {
                                consecutiveDeviceErrors = 0;
                                const data = await response.json();
                                setTerminalState('error', 'Student Not Found', data.message || 'Tag is not enrolled');
                                // Reset after 3 seconds
                                setTimeout(() => {
                                    if (attendanceModeSelect.value === 'both' || attendanceModeSelect.value === 'rfid') {
                                        setTerminalState('scanning', 'Tap your smart card on the reader', 'Listening for card taps...');
                                    }
                                }, 3000);
                                return;
                            }

                            const errText = await response.text();
                            console.error('[RFID] Server error ' + response.status + ':', errText);
                            setTerminalState('error', 'Server Error (' + response.status + ')', errText.substring(0, 120));
                            consecutiveDeviceErrors++;
                            return;
                        }

                        const data = await response.json();

                        // ── Device not connected ──────────────────────────────────
                        if (data.device_error) {
                            consecutiveDeviceErrors++;
                            console.error('[RFID] Device error:', data.message, '| Port:', data.port ?? 'unknown');

                            if (consecutiveDeviceErrors === 1 || consecutiveDeviceErrors % 10 === 0) {
                                setTerminalState('error',
                                    '⚠ RFID Reader Offline',
                                    (data.message || 'Arduino not connected') + (data.port ? ' (' + data.port + ')' : '')
                                );
                                document.getElementById('terminalStatusBadge').innerHTML =
                                    '<i class="material-symbols-rounded align-middle" style="font-size:14px;">wifi_off</i> Offline';
                                document.getElementById('terminalStatusBadge').className =
                                    'badge bg-danger fw-semibold px-3 py-2';
                            }
                            return;
                        }

                        // ── No tag detected this poll (normal) ───────────────────
                        if (data.no_tag || (!data.success && !data.device_error)) {
                            consecutiveDeviceErrors = 0;
                            // Restore online badge if it was in error state
                            const badge = document.getElementById('terminalStatusBadge');
                            if (badge.classList.contains('bg-danger')) {
                                badge.innerHTML = '<i class="material-symbols-rounded align-middle" style="font-size:14px;">wifi</i> Active';
                                badge.className = 'badge bg-white text-primary fw-semibold px-3 py-2';
                                setTerminalState('scanning', 'Tap your smart card on the reader', 'Listening for card taps...');
                            }
                            return;
                        }


                        // ── Success ───────────────────────────────────────────────
                        if (data.success) {
                            consecutiveDeviceErrors = 0;
                            const badge = document.getElementById('terminalStatusBadge');
                            badge.innerHTML = '<i class="material-symbols-rounded align-middle" style="font-size:14px;">wifi</i> Active';
                            badge.className = 'badge bg-white text-primary fw-semibold px-3 py-2';

                            const isCheckIn = data.action === 'check_in';
                            setTerminalState('success',
                                isCheckIn ? '✓ Checked In!' : '✓ Checked Out!',
                                (data.data?.student || 'Student') + ' — ' + (data.data?.time || data.data?.check_out || '')
                            );

                            // Show student detail card
                            showStudentDetail(data.data, data.action);
                            updateRecentAttendance();

                            // Pause polling for 5 seconds after a successful scan
                            clearInterval(rfidInterval);
                            setTimeout(() => {
                                if (attendanceModeSelect.value === 'both' || attendanceModeSelect.value === 'rfid') {
                                    setTerminalState('scanning', 'Tap your smart card on the reader', 'Listening for card taps...');
                                    hideStudentDetail();
                                    startRfidPolling();
                                }
                            }, 5000);
                        }

                    } catch (error) {
                        // Network / fetch error (e.g. server down, CORS)
                        consecutiveDeviceErrors++;
                        console.error('[RFID] Fetch error:', error);
                        if (consecutiveDeviceErrors % 5 === 1) {
                            setTerminalState('error', '⚠ Connection Error', 'Cannot reach server: ' + error.message);
                        }
                    } finally {
                        isRfidPolling = false;
                    }
                }, 2000); // Poll every 2 seconds
            }

            function stopRfidPolling() {
                if (rfidInterval) {
                    clearInterval(rfidInterval);
                    rfidInterval = null;
                }
                isRfidPolling = false;
            }

            // Start RFID polling automatically on load since default is BOTH
            startRfidPolling();

            // Load initial recent attendance
            updateRecentAttendance();
        });
    </script>
@endsection