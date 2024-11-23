<?php

namespace Tests\Feature\Auth;

use App\Mail\PasswordResetSuccessMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ForgotPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_link_request_form()
    {
        $response = $this->get('/forgot-password');

        $response->assertOk();
        $response->assertViewIs('auth.passwords.email');
    }

    public function test_send_reset_link_email_with_valid_email()
    {
        $user = User::factory()->create();

        $response = $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', trans(Password::RESET_LINK_SENT));
    }

    public function test_send_reset_link_email_with_invalid_email()
    {
        $response = $this->post('/forgot-password', [
            'email' => 'invalid-email',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_send_reset_link_email_with_unregistered_email()
    {
        $response = $this->post('/forgot-password', [
            'email' => 'unregistered@example.com',
        ]);

        $response->assertSessionHasErrors('email', 'This email address is not registered.');
    }

    public function test_send_reset_link_email_with_unverified_email()
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $response = $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertSessionHasErrors('email', 'This email address is not verified. Please verify your email first.');
    }

    public function test_show_reset_form()
    {
        $response = $this->get('/reset-password/token');

        $response->assertOk();
        $response->assertViewIs('auth.passwords.reset');
        $response->assertViewHas('token', 'token');
    }

    public function test_reset_password_with_valid_data()
    {
        Mail::fake(); // Prevent actual emails from being sent

        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new_password',
            'password_confirmation' => 'new_password',
        ]);

        $response->assertRedirect(route('password.reset.success'));
        $response->assertSessionHas('status', trans(Password::PASSWORD_RESET));
        $this->assertTrue(Hash::check('new_password', $user->fresh()->password));

        Mail::assertSent(PasswordResetSuccessMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_reset_password_with_invalid_data()
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new', // Password too short
            'password_confirmation' => 'new',
        ]);

        $response->assertSessionHasErrors('password');
    }
}