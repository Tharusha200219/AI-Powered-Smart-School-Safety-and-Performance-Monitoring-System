<?php

namespace App\Repositories\Admin\Management;

use App\Models\SchoolClass;
use App\Repositories\Interfaces\Admin\Management\SchoolClassRepositoryInterface;
use Illuminate\Support\Facades\DB;

class SchoolClassRepository implements SchoolClassRepositoryInterface
{
    protected $model;

    public function __construct(SchoolClass $model)
    {
        $this->model = $model;
    }

    /**
     * Get all classes
     */
    public function getAll()
    {
        return $this->model->with(['classTeacher', 'students', 'subjects'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get class by ID
     */
    public function getById($id)
    {
        return $this->model->with(['classTeacher', 'students', 'subjects'])
            ->where('id', $id)
            ->first();
    }

    /**
     * Create new class
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            if (!isset($data['class_code'])) {
                $data['class_code'] = $this->generateClassCode();
            }

            $class = $this->model->create($data);

            // Assign subjects if provided
            if (isset($data['subjects'])) {
                $this->assignSubjects($class->id, $data['subjects']);
            }

            return $class;
        });
    }

    /**
     * Update class
     */
    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $class = $this->model->where('id', $id)->first();

            if (!$class) {
                return false;
            }

            $class->update($data);

            // Update subjects if provided
            if (isset($data['subjects'])) {
                $this->assignSubjects($id, $data['subjects']);
            }

            return $class;
        });
    }

    /**
     * Delete class
     */
    public function delete($id)
    {
        return $this->model->where('id', $id)->delete();
    }

    /**
     * Get classes by grade level
     */
    public function getByGrade($grade)
    {
        return $this->model->with(['classTeacher', 'students'])
            ->where('grade_level', $grade)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get classes by academic year
     */
    public function getByAcademicYear($year)
    {
        return $this->model->with(['classTeacher', 'students'])
            ->where('academic_year', $year)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get class with relationships
     */
    public function getWithRelations($id)
    {
        return $this->model->with(['classTeacher', 'students', 'subjects'])
            ->where('id', $id)
            ->first();
    }

    /**
     * Assign subjects to class
     */
    public function assignSubjects($classId, array $subjectIds)
    {
        $class = $this->model->where('id', $classId)->first();

        if (!$class) {
            return false;
        }

        return $class->subjects()->sync($subjectIds);
    }

    /**
     * Assign teacher to class
     */
    public function assignTeacher($classId, $teacherId)
    {
        $class = $this->model->where('id', $classId)->first();

        if (!$class) {
            return false;
        }

        return $class->update(['class_teacher_id' => $teacherId]);
    }

    /**
     * Generate class code
     */
    public function generateClassCode()
    {
        $year = date('Y');
        $lastClass = $this->model->whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastClass ? (int)substr($lastClass->class_code, -4) + 1 : 1;

        return 'CLS' . $year . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
