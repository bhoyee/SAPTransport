<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterContactTableAddTicketColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contact', function (Blueprint $table) {
            $table->string('ticket_num', 6)->nullable()->unique();   // Add ticket number
            $table->enum('status', ['open', 'closed'])->default('open'); // Add ticket status
            $table->enum('department', ['support', 'sales', 'billing'])->nullable(); // Add department
            $table->enum('priority', ['low', 'medium', 'high'])->default('low'); // Add priority
            $table->string('attachment')->nullable(); // Add attachment for uploaded files
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contact', function (Blueprint $table) {
            // Drop the newly added columns in reverse
            $table->dropColumn('ticket_num');
            $table->dropColumn('status');
            $table->dropColumn('department');
            $table->dropColumn('priority');
            $table->dropColumn('attachment');
        });
    }
}
