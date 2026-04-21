<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\AttendanceLog;
use App\Models\User;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
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
        $user = $request->user();

        // Support both regular and JSON requests (for QR/GPS)
        $qrToken = $request->input('qr_token');
        $lat = $request->input('latitude');
        $lng = $request->input('longitude');

        $isJson = $request->expectsJson();

        // Utility to log and respond
        $finish = function ($message, $success = true) use ($user, $qrToken, $lat, $lng, $isJson) {
            AttendanceLog::create([
                'user_id' => $user->id,
                'qr_token' => $qrToken ?? 'N/A',
                'latitude' => $lat,
                'longitude' => $lng,
                'result' => $message
            ]);
            return $this->response($message, $success, $isJson);
        };

        // 1. Security & Business Validation
        if (!$session->is_active) {
            return $finish('This session is no longer active.', false);
        }

        if ($user->role !== 'superadmin' && $session->organisation_id !== $user->organisation_id) {
            return $finish('This session is not for your organisation.', false);
        }

        if ($user->role !== 'superadmin' && $session->division_id && $session->division_id !== $user->division_id) {
            return $finish('You do not belong to this division.', false);
        }

        // 2. Schedule Validation
        $now = now();
        $startTime = \Carbon\Carbon::parse($session->session_date . ' ' . $session->start_time);
        $endTime = \Carbon\Carbon::parse($session->session_date . ' ' . $session->end_time);

        if ($now->lt($startTime->subMinutes(30))) {
            return $finish('Check-in is not yet open. Please try again 30 minutes before the session starts.', false);
        }

        if ($now->gt($endTime)) {
            return $finish('This session has already ended.', false);
        }

        // 3. QR Token Validation
        if ($session->qr_token !== $qrToken) {
            return $finish('Invalid QR Code. Please scan the official code.', false);
        }

        // 4. GPS Geofencing Validation
        if ($session->latitude && $session->longitude) {
            if (!$lat || !$lng) {
                return $finish('GPS coordinates are required for this session.', false);
            }

            $distance = $this->calculateDistance($session->latitude, $session->longitude, $lat, $lng);
            if ($distance > ($session->radius ?? 100)) {
                $roundedDist = round($distance);
                return $finish("You are too far from the location ($roundedDist meters away). Please get closer.", false);
            }
        }

        // 5. Save Attendance
        Attendance::updateOrCreate(
            ['session_id' => $session->id, 'user_id' => $user->id],
            [
                'status' => 'hadir', 
                'checkin_time' => now(),
                'latitude' => $lat,
                'longitude' => $lng
            ]
        );

        return $finish('Check-in successful! Welcome to ' . $session->title, true);
    }

    private function response($message, $success = true, $json = true)
    {
        if ($json) {
            return response()->json([
                'success' => $success,
                'message' => $message
            ], $success ? 200 : 400);
        }

        return $success 
            ? redirect()->route('dashboard')->with('success', $message)
            : back()->with('error', $message);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
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
