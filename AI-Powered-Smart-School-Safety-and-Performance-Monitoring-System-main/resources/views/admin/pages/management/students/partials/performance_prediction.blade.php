<!-- Performance Prediction Card - Enhanced UI with Attendance & Term Marks -->
<div class="card mb-4" id="performancePredictionCard">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0 d-flex align-items-center">
            <i class="material-symbols-rounded me-2 icon-size-sm">trending_up</i>
            AI Performance Prediction
        </h6>
        <span class="badge bg-gradient-info badge-sm">Live Prediction</span>
    </div>
    <div class="card-body">
        <!-- Loading State -->
        <div id="predictionLoading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading predictions...</span>
            </div>
            <p class="text-muted mt-3">Analyzing student performance data...</p>
        </div>

        <!-- Error State -->
        <div id="predictionError" class="alert alert-warning alert-dismissible fade show" role="alert"
            style="display: none;">
            <i class="material-symbols-rounded align-middle me-2">error</i>
            <span id="errorMessage"></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <!-- Predictions Content -->
        <div id="predictionContent" style="display: none;">
            <!-- Overall Summary -->
            <div class="row mb-4" id="predictionSummary">
                <!-- Summary stats populated by JavaScript -->
            </div>

            <!-- Detailed Subject Cards -->
            <div class="row" id="detailedPredictions">
                <!-- Individual prediction cards populated by JavaScript -->
            </div>
        </div>
    </div>
</div>

<style>
    /* Subject Performance Cards */
    .subject-performance-card {
        border: 1px solid #e3e6f0;
        border-radius: 12px;
        transition: all 0.3s ease;
        overflow: hidden;
        background: white;
    }

    .subject-performance-card:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        transform: translateY(-4px);
    }

    .subject-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .subject-header h5 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .attendance-badge {
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(10px);
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .attendance-badge.high {
        background: rgba(16, 185, 129, 0.9);
        border-color: #10b981;
    }

    .attendance-badge.medium {
        background: rgba(251, 191, 36, 0.9);
        border-color: #fbbf24;
    }

    .attendance-badge.low {
        background: rgba(239, 68, 68, 0.9);
        border-color: #ef4444;
    }

    /* Term Marks Section */
    .term-marks-section {
        padding: 1.25rem;
        background: #f9fafb;
        border-bottom: 1px solid #e3e6f0;
    }

    .term-marks-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
    }

    .term-mark-item {
        text-align: center;
        padding: 1rem;
        background: white;
        border-radius: 8px;
        border: 2px solid #e3e6f0;
        transition: all 0.2s ease;
    }

    .term-mark-item:hover {
        border-color: #667eea;
        transform: scale(1.05);
    }

    .term-mark-label {
        display: block;
        font-size: 0.75rem;
        color: #6b7280;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
        letter-spacing: 0.5px;
    }

    .term-mark-value {
        display: block;
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
        line-height: 1;
    }

    /* Prediction Section */
    .prediction-section {
        padding: 1.25rem;
    }

    .predicted-score-box {
        text-align: center;
        padding: 1.5rem;
        background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
        border-radius: 12px;
        margin-bottom: 1rem;
        border: 2px solid #667eea;
    }

    .predicted-label {
        font-size: 0.85rem;
        color: #6b7280;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
    }

    .predicted-value {
        font-size: 3rem;
        font-weight: 700;
        color: #667eea;
        line-height: 1;
        margin-bottom: 0.5rem;
    }

    .confidence-range {
        font-size: 0.85rem;
        color: #6b7280;
        font-weight: 500;
    }

    /* Trend and Category Badges */
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-top: 1rem;
    }

    .info-item {
        padding: 0.75rem;
        background: #f9fafb;
        border-radius: 8px;
        text-align: center;
    }

    .info-label {
        display: block;
        font-size: 0.75rem;
        color: #6b7280;
        font-weight: 600;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
    }

    .trend-badge-large {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .trend-badge-large i {
        font-size: 1.2rem;
    }

    .trend-improving {
        background: #d4edda;
        color: #155724;
    }

    .trend-declining {
        background: #f8d7da;
        color: #721c24;
    }

    .trend-stable {
        background: #d1ecf1;
        color: #0c5460;
    }

    .category-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .category-excellent {
        background: #059669;
        color: white;
    }

    .category-good {
        background: #10b981;
        color: white;
    }

    .category-average {
        background: #fbbf24;
        color: white;
    }

    .category-needs-improvement {
        background: #dc2626;
        color: white;
    }

    /* Recommendation Section */
    .recommendation-box {
        margin-top: 1rem;
        padding: 1rem;
        background: #fef3c7;
        border-left: 4px solid #f59e0b;
        border-radius: 6px;
    }

    .recommendation-box .icon {
        color: #f59e0b;
        font-size: 1.2rem;
        vertical-align: middle;
    }

    .recommendation-text {
        font-size: 0.9rem;
        color: #92400e;
        margin: 0;
        line-height: 1.5;
    }

    /* Summary Cards */
    .summary-stat-card {
        text-align: center;
        padding: 1.5rem;
        background: white;
        border-radius: 12px;
        border: 2px solid #e3e6f0;
        transition: all 0.2s ease;
    }

    .summary-stat-card:hover {
        border-color: #667eea;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .summary-stat-card .icon {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
    }

    .summary-stat-card h6 {
        font-size: 0.85rem;
        color: #6b7280;
        font-weight: 600;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
    }

    .summary-stat-card .value {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1;
    }

    /* Attendance Progress Bar */
    .attendance-progress {
        height: 8px;
        background: #e3e6f0;
        border-radius: 10px;
        overflow: hidden;
        margin-top: 0.5rem;
    }

    .attendance-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #10b981 0%, #059669 100%);
        transition: width 0.3s ease;
    }

    .attendance-progress-fill.medium {
        background: linear-gradient(90deg, #fbbf24 0%, #f59e0b 100%);
    }

    .attendance-progress-fill.low {
        background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        loadPerformancePrediction({{ $student->student_id }});
    });

    function loadPerformancePrediction(studentId) {
        const loadingEl = document.getElementById('predictionLoading');
        const errorEl = document.getElementById('predictionError');
        const contentEl = document.getElementById('predictionContent');

        // Show loading state
        loadingEl.style.display = 'block';
        errorEl.style.display = 'none';
        contentEl.style.display = 'none';

        // Fetch prediction from API
        fetch(`/api/students/${studentId}/prediction`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    displayPrediction(data.data);
                    loadingEl.style.display = 'none';
                    contentEl.style.display = 'block';
                } else if (data.status === 'no_data') {
                    loadingEl.style.display = 'none';
                    showError(data.message || 'No marks data available for this student.');
                } else {
                    showError(data.error || 'Failed to load prediction');
                }
            })
            .catch(error => {
                console.error('Error loading prediction:', error);
                showError(error.message ||
                    'Failed to connect to prediction service. Make sure the API is running on port 5002.');
            });
    }

    function displayPrediction(data) {
        if (!data.predictions || data.predictions.length === 0) {
            showError('No prediction data available for this student');
            return;
        }

        const summaryContainer = document.getElementById('predictionSummary');
        const detailedContainer = document.getElementById('detailedPredictions');

        summaryContainer.innerHTML = '';
        detailedContainer.innerHTML = '';

        // Calculate stats
        let totalImprovement = 0;
        let improvingSubjects = 0;
        let avgAttendance = 0;

        data.predictions.forEach(pred => {
            if (pred.improvement > 0) improvingSubjects++;
            totalImprovement += pred.improvement || 0;
            avgAttendance += pred.attendance || 0;
        });

        avgAttendance = avgAttendance / data.predictions.length || 0;

        // Display summary stats
        displaySummaryStats(data, totalImprovement, improvingSubjects, avgAttendance);

        // Create detailed cards for each subject
        data.predictions.forEach(pred => {
            const card = createEnhancedSubjectCard(pred);
            detailedContainer.appendChild(card);
        });
    }

    function createEnhancedSubjectCard(prediction) {
        const card = document.createElement('div');
        card.className = 'col-lg-6 col-md-12 mb-4';

        // Determine attendance level
        const attendance = prediction.attendance || 0;
        const attendanceClass = attendance >= 80 ? 'high' : attendance >= 60 ? 'medium' : 'low';
        const attendanceProgressClass = attendance >= 80 ? '' : attendance >= 60 ? 'medium' : 'low';

        // Trend icons and colors
        const trendClass = prediction.prediction_trend === 'Improving' ? 'trend-improving' :
            prediction.prediction_trend === 'Declining' ? 'trend-declining' : 'trend-stable';
        const trendIcon = prediction.prediction_trend === 'Improving' ? 'trending_up' :
            prediction.prediction_trend === 'Declining' ? 'trending_down' : 'trending_flat';

        // Category badge
        const category = prediction.performance_category || 'Average';
        const categoryClass = category === 'Excellent' ? 'category-excellent' :
            category === 'Good' ? 'category-good' :
            category === 'Average' ? 'category-average' : 'category-needs-improvement';

        card.innerHTML = `
            <div class="subject-performance-card">
                <!-- Subject Header with Attendance -->
                <div class="subject-header">
                    <h5>📚 ${prediction.subject}</h5>
                    <div class="attendance-badge ${attendanceClass}">
                        📊 ${attendance.toFixed(1)}%
                    </div>
                </div>

                <!-- Term Marks Section -->
                <div class="term-marks-section">
                    <h6 class="mb-3" style="color: #6b7280; font-size: 0.85rem; font-weight: 600;">TERM MARKS</h6>
                    <div class="term-marks-grid">
                        <div class="term-mark-item">
                            <span class="term-mark-label">Term 1</span>
                            <span class="term-mark-value">${(prediction.term1_marks || 0).toFixed(0)}</span>
                        </div>
                        <div class="term-mark-item">
                            <span class="term-mark-label">Term 2</span>
                            <span class="term-mark-value">${(prediction.term2_marks || 0).toFixed(0)}</span>
                        </div>
                        <div class="term-mark-item">
                            <span class="term-mark-label">Term 3</span>
                            <span class="term-mark-value">${(prediction.term3_marks || 0).toFixed(0)}</span>
                        </div>
                    </div>
                    <div class="attendance-progress">
                        <div class="attendance-progress-fill ${attendanceProgressClass}"
                             style="width: ${Math.min(attendance, 100)}%"></div>
                    </div>
                </div>

                <!-- Prediction Section -->
                <div class="prediction-section">
                    <div class="predicted-score-box">
                        <div class="predicted-label">🎯 Predicted Performance</div>
                        <div class="predicted-value">${prediction.predicted_performance.toFixed(1)}</div>
                        <div class="confidence-range">
                            95% CI: [${prediction.confidence_interval.lower_bound.toFixed(1)},
                            ${prediction.confidence_interval.upper_bound.toFixed(1)}]
                        </div>
                    </div>

                    <!-- Trend and Category -->
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Trend</span>
                            <div class="trend-badge-large ${trendClass}">
                                <i class="material-symbols-rounded">${trendIcon}</i>
                                ${prediction.prediction_trend}
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Category</span>
                            <div class="category-badge ${categoryClass}">
                                ⭐ ${category}
                            </div>
                        </div>
                    </div>

                    <!-- Recommendation -->
                    ${prediction.recommendation ? `
                        <div class="recommendation-box">
                            <i class="material-symbols-rounded icon">lightbulb</i>
                            <p class="recommendation-text"><strong>💡 Recommendation:</strong> ${prediction.recommendation}</p>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;

        return card;
    }

    function displaySummaryStats(data, totalImprovement, improvingSubjects, avgAttendance) {
        const summary = document.getElementById('predictionSummary');
        const avgImprovementValue = (totalImprovement / (data.total_subjects || 1)).toFixed(1);
        const improvementColor = avgImprovementValue > 0 ? 'text-success' :
            avgImprovementValue < 0 ? 'text-danger' : 'text-secondary';

        summary.innerHTML = `
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="summary-stat-card">
                    <i class="material-symbols-rounded icon text-primary">school</i>
                    <h6>Total Subjects</h6>
                    <div class="value text-primary">${data.total_subjects || 0}</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="summary-stat-card">
                    <i class="material-symbols-rounded icon text-success">trending_up</i>
                    <h6>Improving</h6>
                    <div class="value text-success">${improvingSubjects}/${data.total_subjects || 0}</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="summary-stat-card">
                    <i class="material-symbols-rounded icon ${improvementColor}">auto_graph</i>
                    <h6>Avg. Change</h6>
                    <div class="value ${improvementColor}">${avgImprovementValue > 0 ? '+' : ''}${avgImprovementValue}</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="summary-stat-card">
                    <i class="material-symbols-rounded icon text-info">event_available</i>
                    <h6>Avg. Attendance</h6>
                    <div class="value text-info">${avgAttendance.toFixed(1)}%</div>
                </div>
            </div>
        `;
    }

    function showError(message) {
        const loadingEl = document.getElementById('predictionLoading');
        const errorEl = document.getElementById('predictionError');
        const contentEl = document.getElementById('predictionContent');
        const errorMessage = document.getElementById('errorMessage');

        loadingEl.style.display = 'none';
        contentEl.style.display = 'none';
        errorEl.style.display = 'block';
        errorMessage.textContent = message;
    }
</script>
