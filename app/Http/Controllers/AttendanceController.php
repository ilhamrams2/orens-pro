<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\AttendanceLog;
use App\Models\User;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    protected $attendanceService;

    public function __construct(\App\Services\AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function markingSheet(Request $request, AttendanceSession $session)
    {
        $user = $request->user();
        
        // Security check
        if ($user->role !== 'superadmin' && $session->organisation_id !== $user->organisation_id) {
            abort(403);
        }
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
        // Only Superadmin or Admin (Pembina) can see reports
        if ($user->role !== 'superadmin' && $user->role !== 'admin') {
            abort(403);
        }
        // Admin must be from the same organisation
        if ($user->role === 'admin' && $session->organisation_id !== $user->organisation_id) {
            abort(403);
        }

        $session->load(['organisation', 'division', 'creator']);
        $members = User::where('organisation_id', $session->organisation_id)
            ->where(function($q) use ($session) {
                if ($session->division_id) {
                    $q->where('division_id', $session->division_id);
                }
            })
            ->where('role', 'member')
            ->orderBy('name')
            ->get();

        $attendances = Attendance::where('session_id', $session->id)->get()->keyBy('user_id');

        return view('sessions.report', compact('session', 'members', 'attendances'));
    }

    public function sessionLogs(Request $request, AttendanceSession $session)
    {
        $user = $request->user();
        if ($user->role !== 'superadmin' && $session->organisation_id !== $user->organisation_id) {
            abort(403);
        }

        $logs = AttendanceLog::where('qr_token', $session->qr_token)
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('attendance.logs', compact('session', 'logs'));
    }

    public function submitMarking(Request $request, AttendanceSession $session)
    {
        $user = $request->user();
        if ($user->role !== 'superadmin' && $session->organisation_id !== $user->organisation_id) {
            abort(403);
        }
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
        $result = $this->attendanceService->processSelfCheckIn(
            $request->user(),
            $session,
            $request->only(['qr_token', 'latitude', 'longitude'])
        );

        if ($request->expectsJson()) {
            return response()->json($result, $result['success'] ? 200 : 400);
        }

        return $result['success']
            ? redirect()->route('dashboard')->with('success', $result['message'])
            : back()->with('error', $result['message']);
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
