# Laravel Sidebar Integration Guide

## Add Prediction Link to Student Sidebar

### Option 1: Using sidebar.php helper

If you're using the sidebar helper file (`app/Helpers/sidebar.php` or similar):

Add this menu item to the student section:

```php
[
    'type' => 'item',
    'title' => 'Performance Predictions',
    'icon' => 'fas fa-chart-line',
    'route' => 'admin.predictions.my-predictions',
    'badge' => [
        'text' => 'AI',
        'class' => 'badge-success'
    ],
    'permission' => 'student', // Adjust based on your permission system
]
```

### Option 2: Direct Blade Template

If you're editing sidebar blade file directly (`resources/views/layouts/sidebar.blade.php`):

```blade
{{-- For Students --}}
@if(auth()->user()->type === 'student')
    <li class="nav-item">
        <a href="{{ route('admin.predictions.my-predictions') }}" class="nav-link">
            <i class="nav-icon fas fa-chart-line"></i>
            <p>
                Performance Predictions
                <span class="badge badge-success right">AI</span>
            </p>
        </a>
    </li>
@endif
```

### Option 3: Using Livewire Component

If using Livewire for sidebar:

```php
// In your Livewire sidebar component

public function getMenuItems()
{
    $items = [
        // ... other items
    ];

    if (auth()->user()->type === 'student') {
        $items[] = [
            'title' => 'Performance Predictions',
            'icon' => 'fas fa-chart-line',
            'route' => 'admin.predictions.my-predictions',
            'badge' => 'AI'
        ];
    }

    return $items;
}
```

### Option 4: Update config/sidebar.php

If you have a config file for sidebar:

```php
// config/sidebar.php

return [
    'student' => [
        [
            'title' => 'Dashboard',
            'icon' => 'fas fa-tachometer-alt',
            'route' => 'admin.dashboard.index'
        ],
        [
            'title' => 'Performance Predictions',
            'icon' => 'fas fa-chart-line',
            'route' => 'admin.predictions.my-predictions',
            'badge' => [
                'text' => 'AI',
                'variant' => 'success'
            ]
        ],
        // ... other items
    ]
];
```

---

## Add Predictions to Student Detail View

### Location: `resources/views/admin/management/students/show.blade.php`

Add this section after student information:

```blade
{{-- Student Information Section --}}
<div class="card mb-4">
    {{-- ... existing student info ... --}}
</div>

{{-- Performance Predictions Section --}}
@php
    $predictionService = app(\App\Services\PerformancePredictionService::class);
    $predictions = $predictionService->predictStudentPerformance($student);
@endphp

@if($predictions && !empty($predictions['predictions']))
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-chart-line me-2"></i>
            AI Performance Predictions
        </h5>
    </div>
    <div class="card-body">
        <x-performance-prediction-widget :predictions="$predictions" />
    </div>
</div>
@endif
```

### Alternative: Load via AJAX (Better Performance)

```blade
{{-- In your student show view --}}
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-chart-line me-2"></i>
            AI Performance Predictions
        </h5>
    </div>
    <div class="card-body">
        <div id="predictions-container">
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading predictions...</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Load predictions via AJAX
$(document).ready(function() {
    $.ajax({
        url: '{{ route("admin.predictions.api.student", $student->student_id) }}',
        method: 'GET',
        success: function(data) {
            renderPredictions(data);
        },
        error: function() {
            $('#predictions-container').html(
                '<div class="alert alert-warning">Unable to load predictions. Please try again later.</div>'
            );
        }
    });
});

function renderPredictions(data) {
    let html = '';

    if (data.predictions && data.predictions.length > 0) {
        data.predictions.forEach(function(pred) {
            let trendBadge = pred.prediction_trend === 'improving' ? 'success' :
                           pred.prediction_trend === 'declining' ? 'danger' : 'warning';

            html += `
                <div class="prediction-item mb-3 p-3 border rounded">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-0 fw-bold">${pred.subject}</h6>
                        <span class="badge badge-${trendBadge}">${pred.prediction_trend}</span>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <small class="text-muted">Current: ${pred.current_performance}%</small>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Predicted: ${pred.predicted_performance}%</small>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted"><i class="fas fa-lightbulb"></i> ${pred.recommendation}</small>
                    </div>
                </div>
            `;
        });
    } else {
        html = '<div class="alert alert-info">No predictions available yet.</div>';
    }

    $('#predictions-container').html(html);
}
</script>
@endpush
```

---

## Add Dashboard Widget (Optional)

### For Student Dashboard

Location: `resources/views/admin/dashboard/index.blade.php`

```blade
@if(auth()->user()->type === 'student')
    <div class="row">
        {{-- Other dashboard widgets --}}

        <div class="col-lg-6 col-md-12">
            @php
                $student = auth()->user()->student;
                $predictionService = app(\App\Services\PerformancePredictionService::class);
                $predictions = $predictionService->predictStudentPerformance($student);
            @endphp
            <x-performance-prediction-widget :predictions="$predictions" />
        </div>
    </div>
@endif
```

---

## Permission Setup (Optional)

If using Spatie Laravel Permission or similar:

```php
// In your permission seeder or migration

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

// Create permission
Permission::create(['name' => 'view-predictions']);

// Assign to student role
$studentRole = Role::findByName('student');
$studentRole->givePermissionTo('view-predictions');

// Assign to teacher/admin roles
$teacherRole = Role::findByName('teacher');
$teacherRole->givePermissionTo('view-predictions');
```

Then use in routes:

```php
Route::middleware(['auth', 'permission:view-predictions'])->group(function () {
    Route::get('/predictions/my-predictions', [PerformancePredictionController::class, 'showMyPredictions'])
        ->name('admin.predictions.my-predictions');
});
```

---

## Testing the Integration

### 1. Check Route is Registered

```bash
php artisan route:list | grep predictions
```

### 2. Test Service

```php
// In tinker: php artisan tinker
$student = \App\Models\Student::first();
$service = new \App\Services\PerformancePredictionService();
$predictions = $service->predictStudentPerformance($student);
dd($predictions);
```

### 3. Test Controller

Visit: `http://your-app/admin/predictions/my-predictions`

### 4. Test API Endpoint

```bash
curl http://your-app/admin/predictions/api/my-predictions \
  -H "Cookie: your-session-cookie"
```

---

## Styling (Optional)

Add custom styles if needed:

```css
/* In your app.css or custom.css */

.prediction-widget {
  animation: fadeIn 0.5s;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.prediction-item {
  transition: all 0.3s ease;
}

.prediction-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.badge-ai {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}
```

---

## Quick Implementation Checklist

- [ ] Choose sidebar integration method
- [ ] Add menu item to sidebar
- [ ] Update student show view (optional)
- [ ] Add dashboard widget (optional)
- [ ] Set up permissions (optional)
- [ ] Test routes work
- [ ] Test predictions display
- [ ] Style as needed
- [ ] Train model with real data

---

## Need Help?

Check these files for reference:

- Service: `app/Services/PerformancePredictionService.php`
- Controller: `app/Http/Controllers/PerformancePredictionController.php`
- Widget: `resources/views/components/performance-prediction-widget.blade.php`
- Routes: `routes/web.php` (search for "predictions")
