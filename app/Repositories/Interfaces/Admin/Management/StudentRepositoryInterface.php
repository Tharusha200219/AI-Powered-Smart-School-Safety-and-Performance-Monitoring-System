<?php

namespace App\Repositories\Interfaces\Admin\Management;

interface StudentRepositoryInterface
{
    /**
     * Get all students
     */
    public function getAll();

    /**
     * Get student by ID
     */
    public function getById($id);

    /**
     * Create new student
     */
    public function create(array $data);

    /**
     * Update student
     */
    public function update($id, array $data);

    /**
     * Delete student
     */
    public function delete($id);

    /**
     * Get students by grade level
     */
    public function getByGrade($grade);

    /**
     * Get students by class
     */
    public function getByClass($classId);

    /**
     * Update student grade and subjects
     */
    public function updateGrade($id, $newGrade);

    /**
     * Get student with relationships
     */
    public function getWithRelations($id);

    /**
     * Assign subjects to student
     */
    public function assignSubjects($studentId, array $subjectIds, $grade);

    /**
     * Generate student code
     */
    public function generateStudentCode();
}
