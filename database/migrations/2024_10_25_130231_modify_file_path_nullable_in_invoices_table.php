<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyFilePathNullableInInvoicesTable extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('file_path')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('file_path')->nullable(false)->change();
        });
    }
}
