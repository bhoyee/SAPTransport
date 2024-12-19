<?php
namespace Tests\Feature\Controllers;

use App\Models\Booking;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BookingEditControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles for the test
        Role::create(['name' => 'passenger']);
        Role::create(['name' => 'admin']);
    }

    public function test_edit_shows_correct_booking_for_authenticated_user()
    {
        // Create a user and assign the 'passenger' role
        $user = User::factory()->create([
            'email_verified_at' => now(), // Ensure email is verified
        ]);
        $user->assignRole('passenger');

        // Create a booking for this user
        $booking = Booking::create([
            'user_id' => $user->id,
            'service_type' => 'AirportTransfer',
            'trip_type' => 'oneway',
            'status' => 'pending',
            'vehicle_type' => 'sedan',
            'pickup_address' => 'Test address',
            'dropoff_address' => 'Test destination',
            'pickup_date' => '2024-12-31',
            'pickup_time' => '14:00',
            'number_adults' => 1,
            'booking_reference' => 'TESTREF123',
        ]);

        // Act as the user and access the edit page for the booking
        $response = $this->actingAs($user)->get(route('booking.edit', $booking->id));

        // Assert that the response is successful and the correct view is returned
        $response->assertStatus(200);
        $response->assertViewIs('passenger.editbooking');
        $response->assertViewHas('booking');
    }

    public function test_edit_redirects_for_unauthorized_user()
    {
        // Create a user with 'passenger' role
        $user = User::factory()->create();
        $user->assignRole('passenger');

        // Create another user (who will not have access to the booking)
        $anotherUser = User::factory()->create();
        $anotherUser->assignRole('passenger');

        // Create a booking for the first user
        $booking = Booking::create([
            'user_id' => $user->id,
            'service_type' => 'AirportTransfer',
            'trip_type' => 'oneway',
            'status' => 'pending',
            'vehicle_type' => 'sedan',
            'pickup_address' => 'Test address',
            'dropoff_address' => 'Test destination',
            'pickup_date' => '2024-12-31',
            'pickup_time' => '14:00',
            'number_adults' => 1,
            'booking_reference' => 'TESTREF123',
        ]);

        // Act as another user trying to access the booking they do not own
        $response = $this->actingAs($anotherUser)->get(route('booking.edit', $booking->id));

        // Assert the response is a redirect to the dashboard with an error message
        $response->assertRedirect(route('passenger.dashboard'));
        $response->assertSessionHas('error', 'Unauthorized access.');
    }

    public function test_update_updates_booking_for_authenticated_user()
    {
        // Create a user and assign the 'passenger' role
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole('passenger');
    
        // Create a booking for this user
        $booking = Booking::create([
            'user_id' => $user->id,
            'service_type' => 'AirportTransfer',
            'trip_type' => 'oneway',
            'status' => 'pending',
            'vehicle_type' => 'sedan',
            'pickup_address' => 'Test address',
            'dropoff_address' => 'Test destination',
            'pickup_date' => '2024-12-31',
            'pickup_time' => '14:00',
            'number_adults' => 1,
            'booking_reference' => 'TESTREF123',
        ]);
    
        // Prepare updated data
        $updatedData = [
            'service_type' => 'Charter Service',  // Match the actual value
            'trip_type' => 'round_trip',
            'vehicle_type' => 'SUV',
            'pickup_address' => 'New Address',
            'dropoff_address' => 'New Destination',
            'pickup_date' => '2025-01-01',
            'pickup_time' => '15:00',
            'number_adults' => 2,
        ];
    
        // Act as the user and submit the update request
        $response = $this->actingAs($user)->put(route('booking.update', $booking->id), $updatedData);
    
        // Assert that the response redirects back to the edit page with success message
        $response->assertRedirect(route('booking.edit', $booking->id));
        $response->assertSessionHas('success', 'Booking updated successfully.');
    
        // Assert that the booking data was actually updated
        $booking->refresh();
        $this->assertEquals($updatedData['service_type'], $booking->service_type);  // Match "Charter Service"
        $this->assertEquals($updatedData['trip_type'], $booking->trip_type);
        $this->assertEquals($updatedData['vehicle_type'], $booking->vehicle_type);
    }
    
    public function test_update_redirects_for_unauthorized_user()
    {
        // Create a user and assign the 'passenger' role
        $user = User::factory()->create();
        $user->assignRole('passenger');

        // Create another user (who will not have access to the booking)
        $anotherUser = User::factory()->create();
        $anotherUser->assignRole('passenger');

        // Create a booking for the first user
        $booking = Booking::create([
            'user_id' => $user->id,
            'service_type' => 'AirportTransfer',
            'trip_type' => 'oneway',
            'status' => 'pending',
            'vehicle_type' => 'sedan',
            'pickup_address' => 'Test address',
            'dropoff_address' => 'Test destination',
            'pickup_date' => '2024-12-31',
            'pickup_time' => '14:00',
            'number_adults' => 1,
            'booking_reference' => 'TESTREF123',
        ]);

        // Prepare updated data
        $updatedData = [
            'service_type' => 'Charter',
            'trip_type' => 'round_trip',
            'vehicle_type' => 'SUV',
            'pickup_address' => 'New Address',
            'dropoff_address' => 'New Destination',
            'pickup_date' => '2025-01-01',
            'pickup_time' => '15:00',
            'number_adults' => 2,
        ];

        // Act as another user trying to update the booking they do not own
        $response = $this->actingAs($anotherUser)->put(route('booking.update', $booking->id), $updatedData);

        // Assert the response is a redirect to the dashboard with an error message
        $response->assertRedirect(route('passenger.dashboard'));
        $response->assertSessionHas('error', 'Unauthorized access.');
    }

    public function test_show_shows_booking_details_for_authenticated_user()
    {
        // Create a user and assign the 'passenger' role
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $user->assignRole('passenger');

        // Create a booking for this user
        $booking = Booking::create([
            'user_id' => $user->id,
            'service_type' => 'AirportTransfer',
            'trip_type' => 'oneway',
            'status' => 'confirmed',
            'vehicle_type' => 'sedan',
            'pickup_address' => 'Test address',
            'dropoff_address' => 'Test destination',
            'pickup_date' => '2024-12-31',
            'pickup_time' => '14:00',
            'number_adults' => 1,
            'booking_reference' => 'TESTREF123',
        ]);

        // Act as the user and access the show page for the booking
        $response = $this->actingAs($user)->get(route('booking.show', $booking->id));

        // Assert that the response is successful and the correct view is returned
        $response->assertStatus(200);
        $response->assertViewIs('passenger.details');
        $response->assertViewHas('booking');
    }
}
