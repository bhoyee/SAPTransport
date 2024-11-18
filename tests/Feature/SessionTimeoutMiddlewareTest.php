<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SessionTimeoutMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_session_timeout_redirects_to_home()
    {
        // Create roles if not exist
        Role::firstOrCreate(['name' => 'passenger']);

        // Create user and assign passenger role
        $user = User::factory()->create();
        $user->assignRole('passenger');

        // Simulate session expiration
        $response = $this->actingAs($user)
            ->withSession(['lastActivityTime' => now()->subSeconds(7200)->timestamp]) // 2 hours idle
            ->get(route('passenger.dashboard'));

        $response->dump(); // Output response for debugging

        // Assert redirection to home
        $response->assertRedirect(route('home'));
    }

    public function test_idle_user_redirects_to_lock_screen()
    {
        // Create roles if not exist
        Role::firstOrCreate(['name' => 'passenger']);

        // Create user and assign passenger role
        $user = User::factory()->create();
        $user->assignRole('passenger');

        // Simulate idle user
        $response = $this->actingAs($user)
            ->withSession(['lastActivityTime' => now()->subSeconds(90)->timestamp]) // 90 seconds idle
            ->get(route('passenger.dashboard'));

        $response->dump(); // Output response for debugging

        // Assert redirection to lock screen
        $response->assertRedirect(route('lockscreen.show'));
    }

    public function test_active_user_access_dashboard()
    {
        // Create roles if not exist
        Role::firstOrCreate(['name' => 'passenger']);

        // Create user and assign passenger role
        $user = User::factory()->create();
        $user->assignRole('passenger');

        // Simulate active user
        $response = $this->actingAs($user)
            ->withSession(['lastActivityTime' => now()->timestamp]) // Active user
            ->get(route('passenger.dashboard'));

        $response->assertStatus(200); // Assert access is granted
    }
}
