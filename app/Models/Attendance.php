<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id', 'session_id', 'checkin_time', 
        'latitude', 'longitude', 'distance', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function session()
    {
        return $this->belongsTo(AttendanceSession::class, 'session_id');
    }
}
