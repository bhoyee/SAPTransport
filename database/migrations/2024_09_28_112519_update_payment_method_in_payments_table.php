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
        Schema::table('payments', function (Blueprint $table) {
            // Change the `payment_method` column from enum to string
            $table->string('payment_method', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            // Revert the `payment_method` column back to enum if needed
            $table->enum('payment_method', ['credit_card', 'bank_transfer', 'cash'])->change();
        });
    }
};
