<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Organisation;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    private $organisation;
    private $superadmin;
    private $pembina;
    private $pengurus;
    private $member;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organisation = Organisation::create([
            'name' => 'Orens Solution',
            'address' => 'Gedung A',
        ]);

        $this->superadmin = User::create([
            'name' => 'Superadmin Orens',
            'email' => 'superadmin@smkprestasiprima.sch.id',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
        ]);

        $this->pembina = User::create([
            'organisation_id' => $this->organisation->id,
            'name' => 'Pembina Orens',
            'email' => 'pembina1@smkprestasiprima.sch.id',
            'password' => bcrypt('password'),
            'role' => 'pembina',
        ]);

        $this->pengurus = User::create([
            'organisation_id' => $this->organisation->id,
            'name' => 'Pengurus Orens',
            'email' => 'pengurus@smkprestasiprima.sch.id',
            'password' => bcrypt('password'),
            'role' => 'pengurus',
        ]);

        $this->member = User::create([
            'organisation_id' => $this->organisation->id,
            'name' => 'Member Orens',
            'email' => 'member@smkprestasiprima.sch.id',
            'password' => bcrypt('password'),
            'role' => 'member',
        ]);
    }

    public function test_guest_cannot_access_audit_logs()
    {
        $response = $this->get(route('admin.audit-logs'));
        $response->assertRedirect('/login');
    }

    public function test_member_cannot_access_audit_logs()
    {
        $response = $this->actingAs($this->member)
            ->get(route('admin.audit-logs'));

        $response->assertStatus(403);
    }

    public function test_pengurus_cannot_access_audit_logs()
    {
        $response = $this->actingAs($this->pengurus)
            ->get(route('admin.audit-logs'));

        $response->assertStatus(403);
    }

    public function test_pembina_cannot_access_audit_logs()
    {
        $response = $this->actingAs($this->pembina)
            ->get(route('admin.audit-logs'));

        $response->assertStatus(403);
    }

    public function test_superadmin_can_access_audit_logs()
    {
        // Generate an audit log entry
        AuditLog::create([
            'user_id' => $this->superadmin->id,
            'event' => 'created',
            'auditable_type' => User::class,
            'auditable_id' => $this->member->id,
            'new_values' => ['name' => 'Member Orens'],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
        ]);

        $response = $this->actingAs($this->superadmin)
            ->get(route('admin.audit-logs'));

        $response->assertStatus(200);
        $response->assertSee('Log Audit');
        $response->assertSee('Superadmin Orens');
    }
}
