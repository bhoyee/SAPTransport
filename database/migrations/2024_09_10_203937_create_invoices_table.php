<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();  // Primary key
            $table->foreignId('booking_id')  // Foreign key to bookings table
                  ->constrained('bookings')
                  ->onDelete('cascade');  // Cascade on delete
            $table->foreignId('generated_by')  // Foreign key to users table (user who generated invoice)
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');  // Set null on delete
            $table->string('invoice_number', 50);  // Invoice number with max length of 50
            $table->timestamp('invoice_date');  // Timestamp for invoice date
            $table->decimal('amount', 10, 2);  // Amount with precision (10,2)
            $table->enum('status', ['Unpaid', 'Paid']);  // Payment status
            $table->string('file_path', 255);  // Path for storing the invoice file
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
        Schema::dropIfExists('invoices');
    }
}
