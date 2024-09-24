<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();  // Primary key
            $table->foreignId('sender_id')  // Foreign key to users (sender)
                  ->constrained('users')
                  ->onDelete('cascade');  // Cascade on delete
            $table->foreignId('receiver_id')  // Foreign key to users (receiver)
                  ->constrained('users')
                  ->onDelete('cascade');  // Cascade on delete
            $table->text('message');  // Message content
            $table->enum('status', ['sent', 'received', 'read']);  // Message status
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
        Schema::dropIfExists('messages');
    }
}
