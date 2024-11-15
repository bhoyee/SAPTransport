<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            // Drop the foreign key constraint before dropping the column
            $table->dropForeign(['receiver_id']);
            $table->dropColumn('receiver_id');
        });
    }

    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            // Re-add the receiver_id column and foreign key constraint
            $table->foreignId('receiver_id')->nullable()->constrained('users')->onDelete('cascade');
        });
    }
};
