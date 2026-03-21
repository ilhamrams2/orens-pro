<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $fillable = ['organisation_id', 'name', 'description'];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class, 'organisation_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'division_id');
    }

    public function sessions()
    {
        return $this->hasMany(AttendanceSession::class, 'division_id');
    }
}
