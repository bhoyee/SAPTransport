<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Mockery;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

use Illuminate\Support\Facades\DB;



class BookingControllerTest extends TestCase
{
    use RefreshDatabase;




    // Mock the configurations
    public function setUp(): void
    {
        parent::setUp();

        config(['app.faker_locale' => 'en_US']); // Mock the faker locale config
        config(['mail.admin_email' => 'admin@saptransport.com']); // Mock admin email
        Role::firstOrCreate(['name' => 'passenger']); // Create the 'passenger' role
    }

    public function test_store_booking_creates_booking_for_authenticated_user()
    {
        $user = User::factory()->create();  // Create a user
        $this->actingAs($user);  // Authenticate the user

        // Prepare booking data
        $bookingData = [
            'service_type' => 'AirportTransfer',
            'trip_type' => 'oneway',
            'vehicle_type' => 'sedan',
            'number_adults' => 1,
            'pickup_address' => 'Test address',
            'dropoff_address' => 'Test destination',
            'pickup_date' => '2024-12-31',
            'pickup_time' => '14:00',
        ];

        Mail::fake();  // Fake the emails to avoid actually sending them

        // Perform the booking store request
        $response = $this->post(route('booking.store'), $bookingData);

        // Assert that the response is successful and returns correct structure
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Assert that the booking is created in the database
        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'service_type' => 'AirportTransfer',
            'trip_type' => 'oneway',
            'vehicle_type' => 'sedan',
            'number_adults' => 1,
        ]);
    }

    public function test_store_booking_fails_for_unauthenticated_user()
    {
        // Try to perform the booking without authenticating a user
        $bookingData = [
            'service_type' => 'AirportTransfer',
            'trip_type' => 'oneway',
            'vehicle_type' => 'sedan',
            'number_adults' => 1,
            'pickup_address' => 'Test address',
            'dropoff_address' => 'Test destination',
            'pickup_date' => '2024-12-31',
            'pickup_time' => '14:00',
        ];

        $response = $this->post(route('booking.store'), $bookingData);

        // Assert that the response status is 401, as the user is not authenticated
        $response->assertStatus(401);
        $response->assertJson(['success' => false, 'error' => 'You need to login before booking a trip.']);
    }



    public function test_store_booking_validates_input()
    {
        $user = User::factory()->create();  // Create a user
        $this->actingAs($user);  // Authenticate the user

        // Prepare invalid booking data (missing required fields)
        $bookingData = [
            'service_type' => '',
            'trip_type' => '',
            'vehicle_type' => '',
            'number_adults' => 0,  // Invalid number
        ];

        $response = $this->post(route('booking.store'), $bookingData);

        // Assert that the response contains validation errors
        $response->assertSessionHasErrors(['service_type', 'trip_type', 'vehicle_type', 'number_adults']);
    }

    public function test_store_booking_redirects_for_unverified_user()
    {
        // Create the 'passenger' role if it doesn't exist
        $role = Role::findOrCreate('passenger'); 

        // Create a user with 'passenger' role and an unverified email
        $user = User::factory()->create(['email_verified_at' => null]);
        $user->assignRole($role); // Assign the role to the user
        $this->actingAs($user);

        // Prepare booking data
        $bookingData = [
            'service_type' => 'AirportTransfer',
            'trip_type' => 'oneway',
            'vehicle_type' => 'sedan',
            'number_adults' => 1,
            'pickup_address' => 'Test address',
            'dropoff_address' => 'Test destination',
            'pickup_date' => '2024-12-31',
            'pickup_time' => '14:00',
        ];

        $response = $this->post(route('booking.store'), $bookingData);

        // Assert that the user is redirected to the email verification page
        $response->assertRedirect(route('verification.notice')); 
    }


    
    public function test_check_status_with_invalid_reference()
    {
        // Make a POST request with an invalid booking reference
        $response = $this->postJson(route('booking.checkStatus'), ['booking_reference' => 'INVALIDREF']);

        // Assert a successful response (200) even for an invalid reference
        $response->assertStatus(200); 

        // Assert that the response JSON indicates an error
        $response->assertJson([
            'status' => 'error',
            'message' => 'No booking found with that reference number.'
        ]);
    }

    public function test_check_status_with_missing_reference()
    {
        // Make a POST request without a booking reference
        $response = $this->postJson(route('booking.checkStatus'), []); // Empty array for request body

        // Assert a successful response (200) even for a missing reference
        $response->assertStatus(200); 

        // Assert that the response JSON indicates an error
        $response->assertJson([
            'status' => 'error',
            'message' => 'Booking reference is required.' 
        ]);
    }

}
