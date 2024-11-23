<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_complete_profile_form()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get(route('complete.profile'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.complete-profile');
    }

    public function test_save_profile_successfully()
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $this->actingAs($user);

        $response = $this->post(route('complete.profile'), [
            'phone' => '1234567890',
            'gender' => 'male',
        ]);

        $response->assertRedirect(route('verification.notice'));
        $response->assertSessionHas('resent', true);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'phone' => '1234567890',
            'gender' => 'male',
        ]);
    }

    public function test_save_profile_without_sending_verification_email()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user);

        $response = $this->post(route('complete.profile'), [
            'phone' => '0987654321',
            'gender' => 'female',
        ]);

        $response->assertRedirect(route('passenger.dashboard'));
        $response->assertSessionMissing('resent');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'phone' => '0987654321',
            'gender' => 'female',
        ]);
    }

    public function test_save_profile_fails_due_to_validation_error()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post(route('complete.profile'), [
            'phone' => 'invalid-phone',
            'gender' => 'invalid-gender',
        ]);

        $response->assertSessionHasErrors(['phone', 'gender']);

        $this->assertDatabaseMissing('users', [
            'phone' => 'invalid-phone',
            'gender' => 'invalid-gender',
        ]);
    }
}
