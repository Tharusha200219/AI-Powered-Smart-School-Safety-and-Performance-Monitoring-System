<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeatingArrangement extends Model
{
    use HasFactory;

    protected $fillable = [
        'grade_level',
        'section',
        'class_id',
        'academic_year',
        'term',
        'total_rows',
        'seats_per_row',
        'arrangement_data',
        'generated_by',
        'generated_at',
        'is_active',
    ];

    protected $casts = [
        'arrangement_data' => 'array',
        'generated_at' => 'datetime',
        'is_active' => 'boolean',
        'total_rows' => 'integer',
        'seats_per_row' => 'integer',
        'term' => 'integer',
    ];

    /**
     * Get the user who generated this arrangement
     */
    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by', 'id');
    }

    /**
     * Get the class for this arrangement
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id', 'id');
    }

    /**
     * Get all seat assignments for this arrangement
     */
    public function seatAssignments(): HasMany
    {
        return $this->hasMany(StudentSeatAssignment::class, 'seating_arrangement_id');
    }

    /**
     * Scope to get active arrangements
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get arrangement for specific grade and section
     */
    public function scopeForGradeSection($query, string $gradeLevel, ?string $section = null)
    {
        $query->where('grade_level', $gradeLevel);

        if ($section) {
            $query->where('section', $section);
        }

        return $query;
    }

    /**
     * Scope to get current academic year arrangements
     */
    public function scopeCurrentYear($query, string $academicYear)
    {
        return $query->where('academic_year', $academicYear);
    }

    /**
     * Get total seats
     */
    public function getTotalSeatsAttribute(): int
    {
        return $this->total_rows * $this->seats_per_row;
    }
}
