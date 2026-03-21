<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Organisation;
use App\Models\Division;
use App\Models\AttendanceSession;
use App\Models\Attendance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Organisation
        $org = Organisation::create([
            'name' => 'Orens Solution',
            'address' => 'Prestasi Prima',
        ]);

        // 2. Create Divisions
        $game = Division::create(['organisation_id' => $org->id, 'name' => 'Game']);
        $web = Division::create(['organisation_id' => $org->id, 'name' => 'Web']);
        $cyber = Division::create(['organisation_id' => $org->id, 'name' => 'Cyber']);

        // 3. Create Users
        // Admin
        $admin = User::create([
            'organisation_id' => $org->id,
            'name' => 'Admin Orens',
            'email' => 'admin@smkprestasiprima.sch.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Leader Game
        $leaderGame = User::create([
            'organisation_id' => $org->id,
            'division_id' => $game->id,
            'name' => 'Leader Game',
            'email' => 'game@smkprestasiprima.sch.id',
            'password' => Hash::make('password'),
            'role' => 'leader',
        ]);

        // Member Cyber
        $memberCyber = User::create([
            'organisation_id' => $org->id,
            'division_id' => $cyber->id,
            'name' => 'Member Cyber',
            'email' => 'cyber@smaprestasiprima.sch.id',
            'password' => Hash::make('password'),
            'role' => 'member',
        ]);

        // 4. Create Attendance Sessions
        // Global Session
        $session1 = AttendanceSession::create([
            'organisation_id' => $org->id,
            'title' => 'Rapat Mingguan Orens',
            'session_date' => now()->toDateString(),
            'start_time' => '08:00',
            'end_time' => '10:00',
            'qr_token' => 'global-123',
            'is_active' => true,
            'created_by' => $admin->id,
        ]);

        // Game Specific Session
        $session2 = AttendanceSession::create([
            'organisation_id' => $org->id,
            'division_id' => $game->id,
            'title' => 'Project Game Development',
            'session_date' => now()->toDateString(),
            'start_time' => '13:00',
            'end_time' => '15:00',
            'qr_token' => 'game-456',
            'is_active' => true,
            'created_by' => $leaderGame->id,
        ]);

        // 5. Create Sample Attendance
        Attendance::create([
            'user_id' => $memberCyber->id,
            'session_id' => $session1->id,
            'checkin_time' => now(),
            'status' => 'hadir'
        ]);
        
        Attendance::create([
            'user_id' => $leaderGame->id,
            'session_id' => $session1->id,
            'checkin_time' => now(),
            'status' => 'hadir'
        ]);
    }
}
