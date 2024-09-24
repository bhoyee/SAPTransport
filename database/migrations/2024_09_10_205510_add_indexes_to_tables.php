<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Adding indexes to the messages table
        Schema::table('messages', function (Blueprint $table) {
            $table->index('sender_id');  // Add index to sender_id
            $table->index('receiver_id');  // Add index to receiver_id
            $table->index('status');  // Add index to status
        });

        // Adding indexes to the notifications table
        Schema::table('notifications', function (Blueprint $table) {
            $table->index('user_id');  // Add index to user_id
            $table->index('type');  // Add index to type
            $table->index('status');  // Add index to status
        });

        // Adding indexes to the contact table
        Schema::table('contact', function (Blueprint $table) {
            $table->index('category');  // Add index to category
            $table->index('email_address');  // Add index to email_address
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Dropping indexes from the messages table
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['sender_id']);  // Drop index on sender_id
            $table->dropIndex(['receiver_id']);  // Drop index on receiver_id
            $table->dropIndex(['status']);  // Drop index on status
        });

        // Dropping indexes from the notifications table
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['user_id']);  // Drop index on user_id
            $table->dropIndex(['type']);  // Drop index on type
            $table->dropIndex(['status']);  // Drop index on status
        });

        // Dropping indexes from the contact table
        Schema::table('contact', function (Blueprint $table) {
            $table->dropIndex(['category']);  // Drop index on category
            $table->dropIndex(['email_address']);  // Drop index on email_address
        });
    }
}
