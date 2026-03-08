<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'event_date',
        'start_time',
        'end_time',
        'location',
        'created_by',
    ];

    protected $casts = [
        'event_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    /**
     * Get the user who created the event.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the attendance records for the event.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(EventAttendance::class);
    }
}
