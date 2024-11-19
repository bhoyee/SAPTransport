<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the registration form is displayed.
     */
    public function test_registration_form_is_displayed()
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    /**
     * Test user registration with valid data.
     */
    public function test_user_can_register_with_valid_data()
    {
        $response = $this->post(route('register.post'), [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '08123456789',
            'gender' => 'male',
        ]);
    
        // Assert the response redirects to the thank you page
        $response->assertRedirect(route('register.thankyou'));
    
        // Ensure the user is created in the database
        $this->assertDatabaseHas('users', [
            'email' => 'johndoe@example.com',
            'phone' => '08123456789',
        ]);
    }
    

    /**
     * Test user registration fails with an existing email.
     */
    public function test_registration_fails_if_email_already_exists()
    {
        User::factory()->create([
            'email' => 'johndoe@example.com',
        ]);

        $response = $this->post(route('register.post'), [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '08123456789',
            'gender' => 'male',
        ]);

        $response->assertSessionHas('error', 'A user with this email address already exists.');
    }

    /**
     * Test user registration fails with an existing phone number.
     */
    public function test_registration_fails_if_phone_already_exists()
    {
        User::factory()->create([
            'phone' => '08123456789',
        ]);

        $response = $this->post(route('register.post'), [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '08123456789',
            'gender' => 'male',
        ]);

        $response->assertSessionHas('error', 'A user with this phone number already exists.');
    }

    /**
     * Test user registration fails with invalid data.
     */
    public function test_registration_fails_with_invalid_data()
    {
        $response = $this->post(route('register.post'), [
            'name' => '', // Missing name
            'email' => 'invalid-email', // Invalid email
            'password' => 'pass', // Short password
            'password_confirmation' => 'password123', // Mismatched confirmation
            'phone' => '', // Missing phone
            'gender' => 'other', // Invalid gender
        ]);

        $response->assertSessionHasErrors([
            'name',
            'email',
            'password',
            'phone',
            'gender',
        ]);
    }

    /**
     * Test thank you page is displayed after successful registration.
     */
    public function test_thank_you_page_is_displayed()
    {
        $response = $this->get(route('register.thankyou'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.thankyou');
    }
}
