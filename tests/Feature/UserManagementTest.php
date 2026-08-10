<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Organisation;
use App\Models\Division;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_list_excludes_authenticated_user_own_account()
    {
        $org = Organisation::create([
            'name' => 'Org Test',
            'address' => 'Jakarta'
        ]);

        $superadmin = User::create([
            'organisation_id' => $org->id,
            'name' => 'SelfUserUniqueName',
            'email' => 'selfadmin@smkprestasiprima.sch.id',
            'password' => bcrypt('password'),
            'role' => 'superadmin'
        ]);

        $otherMember = User::create([
            'organisation_id' => $org->id,
            'name' => 'Other Member',
            'email' => 'other@smkprestasiprima.sch.id',
            'password' => bcrypt('password'),
            'role' => 'member'
        ]);

        $response = $this->actingAs($superadmin)->get(route('users.index'));

        $response->assertStatus(200);
        $response->assertSee('Other Member');
        $response->assertDontSee('selfadmin@smkprestasiprima.sch.id');
    }

    public function test_user_list_can_be_filtered_by_eskul_and_role_sub_tabs()
    {
        $org = Organisation::create([
            'name' => 'Org Filter',
            'address' => 'Jakarta'
        ]);

        $gameDiv = Division::create([
            'organisation_id' => $org->id,
            'name' => 'Game Development'
        ]);

        $webDiv = Division::create([
            'organisation_id' => $org->id,
            'name' => 'Web Development'
        ]);

        $pembina = User::create([
            'organisation_id' => $org->id,
            'name' => 'Pembina Account',
            'email' => 'pembina1@smkprestasiprima.sch.id',
            'password' => bcrypt('password'),
            'role' => 'pembina'
        ]);

        $gameMember = User::create([
            'organisation_id' => $org->id,
            'division_id' => $gameDiv->id,
            'name' => 'Game Member User',
            'email' => 'gamemember@smkprestasiprima.sch.id',
            'password' => bcrypt('password'),
            'role' => 'member'
        ]);

        $webMember = User::create([
            'organisation_id' => $org->id,
            'division_id' => $webDiv->id,
            'name' => 'Web Member User',
            'email' => 'webmember@smkprestasiprima.sch.id',
            'password' => bcrypt('password'),
            'role' => 'member'
        ]);

        // Filter by Game Development Eskul
        $response = $this->actingAs($pembina)->get(route('users.index', ['division_id' => $gameDiv->id]));
        $response->assertStatus(200);
        $response->assertSee('Game Member User');
        $response->assertDontSee('Web Member User');

        // Filter by Game Development Eskul + Member role sub-tab
        $response2 = $this->actingAs($pembina)->get(route('users.index', ['division_id' => $gameDiv->id, 'role' => 'member']));
        $response2->assertStatus(200);
        $response2->assertSee('Game Member User');
        $response2->assertDontSee('Web Member User');
    }

    public function test_cannot_create_user_with_division_from_another_organisation()
    {
        $org1 = Organisation::create(['name' => 'Org 1', 'address' => 'Jakarta']);
        $org2 = Organisation::create(['name' => 'Org 2', 'address' => 'Bandung']);

        $divOrg2 = Division::create(['organisation_id' => $org2->id, 'name' => 'Div Org 2']);

        $superadmin = User::create([
            'name' => 'Superadmin',
            'email' => 'superadmin@smkprestasiprima.sch.id',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
        ]);

        $response = $this->actingAs($superadmin)->post(route('users.store'), [
            'name' => 'Test User Invalid Div',
            'email' => 'testinvaliddiv@smkprestasiprima.sch.id',
            'password' => 'password123',
            'role' => 'member',
            'organisation_id' => $org1->id,
            'division_id' => $divOrg2->id,
        ]);

        $response->assertSessionHasErrors(['division_id']);
    }

    public function test_pembina_can_create_pembina_account()
    {
        $org = Organisation::create(['name' => 'Org Pembina Test', 'address' => 'Jakarta']);

        $pembina = User::create([
            'organisation_id' => $org->id,
            'name' => 'Pembina Utama',
            'email' => 'pembinautama@smkprestasiprima.sch.id',
            'password' => bcrypt('password'),
            'role' => 'pembina',
        ]);

        $response = $this->actingAs($pembina)->post(route('users.store'), [
            'name' => 'Pembina Baru',
            'email' => 'pembinabaru@smkprestasiprima.sch.id',
            'password' => 'password123',
            'role' => 'pembina',
            'organisation_id' => $org->id,
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'Pembina Baru',
            'email' => 'pembinabaru@smkprestasiprima.sch.id',
            'role' => 'pembina',
            'organisation_id' => $org->id,
        ]);
    }

    public function test_pembina_can_purge_all_members_and_pengurus()
    {
        $org = Organisation::create(['name' => 'Org Purge Test', 'address' => 'Jakarta']);

        $pembina = User::create([
            'organisation_id' => $org->id,
            'name' => 'Pembina Purge',
            'email' => 'pembinapurge@smkprestasiprima.sch.id',
            'password' => bcrypt('password'),
            'role' => 'pembina',
        ]);

        $pengurus = User::create([
            'organisation_id' => $org->id,
            'name' => 'Pengurus Purge',
            'email' => 'penguruspurge@smkprestasiprima.sch.id',
            'password' => bcrypt('password'),
            'role' => 'pengurus',
        ]);

        $member = User::create([
            'organisation_id' => $org->id,
            'name' => 'Member Purge',
            'email' => 'memberpurge@smkprestasiprima.sch.id',
            'password' => bcrypt('password'),
            'role' => 'member',
        ]);

        $response = $this->actingAs($pembina)->delete(route('users.purge-members'));

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseMissing('users', ['id' => $pengurus->id]);
        $this->assertDatabaseMissing('users', ['id' => $member->id]);
        $this->assertDatabaseHas('users', ['id' => $pembina->id]);
    }
}
