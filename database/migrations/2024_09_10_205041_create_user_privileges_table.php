<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPrivilegesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_privileges', function (Blueprint $table) {
            $table->foreignId('user_id')  // Foreign key to users table
                  ->constrained('users')
                  ->onDelete('cascade');  // Cascade on delete
            $table->foreignId('privilege_id')  // Foreign key to privileges table
                  ->constrained('privileges')
                  ->onDelete('cascade');  // Cascade on delete
            $table->primary(['user_id', 'privilege_id']);  // Composite primary key
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
        Schema::dropIfExists('user_privileges');
    }
}
