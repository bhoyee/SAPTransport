<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSecurityCoverageToBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Adding new columns for security coverage
            $table->enum('security_coverage', ['yes', 'no'])->default('no'); // For security coverage (Yes/No)
            $table->integer('mobile_police_count')->nullable(); // For number of mobile police (nullable)
            $table->enum('with_van', ['yes', 'no'])->default('no'); // For "With Van" or "Without Van"
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Dropping the columns when rolling back the migration
            $table->dropColumn(['security_coverage', 'mobile_police_count', 'with_van']);
        });
    }
}
