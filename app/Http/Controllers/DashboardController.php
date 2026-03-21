<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\AttendanceSession;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        if ($user->role === 'admin') {
            return $this->adminStats();
        } elseif ($user->role === 'leader') {
            return $this->leaderStats($user);
        } else {
            return $this->memberStats($user);
        }
    }

    private function adminStats()
    {
        $nowTime = now()->format('H:i:s');
        $today = now()->toDateString();

        $divisions = Division::withCount('users')->get();
        $totalUsers = User::count();
        $totalSessions = AttendanceSession::count();
        $totalAttendances = Attendance::count();

        // Active Sessions: is_active AND today AND currently within time window
        $activeSessionsCount = AttendanceSession::where('is_active', true)
            ->where('session_date', $today)
            ->where('start_time', '<=', $nowTime)
            ->where('end_time', '>=', $nowTime)
            ->count();

        $divisionStats = $divisions->map(function ($division) {
            $sessions = AttendanceSession::where('division_id', $division->id)->pluck('id');
            $attendancesCount = Attendance::whereIn('session_id', $sessions)->count();
            $expectedCount = $sessions->count() * $division->users_count;
            
            return [
                'id' => $division->id,
                'name' => $division->name,
                'user_count' => $division->users_count,
                'session_count' => $sessions->count(),
                'attendance_count' => $attendancesCount,
                'attendance_rate' => $expectedCount > 0 ? round(($attendancesCount / $expectedCount) * 100, 2) : 0
            ];
        });

        return view('dashboard', [
            'total_users' => $totalUsers,
            'total_sessions' => $totalSessions,
            'total_attendances' => $totalAttendances,
            'active_sessions' => $activeSessionsCount,
            'division_stats' => $divisionStats,
            'recent_activity' => AttendanceSession::with('division')->latest()->take(5)->get()
        ]);
    }

    private function leaderStats($user)
    {
        $division = $user->division;
        if (!$division) return abort(403, 'Anda tidak memiliki divisi.');

        $nowTime = now()->format('H:i:s');
        $today = now()->toDateString();

        $membersCount = User::where('division_id', $division->id)->count();
        $allSessions = AttendanceSession::where('division_id', $division->id)->get();
        $sessionsCount = $allSessions->count();
        $attendancesCount = Attendance::whereIn('session_id', $allSessions->pluck('id'))->count();
        $expectedCount = $sessionsCount * $membersCount;

        $activeSessionsCount = AttendanceSession::where('division_id', $division->id)
            ->where('is_active', true)
            ->where('session_date', $today)
            ->where('start_time', '<=', $nowTime)
            ->where('end_time', '>=', $nowTime)
            ->count();

        return view('dashboard', [
            'division' => $division,
            'members_count' => $membersCount,
            'sessions_count' => $sessionsCount,
            'active_sessions' => $activeSessionsCount,
            'attendance_count' => $attendancesCount,
            'attendance_rate' => $expectedCount > 0 ? round(($attendancesCount / $expectedCount) * 100, 2) : 0,
            'recent_activity' => $allSessions->take(5)
        ]);
    }

    private function memberStats($user)
    {
        $today = now()->toDateString();
        $attendances = Attendance::where('user_id', $user->id)->with('session')->latest()->get();
        $totalJoin = $attendances->count();

        $baseQuery = AttendanceSession::where('organisation_id', $user->organisation_id)
            ->where(function($q) use ($user) {
                $q->where('division_id', $user->division_id)->orWhereNull('division_id');
            })->where('is_active', true);

        $todaySessions = (clone $baseQuery)->where('session_date', $today)
            ->with(['attendances' => function($q) use ($user) {
                $q->where('user_id', $user->id);
            }])->get();
        $upcomingSessions = (clone $baseQuery)->where('session_date', '>', $today)->orderBy('session_date')->orderBy('start_time')->take(5)->get();
        
        return view('dashboard', [
            'total_join' => $totalJoin,
            'recent_activity' => $attendances->take(5),
            'today_sessions' => $todaySessions,
            'upcoming_sessions' => $upcomingSessions
        ]);
    }
}
