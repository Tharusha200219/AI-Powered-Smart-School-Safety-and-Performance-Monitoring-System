<!-- Multi-Angle Face Capture Component -->
<div class="modal fade" id="faceCaptureModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary">
                <h5 class="modal-title text-white"><i class="fas fa-camera me-2"></i>Face Recognition Setup</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div id="faceInstructions" class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i><strong>We'll capture your face from multiple angles</strong>
                    <p class="mb-0 mt-2">This helps improve recognition accuracy. Follow the on-screen guide.</p>
                </div>

                <div class="position-relative" style="max-width:640px;margin:0 auto">
                    <video id="faceVideo" autoplay playsinline style="width:100%;border-radius:10px;display:none"></video>
                    <canvas id="faceCanvas" style="width:100%;border-radius:10px;background:#000"></canvas>
                    
                    <div id="faceGuide" class="position-absolute" style="top:50%;left:50%;transform:translate(-50%,-50%);pointer-events:none">
                        <div style="width:200px;height:250px;border:3px dashed rgba(255,255,255,0.5);border-radius:50%;position:relative">
                            <div id="faceGuideArrow" style="position:absolute;top:-40px;left:50%;transform:translateX(-50%);font-size:24px;color:white">⬇️</div>
                        </div>
                    </div>

                    <div class="position-absolute bottom-0 start-0 end-0 p-3" style="background:linear-gradient(to top,rgba(0,0,0,0.7),transparent)">
                        <div class="text-white mb-2">
                            <h5 id="faceInstruction">Position your face in the circle</h5>
                            <p id="faceSubInstruction" class="mb-2">Look straight at the camera</p>
                        </div>
                        <div class="progress" style="height:8px">
                            <div id="faceProgress" class="progress-bar bg-success" style="width:0%"></div>
                        </div>
                        <small class="text-white">Photo <span id="faceCount">0</span> of 5</small>
                    </div>
                </div>

                <div id="faceThumbnails" class="d-flex justify-content-center gap-2 mt-3"></div>
                <div id="faceError" class="alert alert-danger mt-3" style="display:none">
                    <i class="fas fa-exclamation-triangle me-2"></i><span id="faceErrorMessage"></span>
                </div>
                <div id="faceLoading" style="display:none">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-2">Initializing camera...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="faceSkipBtn">
                    <i class="fas fa-times me-1"></i>Skip for Now
                </button>
                <button type="button" class="btn btn-primary" id="faceStartBtn">
                    <i class="fas fa-camera me-1"></i>Start Capture
                </button>
                <button type="button" class="btn btn-success" id="faceDoneBtn" style="display:none">
                    <i class="fas fa-check me-1"></i>Done
                </button>
            </div>
        </div>
    </div>
</div>

<style>
#faceGuide{animation:pulseGuide 2s infinite}
@keyframes pulseGuide{0%,100%{opacity:0.6;transform:translate(-50%,-50%) scale(1)}50%{opacity:1;transform:translate(-50%,-50%) scale(1.05)}}
</style>

<script src="{{ asset('js/face-capture.js') }}"></script>
