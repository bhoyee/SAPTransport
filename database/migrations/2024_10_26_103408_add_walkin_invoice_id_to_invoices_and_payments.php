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
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('walkin_invoice_id')->nullable()->index()->after('id');
        });
    
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('walkin_invoice_id')->nullable()->index()->after('id');
        });
    }
    
    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('walkin_invoice_id');
        });
    
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('walkin_invoice_id');
        });
    }
    
};
