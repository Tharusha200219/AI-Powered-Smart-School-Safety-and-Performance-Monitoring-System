// Face Capture System - Multi-Angle Face Recognition
// Similar to Phone Face Unlock (Face ID)

class FaceCapture {
    constructor() {
        this.modal = null;
        this.video = null;
        this.canvas = null;
        this.ctx = null;
        this.stream = null;
        this.capturedImages = [];
        this.currentAngle = 0;
        this.isCapturing = false;
        
        this.angles = [
            { name: 'Center', instruction: 'Look straight at the camera', arrow: '⬇️', autoCapture: true },
            { name: 'Left', instruction: 'Turn your head slightly to the left', arrow: '↙️', autoCapture: true },
            { name: 'Right', instruction: 'Turn your head slightly to the right', arrow: '↘️', autoCapture: true },
            { name: 'Up', instruction: 'Tilt your head slightly up', arrow: '⬆️', autoCapture: true },
            { name: 'Down', instruction: 'Tilt your head slightly down', arrow: '⬇️', autoCapture: true }
        ];
    }
    
    init() {
        this.modal = document.getElementById('faceCaptureModal');
        this.video = document.getElementById('faceVideo');
        this.canvas = document.getElementById('faceCanvas');
        this.ctx = this.canvas?.getContext('2d');
        
        if (!this.modal || !this.video || !this.canvas) {
            console.error('Face capture elements not found');
            return;
        }
        
        this.initEventListeners();
    }
    
    initEventListeners() {
        document.getElementById('faceStartBtn')?.addEventListener('click', () => this.startCapture());
        document.getElementById('faceDoneBtn')?.addEventListener('click', () => this.finishCapture());
        document.getElementById('faceSkipBtn')?.addEventListener('click', () => this.cleanup());
        
        this.modal?.addEventListener('hidden.bs.modal', () => this.cleanup());
    }
    
    async startCapture() {
        try {
            document.getElementById('faceLoading').style.display = 'block';
            document.getElementById('faceStartBtn').style.display = 'none';
            
            this.stream = await navigator.mediaDevices.getUserMedia({ 
                video: { width: 1280, height: 720, facingMode: 'user' } 
            });
            
            this.video.srcObject = this.stream;
            this.video.style.display = 'block';
            await this.video.play();
            
            this.canvas.width = this.video.videoWidth;
            this.canvas.height = this.video.videoHeight;
            
            document.getElementById('faceLoading').style.display = 'none';
            
            this.isCapturing = true;
            this.updateInstruction();
            this.captureLoop();
            setTimeout(() => this.captureImage(), 2000);
            
        } catch (error) {
            console.error('Camera error:', error);
            this.showError('Failed to access camera. Please allow camera permissions.');
            document.getElementById('faceLoading').style.display = 'none';
            document.getElementById('faceStartBtn').style.display = 'block';
        }
    }
    
    captureLoop() {
        if (!this.isCapturing) return;
        this.ctx.drawImage(this.video, 0, 0, this.canvas.width, this.canvas.height);
        requestAnimationFrame(() => this.captureLoop());
    }
    
    async captureImage() {
        this.ctx.drawImage(this.video, 0, 0, this.canvas.width, this.canvas.height);
        
        const imageBlob = await new Promise(resolve => {
            this.canvas.toBlob(resolve, 'image/jpeg', 0.95);
        });
        
        this.capturedImages.push(imageBlob);
        this.updateProgress();
        this.addThumbnail(imageBlob);
        
        this.currentAngle++;
        
        if (this.currentAngle < this.angles.length) {
            this.updateInstruction();
            setTimeout(() => this.captureImage(), 2000);
        } else {
            this.completeCapture();
        }
    }
    
    updateInstruction() {
        const angle = this.angles[this.currentAngle];
        document.getElementById('faceInstruction').textContent = `Step ${this.currentAngle + 1}`;
        document.getElementById('faceSubInstruction').textContent = angle.instruction;
        document.getElementById('faceGuideArrow').textContent = angle.arrow;
    }
    
    updateProgress() {
        const progress = (this.capturedImages.length / this.angles.length) * 100;
        document.getElementById('faceProgress').style.width = progress + '%';
        document.getElementById('faceCount').textContent = this.capturedImages.length;
    }
    
    addThumbnail(blob) {
        const url = URL.createObjectURL(blob);
        const img = document.createElement('img');
        img.src = url;
        img.style.cssText = 'width:80px;height:80px;object-fit:cover;border-radius:8px;border:2px solid #28a745';
        document.getElementById('faceThumbnails').appendChild(img);
    }
    
    completeCapture() {
        this.isCapturing = false;
        document.getElementById('faceInstruction').textContent = 'All photos captured!';
        document.getElementById('faceSubInstruction').textContent = 'Click Done to continue';
        document.getElementById('faceDoneBtn').style.display = 'block';
        this.canvas.style.border = '5px solid #28a745';
        setTimeout(() => { this.canvas.style.border = 'none'; }, 500);
    }
    
    finishCapture() {
        const event = new CustomEvent('facesCaptured', { detail: { images: this.capturedImages } });
        window.dispatchEvent(event);
        bootstrap.Modal.getInstance(this.modal).hide();
    }
    
    cleanup() {
        this.isCapturing = false;
        if (this.stream) {
            this.stream.getTracks().forEach(track => track.stop());
            this.stream = null;
        }
        
        this.capturedImages = [];
        this.currentAngle = 0;
        if (this.video) this.video.style.display = 'none';
        document.getElementById('faceStartBtn').style.display = 'block';
        document.getElementById('faceDoneBtn').style.display = 'none';
        document.getElementById('faceProgress').style.width = '0%';
        document.getElementById('faceCount').textContent = '0';
        document.getElementById('faceThumbnails').innerHTML = '';
        if (this.canvas) this.canvas.style.border = 'none';
    }
    
    showError(message) {
        document.getElementById('faceErrorMessage').textContent = message;
        document.getElementById('faceError').style.display = 'block';
        setTimeout(() => { document.getElementById('faceError').style.display = 'none'; }, 5000);
    }
    
    open() {
        const modalInstance = new bootstrap.Modal(this.modal);
        modalInstance.show();
        this.updateInstruction();
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.faceCapture = new FaceCapture();
        window.faceCapture.init();
    });
} else {
    window.faceCapture = new FaceCapture();
    window.faceCapture.init();
}
