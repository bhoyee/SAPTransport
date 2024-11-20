<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class VerificationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_verification_notice_page_is_accessible()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $this->actingAs($user); 

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

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        $response = $this->get($verificationUrl);

        $response->assertRedirect(route('verification.success')); 
        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->assertEquals('active', $user->fresh()->status); 
    }

    public function test_verification_fails_with_invalid_hash()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $invalidVerificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => 'invalid-hash'] 
        );

        $response = $this->get($invalidVerificationUrl);

        $response->assertRedirect(route('verification.notice'));
        $response->assertSessionHas('error', 'Invalid verification link.'); 
    }

    public function test_verification_redirects_if_already_verified()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(), 
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        $response = $this->get($verificationUrl);

        $response->assertRedirect(route('verification.notice'));
        $response->assertSessionHas('success', 'Email is already verified.'); 
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
}