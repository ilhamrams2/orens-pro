<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Organisation;
use App\Models\Division;
use App\Models\AttendanceSession;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_view_dashboard_with_dynamic_stats()
    {
        $org = Organisation::create([
            'name' => 'Org Alpha',
            'address' => 'Jakarta'
        ]);

        $superadmin = User::create([
            'organisation_id' => $org->id,
            'name' => 'Super Admin',
            'email' => 'superadmin@orens.pro',
            'password' => bcrypt('password'),
            'role' => 'superadmin'
        ]);

        $response = $this->actingAs($superadmin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Ringkasan Portal');
        $response->assertSee('Total Pengguna');
        $response->assertSee('% Aktif');
    }

    public function test_pembina_can_view_dashboard_with_dynamic_stats()
    {
        $org = Organisation::create([
            'name' => 'Org Beta',
            'address' => 'Bandung'
        ]);

        $pembina = User::create([
            'organisation_id' => $org->id,
            'name' => 'Pembina Test',
            'email' => 'pembina@orens.pro',
            'password' => bcrypt('password'),
            'role' => 'pembina'
        ]);

        $response = $this->actingAs($pembina)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Total Anggota');
        $response->assertSee('% Aktif');
    }

    public function test_pengurus_can_view_dashboard_with_dynamic_stats()
    {
        $org = Organisation::create([
            'name' => 'Org Gamma',
            'address' => 'Surabaya'
        ]);

        $division = Division::create([
            'organisation_id' => $org->id,
            'name' => 'Teknologi'
        ]);

        $pengurus = User::create([
            'organisation_id' => $org->id,
            'division_id' => $division->id,
            'name' => 'Pengurus Test',
            'email' => 'pengurus@orens.pro',
            'password' => bcrypt('password'),
            'role' => 'pengurus'
        ]);

        $response = $this->actingAs($pengurus)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Anggota Divisi');
        $response->assertSee('Kehadiran Divisi');
    }

    public function test_member_can_view_dashboard_with_dynamic_stats()
    {
        $org = Organisation::create([
            'name' => 'Org Delta',
            'address' => 'Semarang'
        ]);

        $member = User::create([
            'organisation_id' => $org->id,
            'name' => 'Member Test',
            'email' => 'member@orens.pro',
            'password' => bcrypt('password'),
            'role' => 'member'
        ]);

        $response = $this->actingAs($member)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Total Partisipasi');
        $response->assertSee('Tingkat Kehadiran');
    }

    public function test_attendance_rate_excludes_alpha_records()
    {
        $org = Organisation::create([
            'name' => 'Org Epsilon',
            'address' => 'Yogyakarta'
        ]);

        $pembina = User::create([
            'organisation_id' => $org->id,
            'name' => 'Pembina Epsilon',
            'email' => 'pembina_eps@orens.pro',
            'password' => bcrypt('password'),
            'role' => 'pembina'
        ]);

        $member1 = User::create([
            'organisation_id' => $org->id,
            'name' => 'Member 1',
            'email' => 'm1@orens.pro',
            'password' => bcrypt('password'),
            'role' => 'member'
        ]);

        $member2 = User::create([
            'organisation_id' => $org->id,
            'name' => 'Member 2',
            'email' => 'm2@orens.pro',
            'password' => bcrypt('password'),
            'role' => 'member'
        ]);

        $session = AttendanceSession::create([
            'organisation_id' => $org->id,
            'title' => 'Test Session',
            'session_date' => now()->toDateString(),
            'start_time' => '08:00:00',
            'end_time' => '10:00:00',
            'qr_token' => 'token123',
            'is_active' => false,
            'created_by' => $pembina->id,
        ]);

        // Member 1 is hadir, Member 2 is alpha
        Attendance::create([
            'session_id' => $session->id,
            'user_id' => $member1->id,
            'status' => 'hadir',
            'checkin_time' => now(),
        ]);

        Attendance::create([
            'session_id' => $session->id,
            'user_id' => $member2->id,
            'status' => 'alpha',
            'checkin_time' => null,
        ]);

        $response = $this->actingAs($pembina)->get('/dashboard');
        $response->assertStatus(200);

        // Total expected = 2 members * 1 session = 2
        // Only 1 is hadir (50%)
        $response->assertViewHas('attendance_rate', 50.0);
        $response->assertViewHas('total_attendances', 1);
    }
}
