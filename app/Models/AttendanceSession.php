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
}
