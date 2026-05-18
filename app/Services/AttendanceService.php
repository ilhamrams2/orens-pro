<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\AttendanceLog;
use App\Models\User;
use Carbon\Carbon;

class AttendanceService
{
    /**
     * Process self check-in for a user.
     *
     * @param User $user
     * @param AttendanceSession $session
     * @param array $data [qr_token, latitude, longitude]
     * @return array [success => bool, message => string]
     */
    public function processSelfCheckIn(User $user, AttendanceSession $session, array $data): array
    {
        $qrToken = $data['qr_token'] ?? null;
        $lat = $data['latitude'] ?? null;
        $lng = $data['longitude'] ?? null;

        // 1. Basic Validations
        if (!$session->is_active) {
            return $this->logAndFail($user, $session, $data, 'This session is no longer active.');
        }

        if ($user->role !== 'superadmin' && $session->organisation_id !== $user->organisation_id) {
            return $this->logAndFail($user, $session, $data, 'This session is not for your organisation.');
        }

        if ($user->role !== 'superadmin' && $session->division_id && $session->division_id !== $user->division_id) {
            return $this->logAndFail($user, $session, $data, 'You do not belong to this division.');
        }

        // 2. Schedule Validation
        $now = now();
        $startTime = Carbon::parse($session->session_date . ' ' . $session->start_time);
        $endTime = Carbon::parse($session->session_date . ' ' . $session->end_time);

        if ($now->lt($startTime->subMinutes(30))) {
            return $this->logAndFail($user, $session, $data, 'Check-in is not yet open. Please try again 30 minutes before.');
        }

        if ($now->gt($endTime)) {
            return $this->logAndFail($user, $session, $data, 'This session has already ended.');
        }

        // 3. QR Token Validation (Singapore Enterprise Standard: Dynamic Tokens)
        if (!$session->validateDynamicToken($qrToken)) {
            return $this->logAndFail($user, $session, $data, 'Invalid or Expired QR Code. Please scan the current code.');
        }

        // 4. GPS Geofencing Validation
        if ($session->latitude && $session->longitude) {
            if (!$lat || !$lng) {
                return $this->logAndFail($user, $session, $data, 'GPS coordinates are required for this session.');
            }

            $distance = $this->calculateDistance($session->latitude, $session->longitude, $lat, $lng);
            if ($distance > ($session->radius ?? 100)) {
                $roundedDist = round($distance);
                return $this->logAndFail($user, $session, $data, "You are too far from the location ($roundedDist meters away).");
            }
        }

        // 5. Success - Save Attendance & Log atomically
        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($user, $session, $lat, $lng, $qrToken) {
                Attendance::updateOrCreate(
                    ['session_id' => $session->id, 'user_id' => $user->id],
                    [
                        'status' => 'hadir', 
                        'checkin_time' => now(),
                        'latitude' => $lat,
                        'longitude' => $lng
                    ]
                );

                AttendanceLog::create([
                    'user_id' => $user->id,
                    'qr_token' => $qrToken ?? 'N/A',
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'result' => 'Check-in successful'
                ]);
            });
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Check-in save failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal menyimpan data absensi. Silakan coba lagi.'];
        }

        return [
            'success' => true,
            'message' => 'Check-in successful! Welcome to ' . $session->title
        ];
    }

    /**
     * Generate cumulative report data for multiple sessions.
     * International Standard: Optimized with Caching and High-Speed Processing.
     */
    public function generateMultiReportData($sessions, int $organisationId): array
    {
        $sessionIds = $sessions->pluck('id')->sort()->values()->toArray();
        
        $lastAttendanceUpdate = Attendance::whereIn('session_id', $sessionIds)->max('updated_at') ?? 'never';
        $lastUserUpdate = User::where('organisation_id', $organisationId)->max('updated_at') ?? 'never';
        $lastSessionUpdate = AttendanceSession::whereIn('id', $sessionIds)->max('updated_at') ?? 'never';

        $cacheKey = 'report_' . md5(
            implode('_', $sessionIds) . '_' . 
            $lastAttendanceUpdate . '_' . 
            $lastUserUpdate . '_' . 
            $lastSessionUpdate
        );

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function() use ($sessions, $organisationId, $sessionIds) {
            $sessionDivisionIds = $sessions->pluck('division_id')->unique();
            $hasGlobalSession = $sessionDivisionIds->contains(null);

            $membersQuery = User::where('organisation_id', $organisationId)->where('role', 'member');
            if (!$hasGlobalSession) {
                $membersQuery->whereIn('division_id', $sessionDivisionIds);
            }

            $members = $membersQuery->with('division')->get();
            $attendances = Attendance::whereIn('session_id', $sessionIds)->get();
            
            $reportData = [];
            foreach ($members as $member) {
                $eligibleSessions = $sessions->filter(fn($s) => is_null($s->division_id) || $s->division_id == $member->division_id);
                $eligibleCount = $eligibleSessions->count();
                if ($eligibleCount === 0) continue;

                $presentCount = $attendances->where('user_id', $member->id)->where('status', 'hadir')->count();
                $reportData[$member->id] = [
                    'name' => $member->name,
                    'division' => $member->division->name ?? 'N/A',
                    'present' => $presentCount,
                    'total' => $eligibleCount,
                    'percentage' => $eligibleCount > 0 ? round(($presentCount / $eligibleCount) * 100, 2) : 0
                ];
            }

            $divisionStats = [];
            $divisionsQuery = \App\Models\Division::where('organisation_id', $organisationId);
            if (!$hasGlobalSession) $divisionsQuery->whereIn('id', $sessionDivisionIds);
            $divisions = $divisionsQuery->get();
            
            foreach ($divisions as $division) {
                $divisionMembers = $members->where('division_id', $division->id);
                if ($divisionMembers->isEmpty()) continue;

                $divSessionCount = $sessions->filter(fn($s) => is_null($s->division_id) || $s->division_id == $division->id)->count();
                $totalPossible = $divisionMembers->count() * $divSessionCount;
                $totalPresent = $attendances->whereIn('user_id', $divisionMembers->pluck('id'))->where('status', 'hadir')->count();
                
                if ($totalPossible > 0) {
                    $divisionStats[$division->name] = [
                        'present' => $totalPresent, 'total' => $totalPossible,
                        'percentage' => round(($totalPresent / $totalPossible) * 100, 2)
                    ];
                }
            }

            return [$reportData, $divisionStats];
        });
    }

    private function logAndFail(User $user, AttendanceSession $session, array $data, string $message): array
    {
        AttendanceLog::create([
            'user_id' => $user->id,
            'qr_token' => $data['qr_token'] ?? 'N/A',
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'result' => $message
        ]);

        return [
            'success' => false,
            'message' => $message
        ];
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
}
