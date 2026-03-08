<?php

namespace App\Http\Controllers\Admin\Management;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class EventController extends Controller
{
    public function index()
    {
        Session::put('title', 'Event Management');
        $events = Event::with('creator')->latest()->get();
        return view('admin.pages.management.events.index', compact('events'));
    }

    public function create()
    {
        Session::put('title', 'Create Event');
        return view('admin.pages.management.events.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'event_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        Event::create([
            'name' => $request->name,
            'event_date' => $request->event_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'location' => $request->location,
            'description' => $request->description,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.management.events.index')
            ->with('success', 'Event created successfully.');
    }

    public function edit(Event $event)
    {
        Session::put('title', 'Edit Event');
        return view('admin.pages.management.events.form', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
        ]);

        $event->update($validated);

        return redirect()->route('admin.management.events.index')
            ->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('admin.management.events.index')
            ->with('success', 'Event deleted successfully.');
    }

    /**
     * Show the live attendance scanning page for an event.
     */
    public function attendance(Event $event)
    {
        Session::put('title', 'Event Attendance: ' . $event->name);
        
        // Set this event as the active one for RFID scanning
        Cache::put('active_event_id', $event->id, now()->addHours(8));
        
        // Clear previous stale scan results
        Cache::forget('rfid_last_event_scan');
        
        $attendances = EventAttendance::with('student')
            ->where('event_id', $event->id)
            ->latest()
            ->get();

        return view('admin.pages.management.events.attendance', compact('event', 'attendances'));
    }

    /**
     * Deactivate event scanning mode.
     */
    public function stopScanning()
    {
        Cache::forget('active_event_id');
        return response()->json(['success' => true]);
    }

    /**
     * Poll for new scans (for the live UI).
     */
    public function pollScans(Event $event)
    {
        $lastScan = Cache::get('rfid_last_event_scan');
        
        if ($lastScan && $lastScan['event_id'] == $event->id) {
            return response()->json([
                'found' => true,
                'scan' => [
                    'student_name' => $lastScan['student_name'],
                    'student_code' => $lastScan['student_code'],
                    'grade' => $lastScan['grade'],
                    'class' => $lastScan['class'],
                    'check_in' => $lastScan['check_in'],
                    'check_out' => $lastScan['check_out'],
                    'time' => $lastScan['time'],
                    'type' => $lastScan['type'],
                    'message' => $lastScan['message'],
                    'status' => $lastScan['status'],
                    'scan_id' => $lastScan['scan_id'] ?? null,
                ]
            ]);
        }
        
        return response()->json(['found' => false]);
    }
}
