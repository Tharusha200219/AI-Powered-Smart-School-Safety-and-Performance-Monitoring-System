<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_code',
        'subject_name',
        'grade_level',
        'description',
        'credits',
        'type',
        'status',
    ];

    protected $casts = [
        'credits' => 'integer',
    ];

    /**
     * Get classes that use this subject
     */
    public function classes()
    {
        return $this->belongsToMany(SchoolClass::class, 'class_subject', 'subject_id', 'class_id')
            ->withTimestamps();
    }

    /**
     * Get teachers who teach this subject
     */
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_subject', 'subject_id', 'teacher_id')
            ->withTimestamps();
    }

    /**
     * Get students enrolled in this subject
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_subject', 'subject_id', 'student_id')
            ->withPivot('enrollment_date', 'grade')
            ->withTimestamps();
    }
}
