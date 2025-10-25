<?php

namespace App\Http\Controllers\Admin\Management;

use App\Http\Controllers\Controller;
use App\DataTables\Admin\Management\MarkDataTable;
use App\Models\Mark;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MarkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(MarkDataTable $dataTable, Request $request)
    {
        // Render using DataTable for consistent UI with other management pages
        return $dataTable->render('admin.pages.management.marks.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $studentId = $request->get('student_id');
        $student = null;
        $subjects = collect();

        if ($studentId) {
            $student = Student::with(['subjects', 'schoolClass'])->findOrFail($studentId);
            $subjects = $student->subjects;
        }

        $students = Student::active()
            ->orderBy('grade_level')
            ->orderBy('first_name')
            ->get();

        $academicYears = $this->getAcademicYears();
        $terms = Mark::getTerms();
        $currentAcademicYear = Mark::getCurrentAcademicYear();

        return view('admin.management.marks.create', compact(
            'students',
            'student',
            'subjects',
            'academicYears',
            'terms',
            'currentAcademicYear'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,student_id',
            'subject_id' => 'required|exists:subjects,id',
            'academic_year' => 'required|string|max:20',
            'term' => 'required|integer|between:1,3',
            'marks' => 'required|numeric|min:0',
            'total_marks' => 'required|numeric|min:0|gt:0',
            'remarks' => 'nullable|string|max:500',
        ]);

        // Get student's grade level
        $student = Student::findOrFail($validated['student_id']);
        $validated['grade_level'] = $student->grade_level;
        $validated['entered_by'] = Auth::id();

        // Check if marks already exist for this combination
        $existingMark = Mark::where('student_id', $validated['student_id'])
            ->where('subject_id', $validated['subject_id'])
            ->where('academic_year', $validated['academic_year'])
            ->where('term', $validated['term'])
            ->first();

        if ($existingMark) {
            return back()->with('error', 'Marks already exist for this student, subject, and term. Please edit the existing entry.');
        }

        Mark::create($validated);

        return redirect()->route('admin.management.marks.index')
            ->with('success', 'Marks added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $mark = Mark::with(['student', 'subject', 'enteredBy'])->findOrFail($id);
        return view('admin.management.marks.show', compact('mark'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $mark = Mark::with(['student.subjects'])->findOrFail($id);
        $subjects = $mark->student->subjects;

        $academicYears = $this->getAcademicYears();
        $terms = Mark::getTerms();

        return view('admin.management.marks.edit', compact('mark', 'subjects', 'academicYears', 'terms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $mark = Mark::findOrFail($id);

        $validated = $request->validate([
            'marks' => 'required|numeric|min:0',
            'total_marks' => 'required|numeric|min:0|gt:0',
            'remarks' => 'nullable|string|max:500',
        ]);

        $validated['entered_by'] = Auth::id();

        $mark->update($validated);

        return redirect()->route('admin.management.marks.index')
            ->with('success', 'Marks updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $mark = Mark::findOrFail($id);
        $mark->delete();

        return redirect()->route('admin.management.marks.index')
            ->with('success', 'Marks deleted successfully.');
    }

    /**
     * Get student details and subjects for AJAX request
     */
    public function getStudentDetails(Request $request)
    {
        $studentId = $request->get('student_id');

        if (!$studentId) {
            return response()->json(['error' => 'Student ID is required'], 400);
        }

        $student = Student::with(['subjects', 'schoolClass'])
            ->findOrFail($studentId);

        return response()->json([
            'student_id' => $student->student_id,
            'student_code' => $student->student_code,
            'full_name' => $student->full_name,
            'grade_level' => $student->grade_level,
            'class_name' => $student->schoolClass ? $student->schoolClass->class_name : 'N/A',
            'section' => $student->section,
            'subjects' => $student->subjects->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'subject_code' => $subject->subject_code,
                    'subject_name' => $subject->subject_name,
                ];
            }),
        ]);
    }

    /**
     * Get academic years
     */
    private function getAcademicYears(): array
    {
        $currentYear = now()->year;
        $years = [];

        for ($i = -2; $i <= 2; $i++) {
            $year = $currentYear + $i;
            $years[] = $year . '-' . ($year + 1);
        }

        return $years;
    }
}
