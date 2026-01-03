<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Services\PerformancePredictionService;
use App\Services\SeatingArrangementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    protected PerformancePredictionService $predictionService;
    protected SeatingArrangementService $seatingService;

    public function __construct(
        PerformancePredictionService $predictionService,
        SeatingArrangementService $seatingService
    ) {
        $this->predictionService = $predictionService;
        $this->seatingService = $seatingService;
    }

    /**
     * Display student dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return redirect()->route('home')->with('error', 'Student profile not found.');
        }

        // Get current academic year
        $currentYear = date('Y');
        $academicYear = $currentYear . '-' . ($currentYear + 1);
        $currentTerm = 1; // You might want to calculate this based on current date

        // Get seat assignment
        $seatAssignment = $student->seatAssignment;

        // Get performance predictions
        $predictions = $student->performancePredictions()
            ->with('subject')
            ->where('academic_year', $academicYear)
            ->where('term', $currentTerm)
            ->get();

        // Get marks
        $marks = $student->marks()
            ->with('subject')
            ->where('academic_year', $academicYear)
            ->where('term', $currentTerm)
            ->get();

        // Get attendance summary
        $attendanceSummary = $this->getAttendanceSummary($student, $academicYear);

        return view('student.dashboard', compact(
            'student',
            'seatAssignment',
            'predictions',
            'marks',
            'attendanceSummary',
            'academicYear',
            'currentTerm'
        ));
    }

    /**
     * Show performance page
     */
    public function performance()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return redirect()->route('home')->with('error', 'Student profile not found.');
        }

        // Get current academic year
        $currentYear = date('Y');
        $academicYear = $currentYear . '-' . ($currentYear + 1);
        $currentTerm = 1;

        // Get all predictions
        $predictions = $student->performancePredictions()
            ->with('subject')
            ->where('academic_year', $academicYear)
            ->orderBy('predicted_at', 'desc')
            ->get()
            ->groupBy('term');

        // Get all marks
        $marks = $student->marks()
            ->with('subject')
            ->where('academic_year', $academicYear)
            ->get()
            ->groupBy('term');

        return view('student.performance', compact('student', 'predictions', 'marks', 'academicYear'));
    }

    /**
     * Show seat assignment page
     */
    public function seatAssignment()
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return redirect()->route('home')->with('error', 'Student profile not found.');
        }

        // Get seat assignment
        $seatAssignment = $student->seatAssignment;

        return view('student.seat-assignment', compact('student', 'seatAssignment'));
    }

    /**
     * Get attendance summary for student
     */
    protected function getAttendanceSummary(Student $student, string $academicYear): array
    {
        list($startYear, $endYear) = explode('-', $academicYear);
        $startDate = \Carbon\Carbon::create($startYear, 7, 1);
        $endDate = \Carbon\Carbon::now(); // Up to today

        $totalDays = $student->attendance()
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->count();

        $presentDays = $student->attendance()
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->whereIn('status', ['present', 'late'])
            ->count();

        $absentDays = $student->attendance()
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->where('status', 'absent')
            ->count();

        $lateDays = $student->attendance()
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->where('status', 'late')
            ->count();

        $attendancePercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0;

        return [
            'total_days' => $totalDays,
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'late_days' => $lateDays,
            'attendance_percentage' => $attendancePercentage,
        ];
    }
}
