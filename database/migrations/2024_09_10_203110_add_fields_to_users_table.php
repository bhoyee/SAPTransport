<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 15)->nullable()->after('password');  // Adding 'phone' field
            $table->enum('role', ['admin', 'consultant', 'passenger'])->default('passenger')->after('phone');  // Adding 'role' field
            $table->enum('status', ['inactive', 'active'])->default('inactive')->after('role');  // Adding 'status' field
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'role', 'status']);  // Dropping columns in case of rollback
        });
    }
}
