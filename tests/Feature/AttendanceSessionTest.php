<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Organisation;
use App\Models\Division;
use App\Models\AttendanceSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceSessionTest extends TestCase
{
    use RefreshDatabase;

    private $organisation;
    private $division;
    private $pembina;

    private $member;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organisation = Organisation::create([
            'name' => 'Orens Solution',
            'address' => 'Gedung A',
        ]);

        $this->division = Division::create([
            'organisation_id' => $this->organisation->id,
            'name' => 'Game Development',
        ]);

        $this->pembina = User::create([
            'organisation_id' => $this->organisation->id,
            'name' => 'Pembina Orens',
            'email' => 'pembina1@smkprestasiprima.sch.id',
            'password' => bcrypt('password'),
            'role' => 'pembina',
        ]);

        $this->member = User::create([
            'organisation_id' => $this->organisation->id,
            'division_id' => $this->division->id,
            'name' => 'Member Orens',
            'email' => 'member1@smkprestasiprima.sch.id',
            'password' => bcrypt('password'),
            'role' => 'member',
        ]);
    }

    public function test_expired_sessions_are_automatically_deactivated_and_absent_members_are_marked_alpha()
    {
        // Create an expired session that is marked as active in DB
        $expiredSession = AttendanceSession::create([
            'organisation_id' => $this->organisation->id,
            'division_id' => $this->division->id,
            'title' => 'Expired Session',
            'session_date' => now()->subDay()->toDateString(),
            'start_time' => '08:00:00',
            'end_time' => '10:00:00',
            'qr_token' => 'expired_token',
            'is_active' => true,
            'created_by' => $this->pembina->id,
        ]);

        // Create a running session (active and current)
        $runningSession = AttendanceSession::create([
            'organisation_id' => $this->organisation->id,
            'division_id' => $this->division->id,
            'title' => 'Running Session',
            'session_date' => now()->toDateString(),
            'start_time' => now()->subMinutes(10)->toTimeString(),
            'end_time' => now()->addMinutes(50)->toTimeString(),
            'qr_token' => 'running_token',
            'is_active' => true,
            'created_by' => $this->pembina->id,
        ]);

        $this->assertTrue((bool)$expiredSession->fresh()->is_active);
        $this->assertDatabaseMissing('attendances', [
            'session_id' => $expiredSession->id,
            'user_id' => $this->member->id,
        ]);

        // Access dashboard or run deactivation
        $response = $this->actingAs($this->pembina)->get('/dashboard');
        $response->assertStatus(200);

        // Assert that the expired session is now inactive
        $this->assertFalse((bool)$expiredSession->fresh()->is_active);

        // Assert that the currently running session is still active
        $this->assertTrue((bool)$runningSession->fresh()->is_active);

        // Assert that the member has been marked as 'alpha' for the expired session
        $this->assertDatabaseHas('attendances', [
            'session_id' => $expiredSession->id,
            'user_id' => $this->member->id,
            'status' => 'alpha',
        ]);
    }
}
