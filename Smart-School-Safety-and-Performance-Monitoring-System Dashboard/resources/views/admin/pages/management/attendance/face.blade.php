@extends('admin.layouts.app')

@section('css')
    @vite(['resources/css/components/utilities.css'])
    <style>
        .camera-container {
            position: relative;
            width: 100%;
            max-width: 640px;
            margin: 0 auto;
        }

        .camera-feed {
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transform: scaleX(-1);
            /* Mirror the camera */
        }

        .face-detection-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            /* No mirror transform - we handle coordinates in JS */
        }

        .face-box {
            position: absolute;
            border: 3px solid #4CAF50;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(76, 175, 80, 0.5);
            animation: pulse-box 1.5s infinite;
        }

        .face-box.unknown {
            border-color: #f44336;
            box-shadow: 0 0 20px rgba(244, 67, 54, 0.5);
        }

        .face-box.recognized {
            border-color: #4CAF50;
            box-shadow: 0 0 20px rgba(76, 175, 80, 0.5);
        }

        @keyframes pulse-box {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .face-label {
            position: absolute;
            bottom: -30px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            font-weight: bold;
        }

        .face-label.recognized {
            background: rgba(76, 175, 80, 0.9);
        }

        .face-label.unknown {
            background: rgba(244, 67, 54, 0.9);
        }

        .confidence-badge {
            position: absolute;
            top: -25px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(33, 150, 243, 0.9);
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: bold;
        }

        .status-indicator {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            border-radius: 20px;
            color: white;
            font-weight: bold;
            z-index: 10;
        }

        .status-success {
            background-color: #28a745;
        }

        .status-error {
            background-color: #dc3545;
        }

        .recognition-status {
            position: absolute;
            bottom: 10px;
            left: 10px;
            right: 10px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            font-weight: bold;
            z-index: 10;
        }

        .recognition-success {
            background: rgba(25, 135, 84, 0.9);
        }

        .recognition-error {
            background: rgba(220, 53, 69, 0.9);
        }

        .recognition-processing {
            background: rgba(255, 193, 7, 0.9);
            color: black;
        }

        .student-info {
            margin-top: 10px;
            font-size: 14px;
        }

        .late-indicator {
            color: #ffc107;
            font-weight: bold;
        }

        .scanning-line {
            position: absolute;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, transparent, #4CAF50, transparent);
            animation: scan 2s linear infinite;
        }

        @keyframes scan {
            0% {
                top: 0;
            }

            100% {
                top: 100%;
            }
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
                            <h4 class="mb-0">Face Recognition Attendance</h4>
                            <p class="text-sm text-secondary mb-0">Real-time facial recognition for attendance</p>
                        </div>
                        <div>
                            <button id="startCamera" class="btn btn-success btn-sm">
                                <i class="material-symbols-rounded text-sm">videocam</i> Start Camera
                            </button>
                            <button id="stopCamera" class="btn btn-danger btn-sm" disabled>
                                <i class="material-symbols-rounded text-sm">videocam_off</i> Stop Camera
                            </button>
                            <a href="{{ route('admin.management.attendance.dashboard') }}"
                                class="btn btn-outline-secondary btn-sm">
                                <i class="material-symbols-rounded text-sm">arrow_back</i> Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Camera Feed</h6>
                        </div>
                        <div class="card-body">
                            <div class="camera-container">
                                <video id="cameraFeed" class="camera-feed" autoplay muted playsinline></video>
                                <canvas id="faceDetectionOverlay" class="face-detection-overlay"></canvas>
                                <div class="scanning-line" id="scanningLine" style="display: none;"></div>
                                <div id="statusIndicator" class="status-indicator status-success" style="display: none;">
                                    Ready
                                </div>
                                <div id="recognitionStatus" class="recognition-status" style="display: none;">
                                    <div id="recognitionText">Initializing...</div>
                                    <div id="studentInfo" class="student-info" style="display: none;"></div>
                                </div>
                            </div>

                            <div class="mt-3 text-center">
                                <button id="captureBtn" class="btn btn-primary" disabled>
                                    <i class="material-symbols-rounded">camera</i> Capture & Recognize
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-8 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Recent Attendance</h6>
                        </div>
                        <div class="card-body">
                            <div id="recentAttendance" class="list-group">
                                <div class="list-group-item text-center text-muted">
                                    No recent attendance records
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
        document.addEventListener('DOMContentLoaded', function() {
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

            let stream = null;
            let isCapturing = false;
            let recognitionInterval = null;
            let isAutoRecognizing = false;
            let lastRecognizedStudent = null;
            let faceBoxData = null;

            // Start camera
            startCameraBtn.addEventListener('click', async function() {
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

                    showStatus('Camera started - Auto recognition active', 'success');
                    showRecognitionStatus('Scanning for faces...', 'processing');

                    // Start automatic recognition
                    startAutoRecognition();

                } catch (error) {
                    console.error('Error accessing camera:', error);
                    showStatus('Camera access denied', 'error');
                    showRecognitionStatus('Camera error', 'error');
                }
            });

            // Stop camera
            stopCameraBtn.addEventListener('click', function() {
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
            });

            // Manual capture (disabled in auto mode)
            captureBtn.addEventListener('click', function() {
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
                    canvas.toBlob(async function(blob) {
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
                        const recent = data.data.slice(0, 5);
                        recentAttendance.innerHTML = recent.map(attendance => `
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${attendance.student.first_name} ${attendance.student.last_name}</strong>
                            <br>
                            <small class="text-muted">${attendance.check_in_time} - ${attendance.method}</small>
                        </div>
                        <span class="badge bg-${attendance.status === 'present' ? 'success' : 'warning'}">${attendance.status}</span>
                    </div>
                `).join('');
                    }
                } catch (error) {
                    console.error('Error updating recent attendance:', error);
                }
            }

            // Load initial recent attendance
            updateRecentAttendance();
        });
    </script>
@endsection
