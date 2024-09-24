<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();  // id: Primary key, auto-increment
            $table->foreignId('user_id')  // Foreign key to users table
                  ->constrained('users')
                  ->onDelete('cascade');  // On delete, cascade
            $table->enum('service_type', ['AirportTransfer', 'Charter']);  // Service type
            $table->enum('trip_type', ['airport_pickup', 'airport_dropoff', 'oneway', 'round_trip']);  // Trip type
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed']);  // Booking status
            $table->string('airport_name')->nullable();  // Name of the airport (nullable)
            $table->string('vehicle_type', 100);  // Type of vehicle selected for the trip
            $table->string('pickup_address');  // Pickup address for the trip
            $table->string('dropoff_address');  // Dropoff address for the trip
            $table->date('pickup_date');  // Date of the pickup
            $table->time('pickup_time');  // Time of the pickup
            $table->integer('number_adults');  // Number of adults
            $table->integer('number_children')->nullable();  // Number of children (nullable)
            $table->timestamps();  // created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}
