<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact', function (Blueprint $table) {
            $table->id();  // Primary key
            $table->string('ticket_num', 6)->unique();  // 6-digit ticket number
            $table->enum('status', ['open', 'closed'])->default('open');  // Ticket status
            $table->enum('department', ['support', 'sales', 'billing'])->nullable(); // Department
            $table->enum('priority', ['low', 'medium', 'high'])->default('low'); // Priority
            $table->string('attachment')->nullable(); // File attachment
            $table->unsignedBigInteger('operator_id')->nullable(); // Operator who responded
            $table->foreign('operator_id')->references('id')->on('users')->onDelete('set null');
            $table->string('fullname', 255); // Full name
            $table->string('email_address', 100); // Email address
            $table->string('phone_number', 15)->nullable(); // Phone number
            $table->string('subject')->nullable(); // Subject of the ticket
            $table->text('message'); // Message content
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact');
    }
};
