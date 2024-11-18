<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterContactTableAddTicketColumns extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('contact', function (Blueprint $table) {
            if (!Schema::hasColumn('contact', 'ticket_num')) {
                $table->string('ticket_num', 6)->nullable()->unique(); // Add ticket number
            }

            if (!Schema::hasColumn('contact', 'status')) {
                $table->enum('status', ['open', 'closed'])->default('open'); // Add ticket status
            }

            if (!Schema::hasColumn('contact', 'department')) {
                $table->enum('department', ['support', 'sales', 'billing'])->nullable(); // Add department
            }

            if (!Schema::hasColumn('contact', 'priority')) {
                $table->enum('priority', ['low', 'medium', 'high'])->default('low'); // Add priority
            }

            if (!Schema::hasColumn('contact', 'attachment')) {
                $table->string('attachment')->nullable(); // Add attachment for uploaded files
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('contact', function (Blueprint $table) {
            if (Schema::hasColumn('contact', 'ticket_num')) {
                $table->dropColumn('ticket_num');
            }

            if (Schema::hasColumn('contact', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('contact', 'department')) {
                $table->dropColumn('department');
            }

            if (Schema::hasColumn('contact', 'priority')) {
                $table->dropColumn('priority');
            }

            if (Schema::hasColumn('contact', 'attachment')) {
                $table->dropColumn('attachment');
            }
        });
    }
}
