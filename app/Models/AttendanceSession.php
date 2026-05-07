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
}
