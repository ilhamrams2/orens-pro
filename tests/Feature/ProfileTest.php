<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Organisation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_profile_name_email_and_password()
    {
        $org = Organisation::create(['name' => 'Org Profile Test', 'address' => 'Jakarta']);

        $user = User::create([
            'organisation_id' => $org->id,
            'name' => 'Nama Lama',
            'email' => 'namalama@smkprestasiprima.sch.id',
            'password' => bcrypt('oldpassword'),
            'role' => 'pembina',
        ]);

        $response = $this->actingAs($user)->post(route('profile.update'), [
            'name' => 'Nama Baru',
            'email' => 'namabaru@smkprestasiprima.sch.id',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nama Baru',
            'email' => 'namabaru@smkprestasiprima.sch.id',
        ]);
    }
}
