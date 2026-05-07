<?php

namespace App\Policies;

use App\Models\AttendanceSession;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AttendanceSessionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['superadmin', 'admin', 'leader']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AttendanceSession $attendanceSession): bool
    {
        if ($user->role === 'superadmin') return true;

        if ($user->role === 'admin') {
            return $user->organisation_id === $attendanceSession->organisation_id;
        }

        if ($user->role === 'leader') {
            return $user->organisation_id === $attendanceSession->organisation_id 
                && $user->division_id === $attendanceSession->division_id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['superadmin', 'admin', 'leader']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AttendanceSession $attendanceSession): bool
    {
        if ($user->role === 'superadmin') return true;

        if ($user->role === 'admin') {
            return $user->organisation_id === $attendanceSession->organisation_id;
        }

        if ($user->role === 'leader') {
            return $user->organisation_id === $attendanceSession->organisation_id 
                && $user->division_id === $attendanceSession->division_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AttendanceSession $attendanceSession): bool
    {
        return $this->update($user, $attendanceSession);
    }
}
