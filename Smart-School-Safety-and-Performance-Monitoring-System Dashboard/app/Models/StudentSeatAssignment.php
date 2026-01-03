<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentSeatAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'seating_arrangement_id',
        'student_id',
        'row_number',
        'seat_number',
        'seat_position',
    ];

    protected $casts = [
        'row_number' => 'integer',
        'seat_number' => 'integer',
    ];

    /**
     * Get the seating arrangement
     */
    public function seatingArrangement(): BelongsTo
    {
        return $this->belongsTo(SeatingArrangement::class, 'seating_arrangement_id');
    }

    /**
     * Get the student
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    /**
     * Scope to get seat assignment for a student
     */
    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Get formatted seat position
     */
    public function getFormattedSeatAttribute(): string
    {
        return "Row {$this->row_number}, Seat {$this->seat_number}";
    }
}
