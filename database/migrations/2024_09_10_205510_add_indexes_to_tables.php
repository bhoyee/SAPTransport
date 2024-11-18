<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddIndexesToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            // Check if the index `messages_sender_id_index` exists
            if (!DB::select("SHOW INDEX FROM `messages` WHERE Key_name = 'messages_sender_id_index'")) {
                $table->index('sender_id', 'messages_sender_id_index');
            }
    
            // Similarly for other indexes
            if (!DB::select("SHOW INDEX FROM `messages` WHERE Key_name = 'messages_receiver_id_index'")) {
                $table->index('receiver_id', 'messages_receiver_id_index');
            }
    
            if (!DB::select("SHOW INDEX FROM `messages` WHERE Key_name = 'messages_status_index'")) {
                $table->index('status', 'messages_status_index');
            }
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
