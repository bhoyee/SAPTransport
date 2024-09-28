<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();  // Primary key
            $table->foreignId('booking_id')  // Foreign key to bookings table
                  ->constrained('bookings')
                  ->onDelete('cascade');  // Cascade on delete
            $table->foreignId('user_id')  // Foreign key to users table
                  ->constrained('users')
                  ->onDelete('cascade');  // Cascade on delete
            $table->decimal('amount', 10, 2);  // Amount with precision (10,2)
            $table->enum('status', ['paid', 'unpaid', 'refunded', 'refund-pending']);  // Payment status
            $table->timestamp('payment_date');  // Timestamp for payment date
            $table->enum('payment_method', ['credit_card', 'bank_transfer', 'cash']);  // Payment method
            $table->timestamps();  // Adds created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
