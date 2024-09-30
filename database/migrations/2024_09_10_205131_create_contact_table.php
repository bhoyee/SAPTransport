<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactTable extends Migration
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
            $table->enum('status', ['open', 'closed'])->default('open');  // Status of the ticket
            $table->enum('department', ['support', 'sales', 'billing']);  // Department for the ticket
            $table->enum('priority', ['low', 'medium', 'high'])->default('low');  // Priority of the ticket
            $table->string('attachment')->nullable();  // File attachment for support ticket
            $table->string('fullname', 255);  // Full name of the person contacting
            $table->string('email_address', 100);  // Email address
            $table->string('phone_number', 15)->nullable();  // Phone number (optional)
            $table->text('message');  // Message content
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
        Schema::dropIfExists('contact');
    }
}
