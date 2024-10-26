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
        Schema::create('walkin_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->date('issue_date');
            $table->date('due_date');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['Paid', 'Unpaid', 'Refunded']);
            $table->enum('service_type', ['Airport Transfer', 'Charter']);
            $table->string('invoice_number')->unique();
            $table->date('pickup_date');
            $table->time('pickup_time');
            $table->string('pickup_address');
            $table->string('dropoff_address');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('walkin_invoices');
    }
};
