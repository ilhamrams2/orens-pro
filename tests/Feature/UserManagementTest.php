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
}
