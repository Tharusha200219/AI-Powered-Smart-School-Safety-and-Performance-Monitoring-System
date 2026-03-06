<?php

namespace App\Http\Controllers\Admin\Management;

use App\DataTables\Admin\Management\AttendanceDataTable;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\Admin\Management\AttendanceRepositoryInterface;
use App\Repositories\Interfaces\Admin\Management\StudentRepositoryInterface;
use App\Services\ArduinoNFCService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use GuzzleHttp\Client;

class AttendanceController extends Controller
{
    protected $attendanceRepository;
    protected $studentRepository;
    protected $arduinoService;

    public function __construct(
        AttendanceRepositoryInterface $attendanceRepository,
        StudentRepositoryInterface $studentRepository,
        ArduinoNFCService $arduinoService
    ) {
        $this->attendanceRepository = $attendanceRepository;
        $this->studentRepository = $studentRepository;
        $this->arduinoService = $arduinoService;
    }

    /**
     * Display attendance list with DataTable
     */
    public function index(AttendanceDataTable $dataTable, Request $request)
    {
        Session::put('title', 'Attendance Management');
        return $dataTable->render('admin.pages.management.attendance.index');
    }

    /**
     * Display the specified attendance record
     */
    public function show(string $id)
    {
        $attendance = $this->attendanceRepository->getById($id);

        if (!$attendance) {
            return redirect()->route('admin.management.attendance.index')
                ->with('error', 'Attendance record not found.');
        }

        return view('admin.pages.management.attendance.show', compact('attendance'));
    }

    /**
     * Show the form for editing the specified attendance record
     */
    public function edit(string $id)
    {
        $attendance = $this->attendanceRepository->getById($id);

        if (!$attendance) {
            return redirect()->route('admin.management.attendance.index')
                ->with('error', 'Attendance record not found.');
        }

        return view('admin.pages.management.attendance.edit', compact('attendance'));
    }

    /**
     * Update the specified attendance record
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
            'status' => 'required|in:present,absent,late,excused',
            'notes' => 'nullable|string|max:500'
        ]);

        $attendance = $this->attendanceRepository->getById($id);

        if (!$attendance) {
            return redirect()->route('admin.management.attendance.index')
                ->with('error', 'Attendance record not found.');
        }

        try {
            $updateData = [
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null
            ];

            if ($validated['check_in_time']) {
                $date = $attendance->attendance_date->format('Y-m-d');
                $updateData['check_in_time'] = Carbon::parse($date . ' ' . $validated['check_in_time']);
            }

            if ($validated['check_out_time']) {
                $date = $attendance->attendance_date->format('Y-m-d');
                $updateData['check_out_time'] = Carbon::parse($date . ' ' . $validated['check_out_time']);
            }

            $this->attendanceRepository->update($id, $updateData);

            return redirect()->route('admin.management.attendance.index')
                ->with('success', 'Attendance record updated successfully.');
        } catch (\Exception $e) {
            Log::error('Attendance update error: ' . $e->getMessage());
            return back()->with('error', 'Failed to update attendance record: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified attendance record
     */
    public function destroy(string $id)
    {
        try {
            $attendance = $this->attendanceRepository->getById($id);

            if (!$attendance) {
                return redirect()->route('admin.management.attendance.index')
                    ->with('error', 'Attendance record not found.');
            }

            $this->attendanceRepository->delete($id);

            return redirect()->route('admin.management.attendance.index')
                ->with('success', 'Attendance record deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Attendance delete error: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete attendance record: ' . $e->getMessage());
        }
    }

    /**
     * Display real-time attendance dashboard
     */
    public function dashboard()
    {
        $today = Carbon::today();

        // Get today's statistics
        $stats = $this->attendanceRepository->getStatistics($today);

        // Get recent check-ins (last 20)
        $recentCheckIns = $this->attendanceRepository->getToday()
            ->sortByDesc('check_in_time')
            ->take(20);

        // Load attendance method settings to control button visibility
        $setting = \App\Models\Setting::first();

        return view('admin.pages.management.attendance.dashboard', compact('stats', 'recentCheckIns', 'setting'));
    }

    /**
     * Show face recognition attendance interface
     */
    public function face()
    {
        // Pass settings so the face terminal can restrict its mode dropdown
        $setting = \App\Models\Setting::first();
        return view('admin.pages.management.attendance.face', compact('setting'));
    }

    /**
     * Show RFID attendance interface
     */
    public function rfid()
    {
        $setting = \App\Models\Setting::first();
        return view('admin.pages.management.attendance.rfid', compact('setting'));
    }

    /**
     * Show manual attendance form
     */
    public function create()
    {
        return view('admin.pages.management.attendance.create');
    }

    /**
     * Store manual attendance
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'student_code' => 'required|string',
                'attendance_type' => 'required|in:check_in,check_out,absent',
                'date' => 'nullable|date',
                'check_in_time' => 'nullable|date_format:H:i',
                'check_out_time' => 'nullable|date_format:H:i',
                'notes' => 'nullable|string|max:500'
            ]);

            // Find student by code
            $student = $this->studentRepository->findByCode($validated['student_code']);

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found with code: ' . $validated['student_code']
                ], 404);
            }

            $date = $validated['date'] ? Carbon::parse($validated['date']) : Carbon::today();
            $result = null;

            switch ($validated['attendance_type']) {
                case 'check_in':
                    $checkInTime = $validated['check_in_time']
                        ? Carbon::parse($date->format('Y-m-d') . ' ' . $validated['check_in_time'])
                        : now();

                    $userId = Auth::check() ? Auth::user()->user_id : null;

                    $result = $this->attendanceRepository->checkIn($student->student_id, [
                        'check_in_time' => $checkInTime,
                        'device_id' => 'manual',
                        'recorded_by' => $userId,
                        'notes' => $validated['notes'] ?? null
                    ]);
                    break;

                case 'check_out':
                    $checkOutTime = $validated['check_out_time']
                        ? Carbon::parse($date->format('Y-m-d') . ' ' . $validated['check_out_time'])
                        : now();

                    $result = $this->attendanceRepository->checkOut($student->student_id, [
                        'check_out_time' => $checkOutTime,
                        'notes' => $validated['notes'] ?? null
                    ]);
                    break;

                case 'absent':
                    $userId = Auth::check() ? Auth::user()->user_id : null;

                    $result = $this->attendanceRepository->markAbsent($student->student_id, $date, [
                        'recorded_by' => $userId,
                        'remarks' => $validated['notes'] ?? 'Manually marked absent'
                    ]);
                    break;
            }

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Attendance recorded successfully',
                    'data' => $result
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to record attendance'
            ], 400);
        } catch (\Exception $e) {
            Log::error('Manual attendance error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error recording attendance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search student by code (for manual entry)
     */
    public function searchStudent(Request $request)
    {
        $code = $request->get('code');

        if (!$code) {
            return response()->json([
                'success' => false,
                'message' => 'Student code is required'
            ], 400);
        }

        $student = $this->studentRepository->findByCode($code);

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        // Get today's attendance status
        $todayAttendance = $this->attendanceRepository->getTodayAttendance($student->student_id);

        return response()->json([
            'success' => true,
            'data' => [
                'student' => [
                    'student_id' => $student->student_id,
                    'student_code' => $student->student_code,
                    'first_name' => $student->first_name,
                    'last_name' => $student->last_name,
                    'full_name' => $student->first_name . ' ' . $student->last_name,
                    'grade_level' => $student->grade_level,
                    'class_name' => $student->schoolClass->class_name ?? 'N/A'
                ],
                'today_attendance' => $todayAttendance ? [
                    'status' => $todayAttendance->status,
                    'check_in_time' => $todayAttendance->check_in_time?->format('H:i:s'),
                    'check_out_time' => $todayAttendance->check_out_time?->format('H:i:s'),
                    'is_late' => $todayAttendance->is_late
                ] : null
            ]
        ]);
    }

    /**
     * NFC check-in/check-out endpoint
     */
    public function nfcScan(Request $request)
    {
        try {
            // --- Check if RFID is enabled ---
            $setting = \App\Models\Setting::first();
            if (!$setting || !$setting->attendance_rfid_enabled) {
                return response()->json([
                    'success' => false,
                    'message' => 'RFID attendance is currently disabled in system settings.',
                ], 403);
            }

            Log::info('RFID: Polling for NFC tag scan...');

            // Read NFC tag from Arduino
            $result = $this->arduinoService->readNFCTag();

            // --- Device not connected / port failure ---
            if (!$result['success'] && isset($result['message'])) {
                $msg = strtolower($result['message']);
                $isDeviceError = str_contains($msg, 'cannot connect')
                    || str_contains($msg, 'failed to open')
                    || str_contains($msg, 'no such file')
                    || str_contains($msg, 'permission denied')
                    || str_contains($msg, 'timeout')
                    || str_contains($msg, 'failed to read');

                if ($isDeviceError) {
                    Log::error('RFID: Arduino device not connected or port unavailable. Port: '
                        . $this->arduinoService->getSerialPort()
                        . ' | Message: ' . $result['message']);

                    return response()->json([
                        'success'      => false,
                        'device_error' => true,
                        'message'      => 'RFID reader not connected. Please check the Arduino device on port: '
                            . $this->arduinoService->getSerialPort(),
                        'port'         => $this->arduinoService->getSerialPort(),
                    ]);
                }

                // No tag was placed on the reader (normal polling "miss")
                Log::debug('RFID: No tag detected this poll. Message: ' . $result['message']);

                return response()->json([
                    'success'    => false,
                    'no_tag'     => true,
                    'message'    => $result['message'],
                ]);
            }

            // --- Tag read but no data ---
            if (!$result['data']) {
                Log::warning('RFID: Tag detected but data is empty or unrecognised. Raw: '
                    . ($result['raw'] ?? 'n/a'));

                return response()->json([
                    'success' => false,
                    'message' => 'Tag detected but could not read student data. Please re-enroll the card.',
                ]);
            }

            $rawCode = $result['data']['student_code'] ?? null;

            if (!$rawCode) {
                Log::warning('RFID: Tag read but student_code is missing. Data: ' . json_encode($result['data']));
                return response()->json([
                    'success' => false,
                    'message' => 'Tag has no student code stored. Please re-enroll the card.',
                ]);
            }

            // --- Look up student (Prioritize RFID Hex for speed/accuracy) ---
            // Try rfid_hex (UID) first for maximum accuracy
            $uid = strtoupper(trim($result['uid'] ?? $rawCode));
            Log::debug('RFID: Looking up student with normalized UID: ' . $uid);

            $student = $this->studentRepository->findByRfidHex($uid);
            
            if ($student) {
                Log::debug('RFID: Found student by rfid_hex: ' . $student->first_name);
            } else {
                Log::debug('RFID: Not found by rfid_hex. Trying student_code fallback matching...');
                $student = $this->studentRepository->findByCode($rawCode);
                if ($student) {
                    Log::debug('RFID: Found student by student_code: ' . $student->first_name);
                }
            }

            if (!$student) {
                Log::warning('RFID: Unrecognized tag scanned: ' . $rawCode . ' | UID: ' . $uid);
                // Log all student RFID hexes for debugging
                $allHexes = \App\Models\Student::whereNotNull('rfid_hex')->pluck('rfid_hex', 'first_name')->toArray();
                Log::debug('RFID: Current enrolled RFID hexes in DB: ' . json_encode($allHexes));

                return response()->json([
                    'success' => false,
                    'message' => 'This RFID tag is not assigned to any student.',
                ], 404);
            }


            // --- Check today's attendance & Enforce Exclusivity ---
            $todayAttendance = $this->attendanceRepository->getTodayAttendance($student->student_id);

            if ($todayAttendance && $todayAttendance->status !== 'absent') {
                // If already checked in today, verify method exclusivity
                $existingMethod = $todayAttendance->method;

                if ($existingMethod === 'face') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Attendance already marked via Face ID. Please use Face ID to check out.',
                    ], 403);
                }

                if ($todayAttendance->check_out_time) {
                    Log::info('RFID: Student ' . $student->first_name . ' already fully recorded today.');
                    return response()->json([
                        'success' => false,
                        'message' => 'Student already checked in and out today via RFID.',
                    ]);
                }

                // --- Second tap → Check out ---
                $attendance = $this->attendanceRepository->checkOut($student->student_id, [
                    'check_out_time' => now(),
                ]);

                Log::info('RFID: Check-out recorded for ' . $student->first_name . ' ' . $student->last_name);

                return response()->json([
                    'success' => true,
                    'action'  => 'check_out',
                    'message' => 'Student checked out successfully',
                    'data'    => [
                        'student'   => $student->first_name . ' ' . $student->last_name,
                        'class'     => $student->schoolClass->class_name ?? 'N/A',
                        'code'      => $student->student_code,
                        'check_in'  => $todayAttendance->check_in_time->format('H:i:s'),
                        'check_out' => now()->format('H:i:s'),
                    ],
                ]);
            }

            // --- First tap → Check in ---
            $now = now();
            $schoolStartTime = Carbon::createFromTimeString($setting->school_start_time ?? '08:00:00');
            $status = $now->greaterThan($schoolStartTime) ? 'late' : 'present';

            $attendance = $this->attendanceRepository->checkIn($student->student_id, [
                'check_in_time' => $now,
                'device_id'     => 'nfc',
                'method'        => 'rfid',
                'nfc_tag_id'    => $uid,
                'status'        => $status,
                'is_auto_recorded' => true,
            ]);

            Log::info('RFID: Check-in recorded for ' . $student->first_name . ' ' . $student->last_name);

            return response()->json([
                'success' => true,
                'action'  => 'check_in',
                'message' => 'Student checked in successfully',
                'data'    => [
                    'student'  => $student->first_name . ' ' . $student->last_name,
                    'class'    => $student->schoolClass->class_name ?? 'N/A',
                    'code'     => $student->student_code,
                    'time'     => $attendance->check_in_time->format('H:i:s'),
                    'is_late'  => $status === 'late',
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('RFID: Unexpected exception in nfcScan(): ' . $e->getMessage());
            return response()->json([
                'success'      => false,
                'device_error' => str_contains(strtolower($e->getMessage()), 'serial')
                    || str_contains(strtolower($e->getMessage()), 'port')
                    || str_contains(strtolower($e->getMessage()), 'arduino'),
                'message'      => 'RFID scan error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get attendance statistics
     */
    public function statistics(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $stats = $this->attendanceRepository->getStatistics(Carbon::parse($date));

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Generate attendance report
     */
    public function report(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'class_id' => 'nullable|exists:school_classes,class_id',
            'status' => 'nullable|in:present,absent,late,excused'
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);

        $filters = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $validated['status'] ?? null,
            'class_id' => $validated['class_id'] ?? null
        ];

        $attendances = $this->attendanceRepository->getReport($filters);

        return view('admin.pages.management.attendance.report', compact(
            'attendances',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Get student attendance percentage
     */
    public function studentPercentage($studentId, Request $request)
    {
        $startDate = $request->get('start_date')
            ? Carbon::parse($request->get('start_date'))
            : Carbon::now()->subDays(30);

        $endDate = $request->get('end_date')
            ? Carbon::parse($request->get('end_date'))
            : Carbon::today();

        $percentage = $this->attendanceRepository->getStudentAttendancePercentage(
            $studentId,
            $startDate,
            $endDate
        );

        return response()->json([
            'success' => true,
            'data' => [
                'percentage' => $percentage,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d')
            ]
        ]);
    }

    /**
     * Device management index page
     */
    public function devicesIndex()
    {
        return view('admin.pages.management.attendance.devices');
    }

    /**
     * Get list of registered devices
     */
    public function devicesList()
    {
        $devices = [];
        $cacheKeys = Cache::get('attendance_devices', []);

        foreach ($cacheKeys as $deviceId) {
            $deviceKey = "device_{$deviceId}";
            $deviceData = Cache::get($deviceKey);

            if ($deviceData) {
                $lastSeenKey = "device_{$deviceId}_last_seen";
                $lastSeen = Cache::get($lastSeenKey);

                $todayScans = $this->attendanceRepository->getAll()
                    ->where('device_id', $deviceId)
                    ->whereDate('created_at', Carbon::today())
                    ->count();

                $devices[] = [
                    'device_id' => $deviceId,
                    'device_name' => $deviceData['device_name'] ?? $deviceId,
                    'location' => $deviceData['location'] ?? null,
                    'last_seen' => $lastSeen,
                    'status' => $this->getDeviceStatus($lastSeen),
                    'today_scans' => $todayScans
                ];
            }
        }

        return response()->json([
            'success' => true,
            'devices' => $devices
        ]);
    }

    /**
     * Register a new device
     */
    public function devicesRegister(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|string|max:50',
            'device_name' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:100'
        ]);

        // Store device info
        $deviceKey = "device_{$validated['device_id']}";
        Cache::put($deviceKey, $validated, now()->addDays(365));

        // Add to devices list
        $devices = Cache::get('attendance_devices', []);
        if (!in_array($validated['device_id'], $devices)) {
            $devices[] = $validated['device_id'];
            Cache::put('attendance_devices', $devices, now()->addDays(365));
        }

        Log::info('Device registered via web interface', $validated);

        return response()->json([
            'success' => true,
            'message' => 'Device registered successfully',
            'device' => $validated
        ]);
    }

    /**
     * Sync device (placeholder - actual sync happens via API)
     */
    public function devicesSync(Request $request)
    {
        $deviceId = $request->input('device_id');

        // This would typically trigger a sync request to the device
        // For now, just return status

        return response()->json([
            'success' => true,
            'message' => 'Sync completed. Upload pending records from device SD card via API endpoint.'
        ]);
    }

    /**
     * Remove a device
     */
    public function devicesRemove(Request $request)
    {
        $deviceId = $request->input('device_id');

        // Remove device data
        $deviceKey = "device_{$deviceId}";
        Cache::forget($deviceKey);
        Cache::forget("device_{$deviceId}_last_seen");
        Cache::forget("device_{$deviceId}_status");

        // Remove from devices list
        $devices = Cache::get('attendance_devices', []);
        $devices = array_diff($devices, [$deviceId]);
        Cache::put('attendance_devices', $devices, now()->addDays(365));

        Log::info('Device removed', ['device_id' => $deviceId]);

        return response()->json([
            'success' => true,
            'message' => 'Device removed successfully'
        ]);
    }

    /**
     * Get today's attendance records (API-like endpoint)
     */
    public function getTodayAttendance(Request $request)
    {
        $attendances = $this->attendanceRepository->getToday();

        return response()->json([
            'success' => true,
            'data' => $attendances,
            'count' => $attendances->count()
        ]);
    }

    /**
     * Automatic face recognition and attendance marking
     */
    public function autoFaceRecognition(Request $request)
    {
        try {
            $validated = $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg|max:5120' // 5MB max
            ]);

            // --- Check if Face ID is enabled ---
            $setting = \App\Models\Setting::first();
            if (!$setting || !$setting->attendance_face_enabled) {
                return response()->json([
                    'success' => false,
                    'message' => 'Face recognition attendance is currently disabled in system settings.',
                ], 403);
            }

            $faceApiUrl = env('FACE_RECOGNITION_API_URL', $setting->face_recognition_api_url ?? 'http://localhost:5004');

            if (!$faceApiUrl) {
                return response()->json([
                    'success' => false,
                    'message' => 'Face recognition API not configured'
                ], 500);
            }

            // Send image to Python API for recognition
            $client = new Client();
            $faceApiKey = env('FACE_RECOGNITION_API_KEY', $setting->face_recognition_api_key ?? '');

            $response = $client->post($faceApiUrl . '/recognize_face', [
                'multipart' => [
                    [
                        'name' => 'image',
                        'contents' => fopen($request->file('image')->getPathname(), 'r'),
                        'filename' => $request->file('image')->getClientOriginalName()
                    ]
                ],
                'headers' => $faceApiKey ? ['Authorization' => 'Bearer ' . $faceApiKey] : [],
                'timeout' => 15
            ]);

            $result = json_decode($response->getBody(), true);

            if (!$result['success']) {
                // Check if no face was detected
                $noFace = !($result['face_detected'] ?? true);

                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Face not recognized',
                    'recognized' => false,
                    'no_face' => $noFace,
                    'face_detected' => $result['face_detected'] ?? false,
                    'bbox' => $result['bbox'] ?? null,
                    'confidence' => $result['confidence'] ?? 0,
                    'student_name' => 'Unknown'
                ]);
            }

            // Get student details
            $studentIdentifier = $result['student_id'];

            // Remove "DASH_" prefix if present
            if (str_starts_with($studentIdentifier, 'DASH_')) {
                $studentIdentifier = substr($studentIdentifier, 5);
            }

            // Try to find student
            $student = \App\Models\Student::where('student_code', $studentIdentifier)->first();
            if (!$student && is_numeric($studentIdentifier)) {
                $student = $this->studentRepository->getById($studentIdentifier);
            }

            if (!$student) {
                \Illuminate\Support\Facades\Log::warning('Face: Student not found in database', [
                    'api_id' => $result['student_id']
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found in database',
                    'recognized' => false,
                    'student_name' => $result['student_name'] ?? 'Unknown'
                ]);
            }

            // --- Check today's attendance & Enforce Exclusivity ---
            $todayAttendance = $this->attendanceRepository->getTodayAttendance($student->student_id);

            if ($todayAttendance && $todayAttendance->status !== 'absent') {
                // Verify method exclusivity
                if ($todayAttendance->method === 'rfid') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Attendance already marked via RFID. Please use your smart card to check out.',
                        'recognized' => true,
                        'student_name' => $student->first_name . ' ' . $student->last_name,
                    ], 403);
                }

                if ($todayAttendance->check_out_time) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Attendance already fully recorded today via Face ID.',
                        'recognized' => true,
                        'student_name' => $student->first_name . ' ' . $student->last_name,
                        'already_marked' => true,
                        'face_detected' => true
                    ]);
                }

                // --- Second recognized face → Check out ---
                $attendance = $this->attendanceRepository->checkOut($student->student_id, [
                    'check_out_time' => now(),
                ]);

                \Illuminate\Support\Facades\Log::info('Face check-out marked', ['id' => $student->student_id]);

                return response()->json([
                    'success' => true,
                    'action'  => 'check_out',
                    'message' => 'Student checked out successfully via Face ID',
                    'recognized' => true,
                    'student_name' => $student->first_name . ' ' . $student->last_name,
                    'check_in_time' => $todayAttendance->check_in_time->format('H:i:s'),
                    'check_out_time' => now()->format('H:i:s'),
                    'confidence' => $result['confidence'] ?? 0.95,
                    'bbox' => $result['bbox'] ?? null,
                    'face_detected' => true,
                    'data' => $attendance
                ]);
            }

            // --- First recognized face → Check in ---
            $now = \Carbon\Carbon::now();
            $schoolStartTime = \Carbon\Carbon::createFromTimeString($setting->school_start_time ?? '08:00:00');
            $status = $now->greaterThan($schoolStartTime) ? 'late' : 'present';

            $attendance = $this->attendanceRepository->checkIn($student->student_id, [
                'check_in_time' => $now->format('H:i:s'),
                'status' => $status,
                'method' => 'face',
                'device_id' => 'AUTO_FACE_RECOG_001',
                'notes' => 'Automatic Face Recognition Attendance'
            ]);

            \Illuminate\Support\Facades\Log::info('Face check-in marked', ['id' => $student->student_id]);

            return response()->json([
                'success' => true,
                'action'  => 'check_in',
                'message' => 'Attendance marked automatically via Face ID',
                'recognized' => true,
                'student_name' => $student->first_name . ' ' . $student->last_name,
                'status' => $status,
                'is_late' => $status === 'late',
                'check_in_time' => $now->format('H:i:s'),
                'confidence' => $result['confidence'] ?? 0.95,
                'bbox' => $result['bbox'] ?? null,
                'face_detected' => true,
                'data' => $attendance
            ]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            \Illuminate\Support\Facades\Log::error('Face recognition API request failed', [
                'error' => $e->getMessage(),
                'url' => $faceApiUrl ?? 'unknown'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Face recognition service unavailable',
                'recognized' => false,
                'student_name' => 'Unknown'
            ], 503);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Automatic face recognition failed', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Recognition failed',
                'recognized' => false,
                'student_name' => 'Unknown'
            ], 500);
        }
    }

    /**
     * Check if student has attendance today
     */
    public function checkAttendanceToday(Request $request, $studentId)
    {
        $todayAttendance = $this->attendanceRepository->getTodayAttendance($studentId);

        return response()->json([
            'has_attendance' => $todayAttendance && $todayAttendance->status !== 'absent',
            'attendance' => $todayAttendance
        ]);
    }

    /**
     * Get device status based on last seen time
     */
    private function getDeviceStatus($lastSeen)
    {
        if (!$lastSeen) {
            return 'offline';
        }

        $minutesAgo = Carbon::parse($lastSeen)->diffInMinutes(now());

        if ($minutesAgo < 2) {
            return 'online';
        } elseif ($minutesAgo < 10) {
            return 'idle';
        } else {
            return 'offline';
        }
    }
}
