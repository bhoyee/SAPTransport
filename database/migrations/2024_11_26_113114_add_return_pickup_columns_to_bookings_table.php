<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->date('return_pickup_date')->nullable();  // Adding return_pickup_date
            $table->time('return_pickup_time')->nullable();  // Adding return_pickup_time
        });
    }
    
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('return_pickup_date');
            $table->dropColumn('return_pickup_time');
        });
    }
    
};
