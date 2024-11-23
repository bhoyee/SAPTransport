<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class SocialLoginControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create the necessary roles
        Role::create(['name' => 'passenger']);
    }

    public function test_redirect_to_google()
    {
        $response = $this->get(route('auth.google'));

        $response->assertRedirect();
        $this->assertStringContainsString('accounts.google.com', $response->headers->get('Location'));
    }

    public function test_handle_google_callback_for_new_user()
    {
        // Mock the Socialite response for a new user
        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn((object) [
                'id' => '123456789',
                'name' => 'New User',
                'email' => 'newuser@example.com',
            ]);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('complete.profile')); // Redirect to profile completion
        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
        ]);
    }

    public function test_handle_google_callback_for_existing_user()
    {
        // Create an existing user
        $user = User::factory()->create([
            'email' => 'existinguser@example.com',
            'status' => 'active', // Ensure status allows login
        ]);
    
        // Mock the Socialite response for the existing user
        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn((object) [
                'id' => '123456789',
                'name' => $user->name,
                'email' => $user->email,
            ]);
    
        // Simulate the user being authenticated
        $this->actingAs($user); // Use actingAs to set the authenticated context
    
        $response = $this->get(route('auth.google.callback'));
    
        $response->assertRedirect(route('passenger.dashboard')); // Assert redirection
        $this->assertAuthenticatedAs($user); // Assert user is authenticated
    }
    

    public function test_social_login_fails_for_suspended_account()
    {
        $user = User::factory()->create([
            'email' => 'suspended@example.com',
            'status' => 'suspend',
        ]);

        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn((object) [
                'id' => '123456789',
                'name' => $user->name,
                'email' => $user->email,
            ]);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', 'Your account has been suspended, please contact support.');
        $this->assertGuest();
    }

    public function test_social_login_fails_for_deleted_account()
    {
        $user = User::factory()->create([
            'email' => 'deleted@example.com',
            'status' => 'deleted',
        ]);

        Socialite::shouldReceive('driver->stateless->user')
            ->andReturn((object) [
                'id' => '123456789',
                'name' => $user->name,
                'email' => $user->email,
            ]);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', 'Your account has been removed from our system, contact support for further clarification.');
        $this->assertGuest();
    }

    public function test_handle_social_login_with_invalid_response()
    {
        Socialite::shouldReceive('driver->stateless->user')->andReturn(null);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', 'Unable to authenticate with Google. Please try again.');
        $this->assertGuest();
    }

    
}
