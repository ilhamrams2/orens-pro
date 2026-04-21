<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    protected $fillable = [
        'user_id', 'qr_token', 'latitude', 'longitude', 'result'
    ];
}
