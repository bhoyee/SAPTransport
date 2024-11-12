<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDriverAndVehicleToBookingsTable extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('driver_name')->nullable()->after('status');  // Adjust placement as needed
            $table->string('vehicle_details')->nullable()->after('driver_name');
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn('driver_name');
            $table->dropColumn('vehicle_details');
        });
    }
}
