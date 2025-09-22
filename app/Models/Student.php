<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\Status;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'student_id';

    protected $fillable = [
        'user_id',
        'student_code',
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'gender',
        'nationality',
        'religion',
        'home_language',
        'photo_path',
        'enrollment_date',
        'grade_level',
        'class_id',
        'section',
        'is_active',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'home_phone',
        'mobile_phone',
        'email',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'enrollment_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id', 'id');
    }

    public function parents()
    {
        return $this->belongsToMany(ParentModel::class, 'parent_student', 'student_id', 'parent_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'student_subject', 'student_id', 'subject_id')
            ->withPivot('enrollment_date', 'grade')
            ->withTimestamps();
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name);
    }

    public function getFullAddressAttribute()
    {
        return trim($this->address_line1 . ' ' . $this->address_line2 . ', ' . $this->city . ', ' . $this->state . ' ' . $this->postal_code . ', ' . $this->country);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByGrade($query, $gradeLevel)
    {
        return $query->where('grade_level', $gradeLevel);
    }

    public function scopeBySection($query, $section)
    {
        return $query->where('section', $section);
    }

    // Static methods
    public static function generateStudentCode()
    {
        // Get the latest student with the new 8-digit format (stu-00000xxx)
        $lastStudent = self::where('student_code', 'like', 'stu-0%')
            ->orderBy('student_id', 'desc')
            ->first();

        if (!$lastStudent) {
            $sequence = 1;
        } else {
            // Extract 8-digit number from codes like 'stu-00000001'
            $codeWithoutPrefix = substr($lastStudent->student_code, 4); // Remove 'stu-'
            $sequence = (int)$codeWithoutPrefix + 1;
        }

        return 'stu-' . str_pad($sequence, 8, '0', STR_PAD_LEFT);
    }
}
