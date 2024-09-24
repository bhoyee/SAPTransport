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
            $table->enum('category', ['enquiry', 'feedback', 'emergency', 'query']);  // Contact message category
            $table->string('fullname', 255);  // Full name of the person contacting
            $table->string('phone_number', 15);  // Phone number
            $table->string('email_address', 100);  // Email address
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
