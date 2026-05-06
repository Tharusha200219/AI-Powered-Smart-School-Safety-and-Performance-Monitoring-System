/**
 * Video Threat Detection Module
 * Handles PC camera and ESP32-CAM integration with real-time detection
 */

class VideoThreatDetection {
    constructor(config) {
        this.config = config;
        this.isRunning = false;
        this.videoSource = 'pc'; // 'pc' or 'esp32'
        this.stream = null;
        this.esp32Interval = null;
        
        // Elements
        this.videoElement = document.getElementById('videoElement');
        this.detectionCanvas = document.getElementById('detectionCanvas');
        this.esp32Stream = document.getElementById('esp32Stream');
        this.esp32Canvas = document.getElementById('esp32DetectionCanvas');
        
        // Stats
        this.stats = {
            framesProcessed: 0,
            objectsDetected: 0,
            threatsDetected: 0,
            totalLatency: 0,
            startTime: null
        };
        
        // Detection history
        this.detectionHistory = [];
        
        this.init();
    }

    init() {
        console.log('🚀 Initializing Video Threat Detection System...');
        console.log('Configuration:', {
            apiUrl: this.config.apiUrl,
            routes: this.config.routes
        });

        // Verify DOM elements exist
        console.log('DOM Elements Check:', {
            videoElement: !!this.videoElement,
            detectionCanvas: !!this.detectionCanvas,
            esp32Stream: !!this.esp32Stream,
            esp32Canvas: !!this.esp32Canvas,
            startBtn: !!document.getElementById('startDetectionBtn'),
            stopBtn: !!document.getElementById('stopDetectionBtn')
        });

        this.setupEventListeners();
        this.checkApiStatus();

        console.log('✅ Video Threat Detection System initialized');
        console.log('💡 To test: Click "Start Detection" button and check console for logs');
    }

    setupEventListeners() {
        // Start/Stop buttons
        document.getElementById('startDetectionBtn')?.addEventListener('click', () => this.startDetection());
        document.getElementById('stopDetectionBtn')?.addEventListener('click', () => this.stopDetection());

        // Video source toggle - ONLY switch if not running
        document.querySelectorAll('input[name="videoSource"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                console.log('Video source change requested:', e.target.value, 'isRunning:', this.isRunning);
                if (!this.isRunning) {
                    this.switchVideoSource(e.target.value);
                } else {
                    console.warn('Cannot switch video source while detection is running. Stop detection first.');
                    // Revert radio button to current source
                    document.querySelector(`input[name="videoSource"][value="${this.videoSource}"]`).checked = true;
                    this.showNotification('Stop detection before switching video source', 'warning');
                }
            });
        });

        // ESP32 connection
        document.getElementById('connectEsp32Btn')?.addEventListener('click', () => this.connectEsp32());

        // Clear buttons
        document.getElementById('clearResultsBtn')?.addEventListener('click', () => this.clearResults());
        document.getElementById('clearAlertsBtn')?.addEventListener('click', () => this.clearHistory());
    }

    async checkApiStatus() {
        try {
            console.log('Checking API status at:', this.config.routes.status);
            const response = await fetch(this.config.routes.status);

            if (!response.ok) {
                throw new Error(`API returned status ${response.status}`);
            }

            const data = await response.json();
            console.log('API Status:', data);

            if (data.status === 'active') {
                this.updateStatusIndicator('ready', 'Models loaded and ready');
                this.showNotification('Detection service is ready', 'success');
            } else {
                this.updateStatusIndicator('error', 'API service unavailable');
                this.showNotification('Detection service is not ready', 'warning');
            }
        } catch (error) {
            console.error('API status check failed:', error);
            this.updateStatusIndicator('error', 'Cannot connect to detection service');
            this.showNotification('Cannot connect to detection service. Make sure the ML API is running.', 'error');
        }
    }

    async startDetection() {
        if (this.isRunning) {
            console.warn('⚠️ Detection already running');
            return;
        }

        console.log('🎬 Starting detection...');
        console.log('Video source:', this.videoSource);
        console.log('Current state:', {
            isRunning: this.isRunning,
            videoSource: this.videoSource,
            hasStream: !!this.stream
        });

        try {
            // Set isRunning BEFORE starting camera to prevent race condition
            this.isRunning = true;
            this.stats.startTime = Date.now();
            console.log('✅ isRunning set to true BEFORE camera start');

            if (this.videoSource === 'pc') {
                console.log('📹 Requesting PC camera access...');
                await this.startPcCamera();
            } else {
                console.log('📡 Connecting to ESP32-CAM...');
                await this.startEsp32Stream();
            }

            // Update UI
            const startBtn = document.getElementById('startDetectionBtn');
            const stopBtn = document.getElementById('stopDetectionBtn');
            const statusEl = document.getElementById('detectionStatus');
            const cameraStatusEl = document.getElementById('cameraStatus');

            if (startBtn) startBtn.classList.add('d-none');
            if (stopBtn) stopBtn.classList.remove('d-none');
            if (statusEl) statusEl.textContent = 'Active';
            if (cameraStatusEl) cameraStatusEl.innerHTML = '<span class="text-success text-sm">✓ Camera connected</span>';

            console.log('✅ Detection started successfully - isRunning:', this.isRunning);
            this.showNotification('Detection started successfully', 'success');
        } catch (error) {
            console.error('❌ Failed to start detection:', error);
            this.showNotification('Failed to start detection: ' + error.message, 'error');
            this.isRunning = false;
        }
    }

    async startPcCamera() {
        try {
            this.stream = await navigator.mediaDevices.getUserMedia({
                video: { width: 640, height: 480 }
            });

            this.videoElement.srcObject = this.stream;

            // Wait for video to be ready before processing
            await new Promise((resolve, reject) => {
                this.videoElement.onloadedmetadata = () => {
                    this.videoElement.play()
                        .then(() => {
                            console.log('Video started playing:', {
                                width: this.videoElement.videoWidth,
                                height: this.videoElement.videoHeight
                            });
                            resolve();
                        })
                        .catch(reject);
                };

                // Timeout after 5 seconds
                setTimeout(() => reject(new Error('Video loading timeout')), 5000);
            });

            document.getElementById('noVideoMsg').style.display = 'none';

            // Start processing frames after video is ready
            this.processVideoFrames();
        } catch (error) {
            console.error('Camera error:', error);
            throw new Error('Camera access denied or not available: ' + error.message);
        }
    }

    async startEsp32Stream() {
        const ip = document.getElementById('esp32IpInput').value.trim();
        if (!ip) {
            throw new Error('Please enter ESP32-CAM IP address');
        }

        // Store IP so the snapshot helper can reach /capture
        this._esp32Ip = ip;

        // *** KEY FIX: Do NOT load the MJPEG /stream URL here. ***
        // The ESP32's handleStream() runs a blocking while-loop that monopolises
        // the HTTP server — once the browser opens that persistent connection,
        // server.handleClient() is never called again, so every subsequent
        // fetch('/capture') request times out and detection never fires.
        //
        // Solution: use /capture snapshots for BOTH live display AND detection.
        // processEsp32Frames() fetches a snapshot every 250 ms, draws it into
        // this.esp32Stream.src (giving a ~4 FPS live view), and sends the same
        // frame data to the Python detection API.
        this.esp32Stream.style.display = 'block';
        document.getElementById('noEsp32Msg').style.display = 'none';

        // Start detection + display using snapshot fetch
        this.processEsp32Frames();
    }

    processVideoFrames() {
        if (!this.isRunning) {
            console.error('❌ processVideoFrames called but isRunning is false - this should not happen!');
            console.trace('Call stack:');
            return;
        }

        console.log('✅ processVideoFrames started - isRunning:', this.isRunning);

        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        let frameCount = 0;

        const processFrame = async () => {
            if (!this.isRunning) {
                console.log('Frame processing stopped - isRunning is false');
                return;
            }

            try {
                frameCount++;

                // Validate video dimensions
                if (!this.videoElement.videoWidth || !this.videoElement.videoHeight) {
                    if (frameCount % 10 === 0) {
                        console.warn('Video dimensions not ready, waiting...', {
                            videoWidth: this.videoElement.videoWidth,
                            videoHeight: this.videoElement.videoHeight,
                            readyState: this.videoElement.readyState
                        });
                    }
                    setTimeout(processFrame, 100);
                    return;
                }

                // Log first successful frame capture
                if (frameCount === 1) {
                    console.log('✅ First frame captured successfully:', {
                        width: this.videoElement.videoWidth,
                        height: this.videoElement.videoHeight
                    });
                }

                // Capture frame
                canvas.width = this.videoElement.videoWidth;
                canvas.height = this.videoElement.videoHeight;
                ctx.drawImage(this.videoElement, 0, 0);

                // Convert to base64
                const frameData = canvas.toDataURL('image/jpeg', 0.8).split(',')[1];

                // Validate frame data
                if (!frameData || frameData.length === 0) {
                    console.error('Failed to capture frame data');
                    setTimeout(processFrame, 100);
                    return;
                }

                // Send for processing
                await this.processFrame(frameData);

                // Continue processing - IMPORTANT: Always schedule next frame
                if (this.isRunning) {
                    setTimeout(processFrame, 100); // Process at ~10 FPS
                } else {
                    console.log('Stopping frame processing - isRunning became false');
                }
            } catch (error) {
                console.error('Error in processFrame loop:', error);
                // Continue even on error
                if (this.isRunning) {
                    setTimeout(processFrame, 100);
                }
            }
        };

        // Start processing
        console.log('🎬 Starting frame processing loop...');
        processFrame();
    }

    processEsp32Frames() {
        // Snapshot fetch strategy:
        //  1. fetch /capture  → CORS-enabled single JPEG from ESP32
        //  2. blobUrl         → update this.esp32Stream.src for live display (~4 FPS)
        //  3. base64 frameData → send to Python detection API
        // This keeps the ESP32 HTTP server free to respond to every request
        // (no blocking MJPEG stream connection).

        let prevBlobUrl = null;

        this.esp32Interval = setInterval(async () => {
            if (!this.isRunning || !this._esp32Ip) return;

            try {
                const result = await this._captureEsp32Snapshot();
                if (!result) {
                    console.warn('ESP32 snapshot not ready yet, skipping frame');
                    return;
                }

                const { frameData, blobUrl } = result;

                // Update live display image with the fresh snapshot
                if (blobUrl) {
                    this.esp32Stream.src = blobUrl;
                    // Revoke previous blob URL after a short delay so browser renders it
                    if (prevBlobUrl) {
                        const old = prevBlobUrl;
                        setTimeout(() => URL.revokeObjectURL(old), 500);
                    }
                    prevBlobUrl = blobUrl;
                }

                await this.processFrame(frameData);
            } catch (error) {
                console.error('ESP32 frame processing error:', error);
            }
        }, 250); // ~4 FPS for ESP32
    }

    /**
     * Fetches a single JPEG frame from the ESP32-CAM /capture endpoint.
     * Returns a base64-encoded JPEG string, or null on failure.
     *
     * HOW IT AVOIDS THE CORS CANVAS-TAINT PROBLEM:
     *   1. fetch() with mode:'cors' → ESP32 responds with Access-Control-Allow-Origin:*
     *   2. Convert response to blob → URL.createObjectURL() → a blob: URL (same-origin)
     *   3. Draw blob-URL image onto canvas → canvas is NOT tainted
     *   4. canvas.toDataURL() works perfectly → base64 JPEG sent to Python API
     */
    async _captureEsp32Snapshot() {
        try {
            const response = await fetch(`http://${this._esp32Ip}/capture`, {
                mode: 'cors',
                cache: 'no-cache'
            });

            if (!response.ok) {
                console.warn(`ESP32 /capture returned ${response.status}`);
                return null;
            }

            const blob = await response.blob();
            if (!blob || blob.size === 0) return null;

            // blob: URL is same-origin → canvas drawImage won't taint it
            const blobUrl = URL.createObjectURL(blob);

            return new Promise((resolve) => {
                const img = new Image();
                img.onload = () => {
                    const w = img.naturalWidth || 320;
                    const h = img.naturalHeight || 240;

                    // Store actual frame dimensions for overlay canvas
                    this._esp32FrameWidth  = w;
                    this._esp32FrameHeight = h;

                    const canvas = document.createElement('canvas');
                    canvas.width  = w;
                    canvas.height = h;
                    canvas.getContext('2d').drawImage(img, 0, 0, w, h);
                    // NOTE: blobUrl is NOT revoked here — caller uses it to update
                    // the live display image, then revokes it after a short delay.

                    let frameData = null;
                    try {
                        frameData = canvas.toDataURL('image/jpeg', 0.8).split(',')[1] || null;
                    } catch (e) {
                        console.error('ESP32 toDataURL error:', e.message);
                    }

                    resolve(frameData ? { frameData, blobUrl } : null);
                };
                img.onerror = () => {
                    URL.revokeObjectURL(blobUrl);
                    resolve(null);
                };
                img.src = blobUrl;
            });

        } catch (e) {
            // Don't log network errors on every tick when ESP32 is momentarily busy
            if (e.name !== 'TypeError') console.warn('ESP32 snapshot fetch failed:', e.message);
            return null;
        }
    }

    async processFrame(frameData) {
        const startTime = Date.now();

        try {
            const response = await fetch(this.config.routes.processFrame, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.config.csrfToken
                },
                body: JSON.stringify({ frame: frameData })
            });

            if (!response.ok) {
                console.error('API response error:', response.status, response.statusText);
                return;
            }

            const data = await response.json();
            const latency = Date.now() - startTime;

            // Detailed logging
            const hasObjects = data.objects?.detections?.length > 0;
            const hasThreat = data.threats?.is_threat || false;

            if (hasObjects || hasThreat) {
                console.log('🎯 DETECTION FOUND:', {
                    success: data.success,
                    latency: latency + 'ms',
                    objects: {
                        total: data.objects?.total_objects || 0,
                        leftBehind: data.objects?.left_behind_count || 0,
                        detections: data.objects?.detections || []
                    },
                    threats: {
                        isThreat: hasThreat,
                        type: data.threats?.threat_type || null,
                        confidence: data.threats?.confidence || 0
                    }
                });
            } else {
                // Only log every 10th frame when no detections to reduce console spam
                if (this.stats.framesProcessed % 10 === 0) {
                    console.log('Frame processed (no detections):', {
                        framesProcessed: this.stats.framesProcessed,
                        latency: latency + 'ms'
                    });
                }
            }

            if (data.success) {
                this.stats.framesProcessed++;
                this.updateStats(latency);

                // Handle detections
                this.handleDetections(data);

                // Draw on canvas (always draw to clear previous detections)
                this.drawDetections(data);
            } else {
                console.error('Detection failed:', data.error || 'Unknown error');
            }
        } catch (error) {
            console.error('Frame processing error:', error);
            // Don't show notification for every error to avoid spam
            if (this.stats.framesProcessed === 0) {
                this.showNotification('Detection service error: ' + error.message, 'error');
            }
        }
    }

    handleDetections(data) {
        const { objects, threats } = data;

        // Handle object detections - Show ALL objects
        if (objects && objects.detections && objects.detections.length > 0) {
            // Update total objects count
            this.updateObjectCount(objects.total_objects);

            // Show ALL detected objects in results panel (not just left-behind)
            this.stats.objectsDetected = objects.total_objects;
            this.addDetectionResult('object', objects.detections);

            // Log all detected objects for debugging
            console.log('Objects detected:', {
                total: objects.total_objects,
                leftBehind: objects.left_behind_count,
                detections: objects.detections.map(obj => ({
                    class: obj.class_name,
                    confidence: obj.confidence,
                    trackId: obj.track_id,
                    isLeftBehind: obj.is_left_behind,
                    timeStationary: obj.time_stationary
                }))
            });
        } else {
            // Clear detection results when no objects detected
            this.updateObjectCount(0);
            this.clearDetectionResults();
        }

        // Handle threat detections
        if (threats && threats.is_threat) {
            this.stats.threatsDetected++;
            this.addDetectionResult('threat', threats);
            this.updateThreatCount(this.stats.threatsDetected);
            this.showThreatAlert(threats);
        }
    }

    drawDetections(data) {
        const canvas = this.videoSource === 'pc' ? this.detectionCanvas : this.esp32Canvas;

        // For PC camera: use <video> natural dimensions
        // For ESP32-CAM: use dimensions captured from the last snapshot
        //   (_esp32FrameWidth/_esp32FrameHeight are stored by _captureEsp32Snapshot)
        let width, height;
        if (this.videoSource === 'pc') {
            width  = this.videoElement.videoWidth;
            height = this.videoElement.videoHeight;
        } else {
            width  = this._esp32FrameWidth  || this.esp32Stream.naturalWidth  || this.esp32Stream.width  || 320;
            height = this._esp32FrameHeight || this.esp32Stream.naturalHeight || this.esp32Stream.height || 240;
        }

        if (!width || !height) {
            console.warn('Cannot draw detections: invalid video dimensions');
            return;
        }

        canvas.width = width;
        canvas.height = height;

        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Draw ALL object detections with bounding boxes
        if (data.objects && data.objects.detections && data.objects.detections.length > 0) {
            console.log(`Drawing ${data.objects.detections.length} detections on canvas`);

            data.objects.detections.forEach((obj) => {
                const [x1, y1, x2, y2] = obj.bbox;

                // Color logic:
                // - Yellow (#FCD34D) for persons
                // - Red (#EF4444) for left-behind objects
                // - Purple (#A855F7) for unknown objects
                // - Green (#10B981) for tracked known objects
                let color, label;

                if (obj.class_name.toLowerCase() === 'person') {
                    color = '#FCD34D';  // Yellow for persons
                    label = `👤 ${obj.class_name}`;
                } else if (obj.is_left_behind) {
                    color = '#EF4444';  // Red for left-behind
                    label = `${obj.class_name} [LEFT BEHIND]`;
                } else if (obj.is_unknown) {
                    color = '#A855F7';  // Purple for unknown objects
                    label = `❓ unknown (${obj.original_class_name || 'unidentified'})`;
                } else {
                    color = '#10B981';  // Green for tracked objects
                    label = `${obj.class_name}`;
                }

                // Draw bounding box
                ctx.strokeStyle = color;
                ctx.lineWidth = 3;
                ctx.strokeRect(x1, y1, x2 - x1, y2 - y1);

                // Draw label background
                const labelWidth = Math.max(200, ctx.measureText(label).width + 10);

                ctx.fillStyle = color;
                ctx.fillRect(x1, Math.max(0, y1 - 25), labelWidth, 25);

                // Draw label text
                ctx.fillStyle = '#FFFFFF';
                ctx.font = 'bold 14px Arial';
                ctx.fillText(label, x1 + 5, Math.max(15, y1 - 7));

                // Draw confidence score
                ctx.font = '12px Arial';
                ctx.fillText(`${(obj.confidence * 100).toFixed(1)}%`, x1 + 5, y2 - 5);
            });
        }

        // Draw threat indicator overlay
        if (data.threats && data.threats.is_threat) {
            // Red overlay
            ctx.fillStyle = 'rgba(239, 68, 68, 0.3)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            // Threat warning text
            ctx.fillStyle = '#EF4444';
            ctx.font = 'bold 24px Arial';
            ctx.strokeStyle = '#FFFFFF';
            ctx.lineWidth = 3;
            ctx.strokeText(`⚠ THREAT: ${data.threats.threat_type}`, 10, 40);
            ctx.fillText(`⚠ THREAT: ${data.threats.threat_type}`, 10, 40);

            // Confidence
            ctx.font = 'bold 18px Arial';
            ctx.strokeText(`Confidence: ${(data.threats.confidence * 100).toFixed(1)}%`, 10, 70);
            ctx.fillText(`Confidence: ${(data.threats.confidence * 100).toFixed(1)}%`, 10, 70);
        }
    }

    clearDetectionResults() {
        const container = document.getElementById('resultsContainer');
        const noResultsMsg = document.getElementById('noResultsMsg');

        if (container) {
            // Clear all alerts except the "no results" message
            const alerts = container.querySelectorAll('.alert');
            alerts.forEach(alert => alert.remove());
        }

        if (noResultsMsg) {
            noResultsMsg.style.display = 'block';
        }
    }

    addDetectionResult(type, data) {
        const container = document.getElementById('resultsContainer');
        const noResultsMsg = document.getElementById('noResultsMsg');

        if (!container) return;

        // Clear previous results first
        this.clearDetectionResults();

        if (noResultsMsg) {
            noResultsMsg.style.display = 'none';
        }

        const time = new Date().toLocaleTimeString();

        if (type === 'object' && Array.isArray(data)) {
            // Show each detected object
            data.forEach(obj => {
                // Determine alert type and badge based on object type
                let alertType, statusBadge, icon;

                if (obj.class_name.toLowerCase() === 'person') {
                    alertType = 'warning';  // Yellow alert for persons
                    statusBadge = '<span class="badge bg-warning text-dark">👤 PERSON</span>';
                    icon = '👤';
                } else if (obj.is_left_behind) {
                    alertType = 'danger';  // Red alert for left-behind
                    statusBadge = '<span class="badge bg-danger">⚠️ LEFT BEHIND</span>';
                    icon = '⚠️';
                } else if (obj.is_unknown) {
                    alertType = 'secondary';  // Gray/purple alert for unknown
                    statusBadge = '<span class="badge bg-purple">❓ UNKNOWN</span>';
                    icon = '❓';
                } else {
                    alertType = 'success';  // Green alert for tracked
                    statusBadge = '<span class="badge bg-success">✓ TRACKED</span>';
                    icon = '✓';
                }

                const resultDiv = document.createElement('div');
                resultDiv.className = `alert alert-${alertType} mb-2`;

                const stationaryTime = obj.time_stationary > 0
                    ? `<small class="text-muted">Stationary: ${obj.time_stationary.toFixed(1)}s</small>`
                    : '';

                // Show original class name for unknown objects
                const displayName = obj.is_unknown && obj.original_class_name
                    ? `unknown (${obj.original_class_name})`
                    : obj.class_name;

                resultDiv.innerHTML = `
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <strong>${icon} ${displayName}</strong>
                                ${statusBadge}
                                <span class="badge bg-secondary">ID: ${obj.track_id}</span>
                            </div>
                            <div class="text-sm">
                                <span class="text-muted">Confidence: ${(obj.confidence * 100).toFixed(1)}%</span>
                                ${stationaryTime}
                            </div>
                        </div>
                        <small class="text-muted">${time}</small>
                    </div>
                `;

                container.appendChild(resultDiv);
            });

            // Add to history (only once for all objects)
            this.addToHistory(type, data);
        } else if (type === 'threat') {
            const resultDiv = document.createElement('div');
            resultDiv.className = 'alert alert-danger mb-2';

            resultDiv.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>⚠️ Threat Detected</strong>
                        <p class="mb-0 text-sm">${data.threat_type} (${(data.confidence * 100).toFixed(1)}%)</p>
                    </div>
                    <small>${time}</small>
                </div>
            `;

            container.appendChild(resultDiv);
            this.addToHistory(type, data);
        }
    }

    addToHistory(type, data) {
        const tbody = document.getElementById('historyTableBody');
        const emptyRow = tbody.querySelector('td[colspan="5"]');

        if (emptyRow) {
            tbody.innerHTML = '';
        }

        const row = tbody.insertRow(0);
        const time = new Date().toLocaleTimeString();

        row.innerHTML = `
            <td class="text-sm">${time}</td>
            <td><span class="badge bg-${type === 'threat' ? 'danger' : 'warning'}">${type.toUpperCase()}</span></td>
            <td class="text-sm">${type === 'object' ? `${data.length} objects` : data.threat_type}</td>
            <td class="text-sm">${type === 'threat' ? (data.confidence * 100).toFixed(1) + '%' : 'N/A'}</td>
            <td><span class="badge bg-info">New</span></td>
        `;

        this.detectionHistory.unshift({ type, data, time });
    }

    showThreatAlert(threat) {
        // Show modal or notification
        this.showNotification(`THREAT DETECTED: ${threat.threat_type}`, 'error');
    }

    stopDetection() {
        console.log('🛑 stopDetection called');
        console.trace('Stop detection call stack'); // This will show where it was called from

        this.isRunning = false;

        if (this.stream) {
            this.stream.getTracks().forEach(track => track.stop());
            this.stream = null;
        }

        if (this.esp32Interval) {
            clearInterval(this.esp32Interval);
            this.esp32Interval = null;
        }

        // Update UI
        document.getElementById('startDetectionBtn').classList.remove('d-none');
        document.getElementById('stopDetectionBtn').classList.add('d-none');
        document.getElementById('detectionStatus').textContent = 'Inactive';
        document.getElementById('cameraStatus').innerHTML = '<span class="text-secondary text-sm">Camera disconnected</span>';

        console.log('Detection stopped');
        this.showNotification('Detection stopped', 'info');
    }

    switchVideoSource(source) {
        console.log('Switching video source to:', source);

        // Should only be called when detection is NOT running
        if (this.isRunning) {
            console.error('switchVideoSource called while detection is running - this should not happen!');
            return;
        }

        this.videoSource = source;

        if (source === 'pc') {
            document.getElementById('pcCameraSection').classList.remove('d-none');
            document.getElementById('esp32CameraSection').classList.add('d-none');
        } else {
            document.getElementById('pcCameraSection').classList.add('d-none');
            document.getElementById('esp32CameraSection').classList.remove('d-none');
        }
    }

    connectEsp32() {
        const ip = document.getElementById('esp32IpInput').value.trim();
        if (!ip) {
            this.showNotification('Please enter ESP32-CAM IP address', 'warning');
            return;
        }

        this.showNotification('Connecting to ESP32-CAM...', 'info');
        // Connection will be established when detection starts
    }

    updateStats(latency) {
        this.stats.totalLatency += latency;

        // Update UI
        const framesEl = document.getElementById('framesProcessed');
        if (framesEl) {
            framesEl.textContent = this.stats.framesProcessed;

            // Add pulse animation on update
            framesEl.style.color = '#4CAF50';
            setTimeout(() => {
                framesEl.style.color = '';
            }, 200);
        }

        const fps = this.stats.framesProcessed / ((Date.now() - this.stats.startTime) / 1000);
        const processingRateEl = document.getElementById('processingRate');
        if (processingRateEl) {
            processingRateEl.innerHTML = `<span class="text-success text-sm">⚡ ${fps.toFixed(1)} fps</span>`;
        }

        const fpsCounterEl = document.getElementById('fpsCounter');
        if (fpsCounterEl) {
            fpsCounterEl.textContent = `${fps.toFixed(1)} FPS`;
        }

        const latencyCounterEl = document.getElementById('latencyCounter');
        if (latencyCounterEl) {
            latencyCounterEl.textContent = `${latency}ms`;
            // Color code latency: green < 200ms, yellow < 500ms, red >= 500ms
            latencyCounterEl.className = latency < 200 ? 'badge bg-success' :
                                         latency < 500 ? 'badge bg-warning' :
                                         'badge bg-danger';
        }
    }

    updateObjectCount(count) {
        document.getElementById('objectCount').textContent = count;
        document.getElementById('lastObjectTime').innerHTML = `<span class="text-warning text-sm">Just now</span>`;
    }

    updateThreatCount(count) {
        document.getElementById('threatCount').textContent = count;
        document.getElementById('lastThreatTime').innerHTML = `<span class="text-danger text-sm">Just now</span>`;
    }

    updateStatusIndicator(status, message) {
        // Update status indicator
        console.log(`Status: ${status} - ${message}`);
    }

    clearResults() {
        document.getElementById('resultsContainer').innerHTML = `
            <div class="text-center text-secondary py-4" id="noResultsMsg">
                <i class="material-symbols-rounded" style="font-size: 48px;">search</i>
                <p class="mt-2">No detections yet. Start monitoring to see results.</p>
            </div>
        `;
    }

    clearHistory() {
        document.getElementById('historyTableBody').innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-secondary py-4">
                    No detection history available
                </td>
            </tr>
        `;
        this.detectionHistory = [];
    }

    showNotification(message, type) {
        // Simple notification - can be enhanced with a toast library
        console.log(`[${type.toUpperCase()}] ${message}`);

        // You can integrate with your existing notification system here
        if (window.showToast) {
            window.showToast(message, type);
        }
    }
}

// Export for use in blade template
window.VideoThreatDetection = VideoThreatDetection;


