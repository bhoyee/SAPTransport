<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();  // Primary key
            $table->foreignId('user_id')  // Foreign key to users table
                  ->constrained('users')
                  ->onDelete('cascade');  // Cascade on delete
            $table->string('action', 255);  // Action performed
            $table->text('description')->nullable();  // Description of the action (optional)
            $table->string('ip_address', 50);  // IP address of the user
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
        Schema::dropIfExists('activity_logs');
    }
}
