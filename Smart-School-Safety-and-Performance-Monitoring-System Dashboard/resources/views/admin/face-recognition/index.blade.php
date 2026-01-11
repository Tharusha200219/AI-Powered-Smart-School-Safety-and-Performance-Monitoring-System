@extends('admin.layouts.app')

@section('title', 'Face Recognition System')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-user-check me-2"></i>Face Recognition System
                        </h4>
                        <div>
                            <a href="{{ route('admin.face-recognition.live') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-video me-1"></i>Live Recognition
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Status -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-2">
                        @if(($status['status'] ?? 'offline') === 'healthy')
                            <i class="fas fa-check-circle fa-3x text-success"></i>
                        @else
                            <i class="fas fa-times-circle fa-3x text-danger"></i>
                        @endif
                    </div>
                    <h5>API Status</h5>
                    <p class="mb-0 text-uppercase fw-bold">
                        {{ $status['status'] ?? 'offline' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-2">
                        @if(($status['model_loaded'] ?? false))
                            <i class="fas fa-brain fa-3x text-success"></i>
                        @else
                            <i class="fas fa-exclamation-triangle fa-3x text-warning"></i>
                        @endif
                    </div>
                    <h5>Model Status</h5>
                    <p class="mb-0 fw-bold">
                        {{ ($status['model_loaded'] ?? false) ? 'Loaded' : 'Not Loaded' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="fas fa-users fa-3x text-info"></i>
                    </div>
                    <h5>Registered Students</h5>
                    <p class="mb-0 h3">
                        {{ $datasetInfo['student_count'] ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-2">
                        <i class="fas fa-user-check fa-3x text-success"></i>
                    </div>
                    <h5>Today's Attendance</h5>
                    <p class="mb-0 h3">
                        {{ $attendance['count'] ?? 0 }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Dataset Information -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-database me-2"></i>Dataset Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Total Students:</strong></td>
                            <td>{{ $datasetInfo['student_count'] ?? 0 }}</td>
                        </tr>
                        <tr>
                            <td><strong>Total Images:</strong></td>
                            <td>{{ $datasetInfo['total_images'] ?? 0 }}</td>
                        </tr>
                        <tr>
                            <td><strong>Average Images per Student:</strong></td>
                            <td>
                                {{ $datasetInfo['student_count'] > 0 
                                    ? round(($datasetInfo['total_images'] ?? 0) / $datasetInfo['student_count'], 1) 
                                    : 0 
                                }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.face-recognition.sync-all') }}" method="POST" class="mb-3">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-block w-100 mb-2">
                            <i class="fas fa-sync me-2"></i>Sync All Students
                        </button>
                    </form>

                    <form action="{{ route('admin.face-recognition.train') }}" method="POST" id="trainForm">
                        @csrf
                        <div class="input-group mb-2">
                            <input type="number" name="epochs" class="form-control" placeholder="Epochs" value="50" min="10" max="200">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-dumbbell me-1"></i>Train Model
                            </button>
                        </div>
                    </form>

                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Training takes 2-5 minutes depending on dataset size
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Attendance -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-check me-2"></i>Today's Attendance
                        <span class="badge bg-primary ms-2">{{ $attendance['count'] ?? 0 }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if(isset($attendance['records']) && count($attendance['records']) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Student ID</th>
                                        <th>Student Name</th>
                                        <th>Confidence</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attendance['records'] as $record)
                                        <tr>
                                            <td>{{ $record['marked_at'] ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $record['student_id'] ?? 'Unknown' }}</span>
                                            </td>
                                            <td>{{ $record['student_name'] ?? 'Unknown' }}</td>
                                            <td>
                                                @if(isset($record['confidence']))
                                                    <div class="progress" style="height: 20px; width: 100px;">
                                                        <div class="progress-bar" 
                                                             role="progressbar" 
                                                             style="width: {{ $record['confidence'] }}%"
                                                             aria-valuenow="{{ $record['confidence'] }}" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="100">
                                                            {{ round($record['confidence'], 1) }}%
                                                        </div>
                                                    </div>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No attendance records for today</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Training Progress Modal -->
<div class="modal fade" id="trainingModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-graduation-cap me-2"></i>Training Model
                </h5>
            </div>
            <div class="modal-body text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-brain fa-4x text-primary mb-3"></i>
                </div>
                <div class="progress mb-3" style="height: 30px;">
                    <div id="trainingProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" 
                         role="progressbar" style="width: 0%">
                        <span id="trainingProgressText">0%</span>
                    </div>
                </div>
                <p id="trainingStatus" class="text-muted mb-2">Initializing...</p>
                <p id="trainingTime" class="small text-muted"></p>
                <p class="small mt-3">
                    <i class="fas fa-info-circle me-1"></i>
                    This usually takes 2-5 minutes
                </p>
            </div>
        </div>
    </div>
</div>

<script>
let trainingCheckInterval = null;
let trainingModal = null;

document.addEventListener('DOMContentLoaded', function() {
    trainingModal = new bootstrap.Modal(document.getElementById('trainingModal'));
    
    // Check if already training on page load
    checkTrainingStatus();
});

document.getElementById('trainForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const button = this.querySelector('button[type="submit"]');
    const originalHtml = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Starting...';
    
    // Show training modal
    trainingModal.show();
    
    // Start training
    fetch('{{ route("admin.face-recognition.train") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Start checking progress
            startProgressChecking();
        } else {
            alert('Failed to start training: ' + (data.error || 'Unknown error'));
            trainingModal.hide();
            button.disabled = false;
            button.innerHTML = originalHtml;
        }
    })
    .catch(error => {
        console.error('Training error:', error);
        alert('Failed to start training');
        trainingModal.hide();
        button.disabled = false;
        button.innerHTML = originalHtml;
    });
});

function startProgressChecking() {
    if (trainingCheckInterval) {
        clearInterval(trainingCheckInterval);
    }
    
    trainingCheckInterval = setInterval(checkTrainingStatus, 2000); // Check every 2 seconds
}

function checkTrainingStatus() {
    fetch('{{ env("FACE_RECOGNITION_API_URL") }}/training/progress')
        .then(response => response.json())
        .then(data => {
            if (data.is_training) {
                // Show modal if not shown
                if (!document.getElementById('trainingModal').classList.contains('show')) {
                    trainingModal.show();
                }
                
                // Update progress
                const progress = Math.round(data.progress || 0);
                document.getElementById('trainingProgressBar').style.width = progress + '%';
                document.getElementById('trainingProgressText').textContent = progress + '%';
                document.getElementById('trainingStatus').textContent = data.message || 'Training...';
                
                // Update time
                if (data.time_remaining) {
                    const minutes = Math.floor(data.time_remaining / 60);
                    const seconds = data.time_remaining % 60;
                    document.getElementById('trainingTime').textContent = 
                        `Estimated time remaining: ${minutes}m ${seconds}s`;
                } else if (data.elapsed_time) {
                    const minutes = Math.floor(data.elapsed_time / 60);
                    const seconds = data.elapsed_time % 60;
                    document.getElementById('trainingTime').textContent = 
                        `Elapsed time: ${minutes}m ${seconds}s`;
                }
                
                // Start checking if not already
                if (!trainingCheckInterval) {
                    startProgressChecking();
                }
            } else if (data.status === 'completed') {
                // Training completed
                document.getElementById('trainingProgressBar').style.width = '100%';
                document.getElementById('trainingProgressText').textContent = '100%';
                document.getElementById('trainingStatus').textContent = 'Training completed!';
                document.getElementById('trainingProgressBar').classList.remove('progress-bar-animated');
                document.getElementById('trainingProgressBar').classList.add('bg-success');
                
                // Stop checking
                if (trainingCheckInterval) {
                    clearInterval(trainingCheckInterval);
                    trainingCheckInterval = null;
                }
                
                // Close modal and reload page after 2 seconds
                setTimeout(() => {
                    trainingModal.hide();
                    location.reload();
                }, 2000);
            } else if (data.status === 'failed') {
                // Training failed
                document.getElementById('trainingProgressBar').classList.remove('progress-bar-animated');
                document.getElementById('trainingProgressBar').classList.add('bg-danger');
                document.getElementById('trainingStatus').textContent = 'Training failed: ' + (data.message || 'Unknown error');
                
                // Stop checking
                if (trainingCheckInterval) {
                    clearInterval(trainingCheckInterval);
                    trainingCheckInterval = null;
                }
                
                // Close modal after 3 seconds
                setTimeout(() => {
                    trainingModal.hide();
                }, 3000);
            }
        })
        .catch(error => {
            console.error('Progress check error:', error);
        });
}

// Sync students button
document.querySelectorAll('form[action*="sync-all"]').forEach(form => {
    form.addEventListener('submit', function(e) {
        const button = this.querySelector('button[type="submit"]');
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Syncing...';
    });
});
</script>
@endsection
