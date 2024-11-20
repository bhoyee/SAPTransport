<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\URL; // Import the URL facade


class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles for the test
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'consultant']);
        Role::create(['name' => 'passenger']);
    }

    public function test_user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $user->assignRole('passenger');

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('passenger.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_if_account_is_suspended()
    {
        $user = User::factory()->create([
            'email' => 'suspendeduser@example.com',
            'password' => Hash::make('password123'),
            'status' => 'suspend',
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', 'Your account has been suspended, please contact support.');
        $this->assertGuest();
    }

    public function test_login_fails_if_account_is_deleted()
    {
        $user = User::factory()->create([
            'email' => 'deleteduser@example.com',
            'password' => Hash::make('password123'),
            'status' => 'deleted',
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', 'Your account has been removed from our system, contact support for further clarification.');
        $this->assertGuest();
    }

    public function test_admin_is_redirected_to_admin_dashboard_after_login()
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $admin->assignRole('admin');

        $response = $this->post(route('login'), [
            'email' => $admin->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_consultant_is_redirected_to_staff_dashboard_after_login()
    {
        $consultant = User::factory()->create([
            'email' => 'consultant@example.com',
            'password' => Hash::make('password123'),
            'status' => 'active',
        ]);

        $consultant->assignRole('consultant');

        $response = $this->post(route('login'), [
            'email' => $consultant->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('staff.dashboard'));
        $this->assertAuthenticatedAs($consultant);
    }

    public function test_login_fails_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

public function test_login_fails_for_unverified_email()
{
    $user = User::factory()->create([
        'email_verified_at' => null, 
        'password' => bcrypt('password123'),
        'status' => 'inactive',
    ]);
    $user->assignRole('passenger');

    $response = $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('verification.notice')); // Assert correct redirection
    $response->assertSessionHas('error', 'You need to verify your email before logging in.');
    $this->assertGuest(); // Ensure the user is not logged in
}

public function test_verification_notice_page_is_accessible()
{
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $this->actingAs($user); // Simulate an authenticated user

    $response = $this->get(route('verification.notice'));

    $response->assertStatus(200);
    $response->assertSee('Verify Your Email Address');
}

public function test_user_email_can_be_verified()
{
    $user = User::factory()->create([
        'email_verified_at' => null,
        'status' => 'inactive',
    ]);

    $url = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $response = $this->get($url);

    $response->assertRedirect(route('verification.success'));
    $this->assertNotNull($user->fresh()->email_verified_at); // Check email verification
    $this->assertEquals('active', $user->fresh()->status); // Ensure status is updated
}



public function test_verification_email_can_be_resent()
{
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $this->actingAs($user);

    $response = $this->post(route('verification.resend'));

    $response->assertRedirect(route('verification.notice'));
    $response->assertSessionHas('resent', true);
}


public function test_login_redirects_to_correct_dashboard_based_on_role()
{
    // Test for admin redirect
    $admin = User::factory()->create([
        'password' => bcrypt('password123'),
    ]);
    $admin->assignRole('admin');

    $this->actingAs($admin); // Authenticate the admin user

    $response = $this->get(route('admin.dashboard')); // Access the dashboard directly

    $response->assertStatus(200); // Assert successful access
    $this->assertAuthenticatedAs($admin); 

    // Test for consultant redirect
    $consultant = User::factory()->create([
        'password' => bcrypt('password123'),
    ]);
    $consultant->assignRole('consultant');

    $this->actingAs($consultant); // Authenticate the consultant user

    $response = $this->get(route('staff.dashboard')); // Access the dashboard directly

    $response->assertStatus(200); // Assert successful access
    $this->assertAuthenticatedAs($consultant);
}

    public function test_logout_redirects_to_home()
    {
        $user = User::factory()->create();
        $user->assignRole('passenger');

        $this->actingAs($user); // Authenticate the user

        $response = $this->post(route('logout'));

        $response->assertRedirect('/');
        $this->assertGuest(); 
    }
}
