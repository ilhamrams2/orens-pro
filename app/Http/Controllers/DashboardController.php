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

        $activeSessionsCountQuery = AttendanceSession::where('is_active', true)
            ->where('session_date', $today)
            ->where('start_time', '<=', $nowTime)
            ->where('end_time', '>=', $nowTime);

        if ($selectedOrgId) {
            $activeSessionsCountQuery->where('organisation_id', $selectedOrgId);
        }
        $activeSessionsCount = $activeSessionsCountQuery->count();

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

        $totalExpectedQuery = AttendanceSession::query();
        if ($selectedOrgId) {
            $totalExpectedQuery->where('organisation_id', $selectedOrgId);
        }
        $totalExpected = $totalExpectedQuery->get()->sum(function($session) {
            return User::where('organisation_id', $session->organisation_id)
                ->where(function($q) use ($session) {
                    if ($session->division_id) $q->where('division_id', $session->division_id);
                })
                ->where('role', 'member')
                ->count();
        });

        // 7-day Trend Data
        $trendData = [];
        $trendLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $trendLabels[] = now()->subDays($i)->format('d M');

            $dayCountQuery = Attendance::where('status', 'hadir')
                ->whereDate('checkin_time', $date);

            if ($selectedOrgId) {
                $dayCountQuery->whereHas('session', function($q) use ($selectedOrgId) {
                    $q->where('organisation_id', $selectedOrgId);
                });
            }

            $trendData[] = $dayCountQuery->count();
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
            'total_sessions' => $totalSessions,
            'total_attendances' => $totalAttendances,
            'attendance_rate' => $totalExpected > 0 ? round(($totalAttendances / $totalExpected) * 100, 2) : 0,
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

        // 7-day Trend Data
        $trendData = [];
        $trendLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $trendLabels[] = now()->subDays($i)->format('d M');
            $trendData[] = Attendance::where('status', 'hadir')
                ->whereDate('checkin_time', $date)
                ->whereHas('session', function($q) use ($orgId) {
                    $q->where('organisation_id', $orgId);
                })
                ->count();
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
            'total_sessions' => $totalSessions,
            'total_attendances' => $totalAttendances,
            'attendance_rate' => $totalExpected > 0 ? round(($totalAttendances / $totalExpected) * 100, 2) : 0,
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

        // 7-day Trend Data
        $trendData = [];
        $trendLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $trendLabels[] = now()->subDays($i)->format('d M');
            $trendData[] = Attendance::where('status', 'hadir')
                ->whereDate('checkin_time', $date)
                ->whereHas('session', function($q) use ($division) {
                    $q->where('division_id', $division->id);
                })
                ->count();
        }

        $divisionChartLabels = [$division->name];
        $divisionChartData = [$expectedCount > 0 ? round(($attendancesCount / $expectedCount) * 100, 2) : 0];

        return view('dashboard', [
            'title' => 'Dashboard',
            'organisation_name' => $user->organisation->name ?? 'Organisation',
            'division' => $division,
            'members_count' => $membersCount,
            'sessions_count' => $sessionsCount,
            'active_sessions' => $activeSessionsCount,
            'attendance_count' => $attendancesCount,
            'attendance_rate' => $expectedCount > 0 ? round(($attendancesCount / $expectedCount) * 100, 2) : 0,
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
