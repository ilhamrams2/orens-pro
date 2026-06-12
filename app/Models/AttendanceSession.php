<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model
{
    protected $fillable = [
        'organisation_id', 'division_id', 'title', 'qr_token', 
        'session_date', 'start_time', 'end_time', 
        'latitude', 'longitude', 'radius', 'is_active', 'created_by'
    ];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class, 'organisation_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'session_id');
    }

    /**
     * Generate a dynamic QR token based on the static token and current time window.
     * Singapore Enterprise Standard: Rotating tokens every 30 seconds.
     */
    public function getDynamicTokenAttribute()
    {
        // Use static token as secret
        $secret = $this->qr_token;
        // 30-second window
        $window = floor(time() / 30);
        
        return hash_hmac('sha256', $secret, $window);
    }

    /**
     * Validate a dynamic token (allows current and previous window for network delay).
     */
    public function validateDynamicToken($token)
    {
        $secret = $this->qr_token;
        $window = floor(time() / 30);
        
        // Check current window
        if (hash_hmac('sha256', $secret, $window) === $token) {
            return true;
        }
        
        // Check previous window (tolerance for late scans)
        if (hash_hmac('sha256', $secret, $window - 1) === $token) {
            return true;
        }
        
        return false;
    }

    /**
     * Boot the model.
     */
    protected static function booted()
    {
        static::updated(function ($session) {
            // When a session is deactivated, fill absent members with 'alpha'
            if ($session->wasChanged('is_active') && !$session->is_active) {
                $session->fillAbsentMembersWithAlpha();
            }
        });
    }

    /**
     * Deactivate all active sessions that have passed their end time.
     */
    public static function deactivateExpiredSessions()
    {
        $today = now()->toDateString();
        $nowTime = now()->toTimeString();

        $expiredSessions = self::where('is_active', true)
            ->where(function($query) use ($today, $nowTime) {
                $query->where('session_date', '<', $today)
                      ->orWhere(function($q) use ($today, $nowTime) {
                          $q->where('session_date', $today)
                            ->where('end_time', '<', $nowTime);
                      });
            })
            ->get();

        foreach ($expiredSessions as $session) {
            $session->update(['is_active' => false]);
        }

        // Retroactive check: ensure all inactive sessions have absent members marked as alpha
        $inactiveSessions = self::where('is_active', false)->get();
        foreach ($inactiveSessions as $session) {
            $session->fillAbsentMembersWithAlpha();
        }
    }

    /**
     * Fill all eligible members who did not check in with 'alpha' status.
     */
    public function fillAbsentMembersWithAlpha()
    {
        // Find all members in the organisation/division
        $membersQuery = \App\Models\User::where('role', 'member')
            ->where('organisation_id', $this->organisation_id);

        if ($this->division_id) {
            $membersQuery->where('division_id', $this->division_id);
        }

        $memberIds = $membersQuery->pluck('id');

        // Find members who already have attendance record
        $attendedMemberIds = \App\Models\Attendance::where('session_id', $this->id)
            ->pluck('user_id')
            ->toArray();

        // Members who are absent
        $absentMemberIds = $memberIds->diff($attendedMemberIds);

        // Insert 'alpha' records
        $insertData = [];
        foreach ($absentMemberIds as $memberId) {
            $insertData[] = [
                'session_id' => $this->id,
                'user_id' => $memberId,
                'status' => 'alpha',
                'checkin_time' => null,
                'latitude' => null,
                'longitude' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($insertData)) {
            \App\Models\Attendance::insert($insertData);
        }
    }
}
