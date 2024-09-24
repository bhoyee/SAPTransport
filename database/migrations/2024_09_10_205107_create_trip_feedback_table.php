<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripFeedbackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_feedback', function (Blueprint $table) {
            $table->id();  // Primary key
            $table->foreignId('booking_id')  // Foreign key to bookings table
                  ->constrained('bookings')
                  ->onDelete('cascade');  // Cascade on delete
            $table->tinyInteger('rating')->checkBetween(1, 5);  // Rating from 1 to 5
            $table->text('comments')->nullable();  // Optional comments for the trip
            $table->timestamps();  // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trip_feedback');
    }
}
