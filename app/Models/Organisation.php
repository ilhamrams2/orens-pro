<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organisation extends Model
{
    protected $fillable = ['name', 'description', 'has_division'];

    public function divisions()
    {
        return $this->hasMany(Division::class, 'organisation_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'organisation_id');
    }

    public function sessions()
    {
        return $this->hasMany(AttendanceSession::class, 'organisation_id');
    }
}
