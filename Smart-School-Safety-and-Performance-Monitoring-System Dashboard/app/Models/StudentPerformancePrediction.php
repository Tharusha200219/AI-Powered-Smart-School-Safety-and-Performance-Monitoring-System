<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPerformancePrediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'academic_year',
        'term',
        'current_performance',
        'current_attendance',
        'predicted_performance',
        'prediction_trend',
        'confidence',
        'recommendations',
        'predicted_at',
    ];

    protected $casts = [
        'current_performance' => 'decimal:2',
        'current_attendance' => 'decimal:2',
        'predicted_performance' => 'decimal:2',
        'confidence' => 'decimal:2',
        'predicted_at' => 'datetime',
    ];

    /**
     * Get the student that owns the prediction
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    /**
     * Get the subject that owns the prediction
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }

    /**
     * Get prediction trend badge color
     */
    public function getTrendColorAttribute(): string
    {
        return match ($this->prediction_trend) {
            'improving' => 'success',
            'stable' => 'info',
            'declining' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get prediction trend icon
     */
    public function getTrendIconAttribute(): string
    {
        return match ($this->prediction_trend) {
            'improving' => 'trending_up',
            'stable' => 'trending_flat',
            'declining' => 'trending_down',
            default => 'help',
        };
    }

    /**
     * Scope to get latest predictions for a student
     */
    public function scopeLatestForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId)
            ->orderBy('predicted_at', 'desc');
    }

    /**
     * Scope to get predictions for current academic year
     */
    public function scopeCurrentYear($query, string $academicYear)
    {
        return $query->where('academic_year', $academicYear);
    }
}
