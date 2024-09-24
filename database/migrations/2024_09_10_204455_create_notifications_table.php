<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();  // Primary key
            $table->foreignId('user_id')  // Foreign key to users table
                  ->constrained('users')
                  ->onDelete('cascade');  // Cascade on delete
            $table->string('message', 255);  // Notification message
            $table->enum('type', ['booking', 'payment', 'message']);  // Notification type
            $table->enum('status', ['unread', 'read']);  // Notification status
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
        Schema::dropIfExists('notifications');
    }
}
