<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\AttendanceSession;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Organisation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        AttendanceSession::deactivateExpiredSessions();

        $user = $request->user();
        
        if ($user->role === 'superadmin') {
            return $this->superadminStats($request);
        } elseif ($user->role === 'pembina') {
            return $this->pembinaStats($user);
        } elseif ($user->role === 'pengurus') {
            return $this->pengurusStats($user);
        } else {
            return $this->memberStats($user);
        }
    }

    private function superadminStats(Request $request)
    {
        $nowTime = now()->format('H:i:s');
        $today = now()->toDateString();
        $selectedOrgId = $request->query('organisation_id');

        $organisations = Organisation::all();

        $divisionsQuery = Division::withCount(['users' => function($q) use ($selectedOrgId) {
            if ($selectedOrgId) {
                $q->where('organisation_id', $selectedOrgId);
            }
        }]);
        $usersQuery = User::query();
        $sessionsQuery = AttendanceSession::query();
        $attendancesQuery = Attendance::query();

        if ($selectedOrgId) {
            $divisionsQuery->where('organisation_id', $selectedOrgId);
            $usersQuery->where('organisation_id', $selectedOrgId);
            $sessionsQuery->where('organisation_id', $selectedOrgId);
            $attendancesQuery->whereHas('session', function($q) use ($selectedOrgId) {
                $q->where('organisation_id', $selectedOrgId);
            });
        }

        $divisions = $divisionsQuery->get();
        $totalUsers = $usersQuery->count();
        $totalSessions = $sessionsQuery->count();
        $totalAttendances = $attendancesQuery->count();

        // Active users (distinct users with at least 1 attendance)
        $activeUsersCountQuery = Attendance::query();
        if ($selectedOrgId) {
            $activeUsersCountQuery->whereHas('session', function($q) use ($selectedOrgId) {
                $q->where('organisation_id', $selectedOrgId);
            });
        }
        $activeUsersCount = $activeUsersCountQuery->distinct('user_id')->count('user_id');
        $usersActiveRate = $totalUsers > 0 ? round(($activeUsersCount / $totalUsers) * 100, 1) : 0;

        $activeSessionsCountQuery = AttendanceSession::where('is_active', true)
            ->where('session_date', $today)
            ->where('start_time', '<=', $nowTime)
            ->where('end_time', '>=', $nowTime);

        if ($selectedOrgId) {
            $activeSessionsCountQuery->where('organisation_id', $selectedOrgId);
        }
        $activeSessionsCount = $activeSessionsCountQuery->count();
        $sessionsActiveRate = $totalSessions > 0 ? round(($activeSessionsCount / $totalSessions) * 100, 1) : 0;

        $divisionStats = $divisions->map(function ($division) use ($selectedOrgId) {
            $sessionsQuery = AttendanceSession::where('division_id', $division->id);
            if ($selectedOrgId) {
                $sessionsQuery->where('organisation_id', $selectedOrgId);
            }
            $sessions = $sessionsQuery->pluck('id');
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

        $userCountsByOrg = User::where('role', 'member')
            ->selectRaw('organisation_id, count(*) as count')
            ->groupBy('organisation_id')
            ->pluck('count', 'organisation_id');

        $userCountsByDiv = User::where('role', 'member')
            ->whereNotNull('division_id')
            ->selectRaw('division_id, count(*) as count')
            ->groupBy('division_id')
            ->pluck('count', 'division_id');

        $totalExpectedQuery = AttendanceSession::query();
        if ($selectedOrgId) {
            $totalExpectedQuery->where('organisation_id', $selectedOrgId);
        }
        $totalExpected = $totalExpectedQuery->get()->sum(function($session) use ($userCountsByOrg, $userCountsByDiv) {
            if ($session->division_id) {
                return $userCountsByDiv[$session->division_id] ?? 0;
            }
            return $userCountsByOrg[$session->organisation_id] ?? 0;
        });

        $attendanceRate = $totalExpected > 0 ? round(($totalAttendances / $totalExpected) * 100, 1) : 0;

        // 7-day Trend Data (single aggregated DB query)
        $startDate = now()->subDays(6)->startOfDay();
        $endDate = now()->endOfDay();

        $trendCountsQuery = Attendance::where('status', 'hadir')
            ->whereBetween('checkin_time', [$startDate, $endDate]);

        if ($selectedOrgId) {
            $trendCountsQuery->whereHas('session', function($q) use ($selectedOrgId) {
                $q->where('organisation_id', $selectedOrgId);
            });
        }

        $rawTrend = $trendCountsQuery->selectRaw('DATE(checkin_time) as date, COUNT(*) as aggregate')
            ->groupBy('date')
            ->pluck('aggregate', 'date');

        $trendData = [];
        $trendLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $dateStr = $day->toDateString();
            $trendLabels[] = $day->format('d M');
            $trendData[] = (int) ($rawTrend[$dateStr] ?? 0);
        }

        $divisionChartLabels = [];
        $divisionChartData = [];
        foreach ($divisionStats as $stat) {
            $divisionChartLabels[] = $stat['name'];
            $divisionChartData[] = $stat['attendance_rate'];
        }

        $recentActivityQuery = AttendanceSession::with('division')->latest()->take(5);
        if ($selectedOrgId) {
            $recentActivityQuery->where('organisation_id', $selectedOrgId);
        }

        return view('dashboard', [
            'title' => 'Dashboard',
            'organisations' => $organisations,
            'selected_organisation_id' => $selectedOrgId,
            'organisation_name' => $selectedOrgId ? (Organisation::find($selectedOrgId)->name ?? 'Global') : 'Global System Administration',
            'total_users' => $totalUsers,
            'active_users_count' => $activeUsersCount,
            'users_active_rate' => $usersActiveRate,
            'total_sessions' => $totalSessions,
            'sessions_active_rate' => $sessionsActiveRate,
            'total_attendances' => $totalAttendances,
            'total_expected' => $totalExpected,
            'attendance_rate' => $attendanceRate,
            'active_sessions' => $activeSessionsCount,
            'division_stats' => $divisionStats,
            'recent_activity' => $recentActivityQuery->get(),
            'trend_labels' => $trendLabels,
            'trend_data' => $trendData,
            'division_chart_labels' => $divisionChartLabels,
            'division_chart_data' => $divisionChartData,
        ]);
    }

    private function pembinaStats($user)
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

        $activeUsersCount = Attendance::whereHas('session', function($q) use ($orgId) {
            $q->where('organisation_id', $orgId);
        })->distinct('user_id')->count('user_id');
        $usersActiveRate = $totalUsers > 0 ? round(($activeUsersCount / $totalUsers) * 100, 1) : 0;

        // Active Sessions: is_active AND today AND currently within time window
        $activeSessionsCount = AttendanceSession::where('organisation_id', $orgId)
            ->where('is_active', true)
            ->where('session_date', $today)
            ->where('start_time', '<=', $nowTime)
            ->where('end_time', '>=', $nowTime)
            ->count();
        $sessionsActiveRate = $totalSessions > 0 ? round(($activeSessionsCount / $totalSessions) * 100, 1) : 0;

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

        $userCountsByOrg = User::where('role', 'member')
            ->where('organisation_id', $orgId)
            ->count();

        $userCountsByDiv = User::where('role', 'member')
            ->where('organisation_id', $orgId)
            ->whereNotNull('division_id')
            ->selectRaw('division_id, count(*) as count')
            ->groupBy('division_id')
            ->pluck('count', 'division_id');

        $totalExpected = AttendanceSession::where('organisation_id', $orgId)->get()->sum(function($session) use ($userCountsByOrg, $userCountsByDiv) {
            if ($session->division_id) {
                return $userCountsByDiv[$session->division_id] ?? 0;
            }
            return $userCountsByOrg;
        });

        $attendanceRate = $totalExpected > 0 ? round(($totalAttendances / $totalExpected) * 100, 1) : 0;

        // 7-day Trend Data (single aggregated DB query)
        $startDate = now()->subDays(6)->startOfDay();
        $endDate = now()->endOfDay();

        $rawTrend = Attendance::where('status', 'hadir')
            ->whereBetween('checkin_time', [$startDate, $endDate])
            ->whereHas('session', function($q) use ($orgId) {
                $q->where('organisation_id', $orgId);
            })
            ->selectRaw('DATE(checkin_time) as date, COUNT(*) as aggregate')
            ->groupBy('date')
            ->pluck('aggregate', 'date');

        $trendData = [];
        $trendLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $dateStr = $day->toDateString();
            $trendLabels[] = $day->format('d M');
            $trendData[] = (int) ($rawTrend[$dateStr] ?? 0);
        }

        $divisionChartLabels = [];
        $divisionChartData = [];
        foreach ($divisionStats as $stat) {
            $divisionChartLabels[] = $stat['name'];
            $divisionChartData[] = $stat['attendance_rate'];
        }

        return view('dashboard', [
            'title' => 'Dashboard',
            'organisation_name' => $user->organisation->name ?? 'Organisation',
            'total_users' => $totalUsers,
            'active_users_count' => $activeUsersCount,
            'users_active_rate' => $usersActiveRate,
            'total_sessions' => $totalSessions,
            'sessions_active_rate' => $sessionsActiveRate,
            'total_attendances' => $totalAttendances,
            'total_expected' => $totalExpected,
            'attendance_rate' => $attendanceRate,
            'active_sessions' => $activeSessionsCount,
            'division_stats' => $divisionStats,
            'recent_activity' => AttendanceSession::where('organisation_id', $orgId)->with('division')->latest()->take(5)->get(),
            'trend_labels' => $trendLabels,
            'trend_data' => $trendData,
            'division_chart_labels' => $divisionChartLabels,
            'division_chart_data' => $divisionChartData,
        ]);
    }

    private function pengurusStats($user)
    {
        $division = $user->division;
        if (!$division) return abort(403, 'Anda tidak memiliki divisi.');
        $orgId = $user->organisation_id;

        $nowTime = now()->format('H:i:s');
        $today = now()->toDateString();

        $membersCount = User::where('division_id', $division->id)
            ->where('organisation_id', $orgId)
            ->count();

        $activeMembersCount = Attendance::whereHas('session', function($q) use ($division) {
            $q->where('division_id', $division->id);
        })->distinct('user_id')->count('user_id');
        $membersActiveRate = $membersCount > 0 ? round(($activeMembersCount / $membersCount) * 100, 1) : 0;
            
        $allSessions = AttendanceSession::where('division_id', $division->id)
            ->where('organisation_id', $orgId)
            ->get();
            
        $sessionsCount = $allSessions->count();
        $attendancesCount = Attendance::whereIn('session_id', $allSessions->pluck('id'))->count();
        $expectedCount = $sessionsCount * $membersCount;
        $attendanceRate = $expectedCount > 0 ? round(($attendancesCount / $expectedCount) * 100, 2) : 0;

        $activeSessionsCount = AttendanceSession::where('division_id', $division->id)
            ->where('organisation_id', $orgId)
            ->where('is_active', true)
            ->where('session_date', $today)
            ->where('start_time', '<=', $nowTime)
            ->where('end_time', '>=', $nowTime)
            ->count();
        $sessionsActiveRate = $sessionsCount > 0 ? round(($activeSessionsCount / $sessionsCount) * 100, 1) : 0;

        // 7-day Trend Data (single aggregated DB query)
        $startDate = now()->subDays(6)->startOfDay();
        $endDate = now()->endOfDay();

        $rawTrend = Attendance::where('status', 'hadir')
            ->whereBetween('checkin_time', [$startDate, $endDate])
            ->whereHas('session', function($q) use ($division) {
                $q->where('division_id', $division->id);
            })
            ->selectRaw('DATE(checkin_time) as date, COUNT(*) as aggregate')
            ->groupBy('date')
            ->pluck('aggregate', 'date');

        $trendData = [];
        $trendLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $dateStr = $day->toDateString();
            $trendLabels[] = $day->format('d M');
            $trendData[] = (int) ($rawTrend[$dateStr] ?? 0);
        }

        $divisionChartLabels = [$division->name];
        $divisionChartData = [$attendanceRate];

        return view('dashboard', [
            'title' => 'Dashboard',
            'organisation_name' => $user->organisation->name ?? 'Organisation',
            'division' => $division,
            'members_count' => $membersCount,
            'active_members_count' => $activeMembersCount,
            'members_active_rate' => $membersActiveRate,
            'sessions_count' => $sessionsCount,
            'sessions_active_rate' => $sessionsActiveRate,
            'active_sessions' => $activeSessionsCount,
            'attendance_count' => $attendancesCount,
            'expected_count' => $expectedCount,
            'attendance_rate' => $attendanceRate,
            'recent_activity' => $allSessions->take(5),
            'trend_labels' => $trendLabels,
            'trend_data' => $trendData,
            'division_chart_labels' => $divisionChartLabels,
            'division_chart_data' => $divisionChartData,
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
            'title' => 'Dashboard',
            'organisation_name' => $user->organisation->name ?? 'Organisation',
            'total_join' => $totalJoin,
            'attendance_rate' => $eligibleSessionsCount > 0 ? round(($totalJoin / $eligibleSessionsCount) * 100, 2) : 0,
            'recent_activity' => $attendances->take(5),
            'today_sessions' => $todaySessions,
            'upcoming_sessions' => $upcomingSessions
        ]);
    }
}
