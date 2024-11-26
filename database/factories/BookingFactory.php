<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'service_type' => 'AirportTransfer', // Must match the enum value
            'trip_type' => 'airport_pickup', // Must match the enum value
            'status' => 'pending', // Ensure this is a valid enum value
            'pickup_address' => $this->faker->address,
            'dropoff_address' => $this->faker->address,
            'pickup_date' => $this->faker->date(),
            'pickup_time' => $this->faker->time(),
            'vehicle_type' => 'sedan',
            'number_adults' => $this->faker->numberBetween(1, 5),
            'number_children' => $this->faker->numberBetween(0, 3),
            'created_by' => $this->faker->email,
        ];
    }
}
