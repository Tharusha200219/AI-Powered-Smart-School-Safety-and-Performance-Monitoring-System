@extends('admin.layouts.app')

@section('content')
    @include('admin.layouts.sidebar')

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        @include('admin.layouts.navbar')

        <div class="container-fluid pt-2">
            <div class="row">
                <div class="col-12">
                    @include('admin.layouts.flash')
                    
                    <div class="card my-4">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6 d-flex align-items-center">
                                    <a href="{{ route('admin.seating-arrangement.index') }}" class="btn btn-sm btn-secondary me-2">
                                        <i class="material-symbols-rounded" style="font-size: 18px;">arrow_back</i>
                                    </a>
                                    <h6 class="mb-0">
                                        <i class="material-symbols-rounded me-2">psychology</i>
                                        Generate Seating Arrangement
                                    </h6>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <form action="{{ route('admin.seating-arrangement.generate') }}" method="POST" id="seatingForm">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">Grade Level</label>
                                            <select name="grade_level" id="gradeLevel" class="form-control" required>
                                                <option value="">Select Grade</option>
                                                @for($i = 1; $i <= 13; $i++)
                                                    <option value="{{ $i }}">Grade {{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">Section</label>
                                            <select name="section" id="section" class="form-control" required>
                                                <option value="">Select Section</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">Academic Year</label>
                                            <input type="text" name="academic_year" class="form-control" 
                                                   value="{{ date('Y') }}-{{ date('Y') + 1 }}" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">Term</label>
                                            <select name="term" class="form-control" required>
                                                <option value="1">Term 1</option>
                                                <option value="2">Term 2</option>
                                                <option value="3">Term 3</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">Seats Per Row</label>
                                            <input type="number" name="seats_per_row" class="form-control" 
                                                   min="1" max="10" value="5" required>
                                        </div>
                                        <small class="text-muted">Number of seats in each row (1-10)</small>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">Total Rows</label>
                                            <input type="number" name="total_rows" class="form-control" 
                                                   min="1" max="20" value="6" required>
                                        </div>
                                        <small class="text-muted">Number of rows in classroom (1-20)</small>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info mt-4">
                                    <i class="material-symbols-rounded me-2">info</i>
                                    <strong>AI-Powered Seating:</strong> The system will analyze student performance data and generate an optimal seating arrangement that:
                                    <ul class="mb-0 mt-2">
                                        <li>Balances strong and weak students for peer learning</li>
                                        <li>Considers behavioral patterns and attendance</li>
                                        <li>Optimizes teacher visibility and classroom management</li>
                                    </ul>
                                </div>
                                
                                <div class="text-end mt-4">
                                    <a href="{{ route('admin.seating-arrangement.index') }}" class="btn btn-light">Cancel</a>
                                    <button type="submit" class="btn btn-primary" id="generateBtn">
                                        <i class="material-symbols-rounded me-1" style="font-size: 18px;">psychology</i>
                                        Generate Arrangement
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
<script>
document.getElementById('gradeLevel').addEventListener('change', function() {
    const grade = this.value;
    const sectionSelect = document.getElementById('section');
    
    if (!grade) {
        sectionSelect.innerHTML = '<option value="">Select Section</option>';
        return;
    }
    
    // Fetch sections for selected grade
    fetch(`/admin/seating-arrangement/get-sections?grade=${grade}`)
        .then(response => response.json())
        .then(data => {
            sectionSelect.innerHTML = '<option value="">Select Section</option>';
            data.forEach(section => {
                sectionSelect.innerHTML += `<option value="${section}">${section}</option>`;
            });
        })
        .catch(error => {
            console.error('Error:', error);
            sectionSelect.innerHTML = '<option value="">Error loading sections</option>';
        });
});

document.getElementById('seatingForm').addEventListener('submit', function(e) {
    const btn = document.getElementById('generateBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Generating...';
});
</script>
@endpush
