<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\User;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function markingSheet(Request $request, AttendanceSession $session)
    {
        $user = $request->user();
        
        // Security check
        if ($user->role === 'leader' && $session->division_id !== $user->division_id) {
            abort(403);
        }

        // Get members of the organisation and division (or all users in org if global session)
        $query = User::where('role', 'member')
            ->where('organisation_id', $session->organisation_id);
            
        if ($session->division_id) {
            $query->where('division_id', $session->division_id);
        }
        
        $members = $query->get();
        
        // Get existing attendance for this session
        $attendances = Attendance::where('session_id', $session->id)->get()->keyBy('user_id');

        return view('attendance.marking', compact('session', 'members', 'attendances'));
    }

    public function report(Request $request, AttendanceSession $session)
    {
        $user = $request->user();
        if ($user->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'attendance' => 'required|array',
        ]);
    }

    public function submitMarking(Request $request, AttendanceSession $session)
    {
        $user = auth()->user();
        if ($user->role === 'leader' && $session->division_id !== $user->division_id) {
            abort(403);
        }

        $request->validate([
            'attendance' => 'required|array',
            'attendance.*' => 'required|in:hadir,sakit,izin,alpha',
        ]);

        foreach ($request->attendance as $userId => $status) {
            Attendance::updateOrCreate(
                ['session_id' => $session->id, 'user_id' => $userId],
                ['status' => $status, 'checkin_time' => $status === 'hadir' ? now() : null]
            );
        }

        return redirect()->route('sessions.index')->with('success', 'Attendance marked successfully.');
    }

    public function selfCheckIn(Request $request, AttendanceSession $session)
    {
        $user = $request->user();

        // Security & Business Validation
        if (!$session->is_active) {
            return back()->with('error', 'This session is no longer active.');
        }

        if ($session->organisation_id !== $user->organisation_id) {
            return back()->with('error', 'This session is not for your organisation.');
        }

        if ($session->division_id && $session->division_id !== $user->division_id) {
            return back()->with('error', 'You do not belong to this division.');
        }

        $now = now();
        $startTime = \Carbon\Carbon::parse($session->session_date . ' ' . $session->start_time);
        $endTime = \Carbon\Carbon::parse($session->session_date . ' ' . $session->end_time);

        if ($now->lt($startTime->subMinutes(30))) {
            return back()->with('error', 'Check-in is not yet open. Please try again 30 minutes before the session starts.');
        }

        if ($now->gt($endTime)) {
            return back()->with('error', 'This session has already ended.');
        }

        Attendance::updateOrCreate(
            ['session_id' => $session->id, 'user_id' => $user->id],
            ['status' => 'hadir', 'checkin_time' => now()]
        );

        return redirect()->route('dashboard')->with('success', 'Check-in successful! Welcome to ' . $session->title);
    }

    public function index(Request $request)
    {
        $attendances = Attendance::with(['session'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(15);

        return view('attendance.index', compact('attendances'));
    }
}
