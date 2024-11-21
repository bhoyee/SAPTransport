<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class PassengerControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_passenger_dashboard()
    {
        $response = $this->get('/passenger/dashboard');

        $response->assertRedirect(route('login'));
    }

    public function test_unverified_user_cannot_access_passenger_dashboard()
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $response = $this->actingAs($user)->get('/passenger/dashboard');

        $response->assertRedirect(route('verification.notice'));
    }

    public function test_user_without_passenger_role_cannot_access_dashboard()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/passenger/dashboard');
    
        $response->assertStatus(403); // Assert 403 Forbidden status
    }
    public function test_passenger_can_access_dashboard()
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'passenger']);
        $user->assignRole($role);

        $response = $this->actingAs($user)->get('/passenger/dashboard');

        $response->assertOk(); 
        $response->assertViewIs('passenger.dashboard');
    }
}