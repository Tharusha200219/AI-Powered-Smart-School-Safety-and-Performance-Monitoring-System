<?php

namespace App\Repositories\Admin\Management;

use App\Models\Student;
use App\Models\Subject;
use App\Repositories\Interfaces\Admin\Management\StudentRepositoryInterface;
use Illuminate\Support\Facades\DB;

class StudentRepository implements StudentRepositoryInterface
{
    protected $model;

    public function __construct(Student $model)
    {
        $this->model = $model;
    }

    /**
     * Get all students
     */
    public function getAll()
    {
        return $this->model->with(['user', 'schoolClass', 'parents'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get student by ID
     */
    public function getById($id)
    {
        return $this->model->with(['user', 'schoolClass', 'parents', 'subjects'])
            ->where('student_id', $id)
            ->first();
    }

    /**
     * Create new student
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            if (!isset($data['student_code'])) {
                $data['student_code'] = $this->generateStudentCode();
            }

            $student = $this->model->create($data);

            // Assign subjects based on grade if provided
            if (isset($data['grade_level']) && isset($data['subjects'])) {
                $this->assignSubjects($student->student_id, $data['subjects'], $data['grade_level']);
            }

            return $student;
        });
    }

    /**
     * Update student
     */
    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $student = $this->model->where('student_id', $id)->first();

            if (!$student) {
                return false;
            }

            $oldGrade = $student->grade_level;
            $newGrade = $data['grade_level'] ?? $oldGrade;

            $student->update($data);

            // If grade changed, update subjects
            if ($oldGrade != $newGrade && isset($data['subjects'])) {
                $this->assignSubjects($id, $data['subjects'], $newGrade);
            }

            return $student;
        });
    }

    /**
     * Delete student
     */
    public function delete($id)
    {
        return $this->model->where('student_id', $id)->delete();
    }

    /**
     * Get students by grade level
     */
    public function getByGrade($grade)
    {
        return $this->model->with(['user', 'schoolClass'])
            ->where('grade_level', $grade)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get students by class
     */
    public function getByClass($classId)
    {
        return $this->model->with(['user', 'schoolClass'])
            ->where('class_id', $classId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Update student grade and subjects
     */
    public function updateGrade($id, $newGrade)
    {
        return DB::transaction(function () use ($id, $newGrade) {
            $student = $this->model->where('student_id', $id)->first();

            if (!$student) {
                return false;
            }

            // Update grade
            $student->update(['grade_level' => $newGrade]);

            // Get subjects for new grade
            $gradeSubjects = Subject::where('grade_level', $newGrade)->get();

            // Remove old subjects and assign new ones
            $student->subjects()->detach();

            if ($gradeSubjects->isNotEmpty()) {
                $this->assignSubjects($id, $gradeSubjects->pluck('id')->toArray(), $newGrade);
            }

            return $student;
        });
    }

    /**
     * Get student with relationships
     */
    public function getWithRelations($id)
    {
        return $this->model->with(['user', 'schoolClass', 'parents', 'subjects'])
            ->where('student_id', $id)
            ->first();
    }

    /**
     * Assign subjects to student
     */
    public function assignSubjects($studentId, array $subjectIds, $grade)
    {
        $student = $this->model->where('student_id', $studentId)->first();

        if (!$student) {
            return false;
        }

        $syncData = [];
        foreach ($subjectIds as $subjectId) {
            $syncData[$subjectId] = [
                'enrollment_date' => now(),
                'grade' => $grade,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        return $student->subjects()->sync($syncData);
    }

    /**
     * Generate student code
     */
    public function generateStudentCode()
    {
        return Student::generateStudentCode();
    }
}
