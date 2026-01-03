<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeatingArrangement;
use App\Models\SchoolClass;
use App\Services\SeatingArrangementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SeatingArrangementController extends Controller
{
    protected SeatingArrangementService $seatingService;

    public function __construct(SeatingArrangementService $seatingService)
    {
        $this->seatingService = $seatingService;
    }

    /**
     * Display seating arrangement management page
     */
    public function index()
    {
        $arrangements = SeatingArrangement::with(['generatedBy', 'schoolClass'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get all classes grouped by grade
        $classesByGrade = SchoolClass::orderBy('grade_level')
            ->orderBy('section')
            ->get()
            ->groupBy('grade_level');

        return view('admin.pages.seating-arrangement.index', compact('arrangements', 'classesByGrade'));
    }

    /**
     * Show form to generate new seating arrangement
     */
    public function create()
    {
        // Get all classes grouped by grade
        $classesByGrade = SchoolClass::orderBy('grade_level')
            ->orderBy('section')
            ->get()
            ->groupBy('grade_level');

        // Get unique grade levels
        $gradeLevels = SchoolClass::distinct()->pluck('grade_level')->sort();

        return view('admin.pages.seating-arrangement.create', compact('classesByGrade', 'gradeLevels'));
    }

    /**
     * Generate seating arrangement
     */
    public function generate(Request $request)
    {
        $request->validate([
            'grade_level' => 'required|string',
            'section' => 'nullable|string',
            'class_id' => 'nullable|exists:classes,id',
            'academic_year' => 'required|string',
            'term' => 'required|integer|min:1|max:3',
            'seats_per_row' => 'required|integer|min:3|max:10',
            'total_rows' => 'required|integer|min:3|max:15',
        ]);

        try {
            // Check if API is available
            if (!$this->seatingService->checkApiHealth()) {
                return back()->with('error', 'Seating arrangement service is currently unavailable. Please ensure the Python API is running.');
            }

            $arrangement = $this->seatingService->generateSeatingArrangement(
                $request->grade_level,
                $request->section,
                $request->class_id,
                $request->academic_year,
                $request->term,
                $request->seats_per_row,
                $request->total_rows,
                Auth::id()
            );

            if ($arrangement) {
                return redirect()
                    ->route('admin.seating-arrangement.show', $arrangement->id)
                    ->with('success', 'Seating arrangement generated successfully!');
            } else {
                return back()->with('error', 'Failed to generate seating arrangement. Please check if students exist for the selected grade/section.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error generating seating arrangement: ' . $e->getMessage());
        }
    }

    /**
     * Display specific seating arrangement
     */
    public function show($id)
    {
        $arrangement = SeatingArrangement::with(['seatAssignments.student', 'generatedBy', 'schoolClass'])
            ->findOrFail($id);

        return view('admin.pages.seating-arrangement.show', compact('arrangement'));
    }

    /**
     * Delete seating arrangement
     */
    public function destroy($id)
    {
        $arrangement = SeatingArrangement::findOrFail($id);
        $arrangement->delete();

        return redirect()
            ->route('admin.seating-arrangement.index')
            ->with('success', 'Seating arrangement deleted successfully!');
    }

    /**
     * Toggle active status
     */
    public function toggleActive($id)
    {
        $arrangement = SeatingArrangement::findOrFail($id);

        // If activating, deactivate other arrangements for same class
        if (!$arrangement->is_active) {
            SeatingArrangement::where('grade_level', $arrangement->grade_level)
                ->where('section', $arrangement->section)
                ->where('academic_year', $arrangement->academic_year)
                ->where('term', $arrangement->term)
                ->where('id', '!=', $id)
                ->update(['is_active' => false]);
        }

        $arrangement->is_active = !$arrangement->is_active;
        $arrangement->save();

        return back()->with('success', 'Seating arrangement status updated!');
    }

    /**
     * API endpoint to get sections for a grade level
     */
    public function getSectionsForGrade(Request $request)
    {
        $gradeLevel = $request->get('grade');

        $sections = SchoolClass::where('grade_level', $gradeLevel)
            ->orderBy('section')
            ->pluck('section')
            ->unique()
            ->values();

        return response()->json($sections);
    }
}
