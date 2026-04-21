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
        // --- SUPERADMIN (Global) ---
        User::create([
            'name' => 'Superadmin Orens',
            'email' => 'superadmin@smkprestasiprima.sch.id',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
        ]);

        // --- ORGANISATION 1: ORENS SOLUTION ---
        $org1 = Organisation::create([
            'name' => 'Orens Solution',
            'address' => 'Gedung A, Lt 2',
        ]);

        $game = Division::create(['organisation_id' => $org1->id, 'name' => 'Game Development']);
        $web = Division::create(['organisation_id' => $org1->id, 'name' => 'Web Development']);
        $cyber = Division::create(['organisation_id' => $org1->id, 'name' => 'Cyber Security']);

        // Users Org 1
        $pembina1 = User::create([
            'organisation_id' => $org1->id,
            'name' => 'Pembina Orens Solution',
            'email' => 'pembina1@smkprestasiprima.sch.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $pengurusGame = User::create([
            'organisation_id' => $org1->id,
            'division_id' => $game->id,
            'name' => 'Pengurus Game',
            'email' => 'game@smkprestasiprima.sch.id',
            'password' => Hash::make('password'),
            'role' => 'leader',
        ]);

        // Loop for 3 members per division in Org 1
        $divisionsOrg1 = [
            ['name' => 'game', 'id' => $game->id, 'domain' => 'smkprestasiprima.sch.id'],
            ['name' => 'web', 'id' => $web->id, 'domain' => 'smkprestasiprima.sch.id'],
            ['name' => 'cyber', 'id' => $cyber->id, 'domain' => 'smaprestasiprima.sch.id'],
        ];

        foreach ($divisionsOrg1 as $div) {
            for ($i = 1; $i <= 3; $i++) {
                User::create([
                    'organisation_id' => $org1->id,
                    'division_id' => $div['id'],
                    'name' => ucfirst($div['name']) . " Member $i",
                    'email' => $div['name'] . $i . '@' . $div['domain'],
                    'password' => Hash::make('password'),
                    'role' => 'member',
                ]);
            }
        }

        // --- ORGANISATION 2: ORENS NETWORK ---
        $org2 = Organisation::create([
            'name' => 'Orens Network',
            'address' => 'Gedung B, Lt 1',
        ]);

        $server = Division::create(['organisation_id' => $org2->id, 'name' => 'Server & Cloud']);
        $infra = Division::create(['organisation_id' => $org2->id, 'name' => 'Network Infrastructure']);

        // Users Org 2
        $pembina2 = User::create([
            'organisation_id' => $org2->id,
            'name' => 'Pembina Orens Network',
            'email' => 'pembina2@smkprestasiprima.sch.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $pengurusServer = User::create([
            'organisation_id' => $org2->id,
            'division_id' => $server->id,
            'name' => 'Pengurus Server',
            'email' => 'server@smaprestasiprima.sch.id',
            'password' => Hash::make('password'),
            'role' => 'leader',
        ]);

        // Loop for 3 members per division in Org 2
        $divisionsOrg2 = [
            ['name' => 'server', 'id' => $server->id, 'domain' => 'smaprestasiprima.sch.id'],
            ['name' => 'infra', 'id' => $infra->id, 'domain' => 'smkprestasiprima.sch.id'],
        ];

        foreach ($divisionsOrg2 as $div) {
            for ($i = 1; $i <= 3; $i++) {
                User::create([
                    'organisation_id' => $org2->id,
                    'division_id' => $div['id'],
                    'name' => ucfirst($div['name']) . " Member $i",
                    'email' => $div['name'] . $i . '@' . $div['domain'],
                    'password' => Hash::make('password'),
                    'role' => 'member',
                ]);
            }
        }

        // --- SESSIONS ---
        // Session for Org 1 (Solution)
        AttendanceSession::create([
            'organisation_id' => $org1->id,
            'division_id' => $game->id,
            'title' => 'Workshop Game Dev Solution',
            'session_date' => now()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '12:00',
            'qr_token' => 'sol-game-1',
            'is_active' => true,
            'created_by' => $pengurusGame->id,
        ]);

        // Session for Org 2 (Network)
        AttendanceSession::create([
            'organisation_id' => $org2->id,
            'division_id' => $server->id,
            'title' => 'Server Maintenance Training',
            'session_date' => now()->toDateString(),
            'start_time' => '13:00',
            'end_time' => '16:00',
            'qr_token' => 'net-server-1',
            'is_active' => true,
            'created_by' => $pengurusServer->id,
        ]);
    }
}
