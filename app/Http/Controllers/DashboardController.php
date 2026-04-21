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
        
        if ($user->role === 'superadmin') {
            return $this->superadminStats();
        } elseif ($user->role === 'admin') {
            return $this->adminStats($user);
        } elseif ($user->role === 'leader') {
            return $this->leaderStats($user);
        } else {
            return $this->memberStats($user);
        }
    }

    private function superadminStats()
    {
        $nowTime = now()->format('H:i:s');
        $today = now()->toDateString();

        $divisions = Division::withCount('users')->get();
        $totalUsers = User::count();
        $totalSessions = AttendanceSession::count();
        $totalAttendances = Attendance::count();

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

        $totalExpected = AttendanceSession::all()->sum(function($session) {
            return User::where('organisation_id', $session->organisation_id)
                ->where(function($q) use ($session) {
                    if ($session->division_id) $q->where('division_id', $session->division_id);
                })
                ->where('role', 'member')
                ->count();
        });

        return view('dashboard', [
            'organisation_name' => 'Global System Administration',
            'total_users' => $totalUsers,
            'total_sessions' => $totalSessions,
            'total_attendances' => $totalAttendances,
            'attendance_rate' => $totalExpected > 0 ? round(($totalAttendances / $totalExpected) * 100, 2) : 0,
            'active_sessions' => $activeSessionsCount,
            'division_stats' => $divisionStats,
            'recent_activity' => AttendanceSession::with('division')->latest()->take(5)->get()
        ]);
    }

    private function adminStats($user)
    {
        $nowTime = now()->format('H:i:s');
        $today = now()->toDateString();
        $orgId = $user->organisation_id;

        $divisions = Division::where('organisation_id', $orgId)->withCount(['users' => function($q) use ($orgId) {
            $q->where('organisation_id', $orgId);
        }])->get();
        
        $totalUsers = User::where('organisation_id', $orgId)->count();
        $totalSessions = AttendanceSession::where('organisation_id', $orgId)->count();
        $totalAttendances = Attendance::whereHas('session', function($q) use ($orgId) {
            $q->where('organisation_id', $orgId);
        })->count();

        // Active Sessions: is_active AND today AND currently within time window
        $activeSessionsCount = AttendanceSession::where('organisation_id', $orgId)
            ->where('is_active', true)
            ->where('session_date', $today)
            ->where('start_time', '<=', $nowTime)
            ->where('end_time', '>=', $nowTime)
            ->count();

        $divisionStats = $divisions->map(function ($division) use ($orgId) {
            $sessions = AttendanceSession::where('division_id', $division->id)->where('organisation_id', $orgId)->pluck('id');
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

        $totalExpected = AttendanceSession::where('organisation_id', $orgId)->get()->sum(function($session) use ($orgId) {
            return User::where('organisation_id', $orgId)
                ->where('role', 'member')
                ->where(function($q) use ($session) {
                    if ($session->division_id) $q->where('division_id', $session->division_id);
                })
                ->count();
        });

        return view('dashboard', [
            'organisation_name' => $user->organisation->name ?? 'Organisation',
            'total_users' => $totalUsers,
            'total_sessions' => $totalSessions,
            'total_attendances' => $totalAttendances,
            'attendance_rate' => $totalExpected > 0 ? round(($totalAttendances / $totalExpected) * 100, 2) : 0,
            'active_sessions' => $activeSessionsCount,
            'division_stats' => $divisionStats,
            'recent_activity' => AttendanceSession::where('organisation_id', $orgId)->with('division')->latest()->take(5)->get()
        ]);
    }

    private function leaderStats($user)
    {
        $division = $user->division;
        if (!$division) return abort(403, 'Anda tidak memiliki divisi.');
        $orgId = $user->organisation_id;

        $nowTime = now()->format('H:i:s');
        $today = now()->toDateString();

        $membersCount = User::where('division_id', $division->id)
            ->where('organisation_id', $orgId)
            ->count();
            
        $allSessions = AttendanceSession::where('division_id', $division->id)
            ->where('organisation_id', $orgId)
            ->get();
            
        $sessionsCount = $allSessions->count();
        $attendancesCount = Attendance::whereIn('session_id', $allSessions->pluck('id'))->count();
        $expectedCount = $sessionsCount * $membersCount;

        $activeSessionsCount = AttendanceSession::where('division_id', $division->id)
            ->where('organisation_id', $orgId)
            ->where('is_active', true)
            ->where('session_date', $today)
            ->where('start_time', '<=', $nowTime)
            ->where('end_time', '>=', $nowTime)
            ->count();

        return view('dashboard', [
            'organisation_name' => $user->organisation->name ?? 'Organisation',
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
            ->with(['organisation', 'attendances' => function($q) use ($user) {
                $q->where('user_id', $user->id);
            }])->get();
        $upcomingSessions = (clone $baseQuery)->where('session_date', '>', $today)->orderBy('session_date')->orderBy('start_time')->take(5)->get();
        
        $eligibleSessionsCount = $baseQuery->count();

        return view('dashboard', [
            'organisation_name' => $user->organisation->name ?? 'Organisation',
            'total_join' => $totalJoin,
            'attendance_rate' => $eligibleSessionsCount > 0 ? round(($totalJoin / $eligibleSessionsCount) * 100, 2) : 0,
            'recent_activity' => $attendances->take(5),
            'today_sessions' => $todaySessions,
            'upcoming_sessions' => $upcomingSessions
        ]);
    }
}
