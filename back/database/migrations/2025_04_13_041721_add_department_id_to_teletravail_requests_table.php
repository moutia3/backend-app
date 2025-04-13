<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // database/migrations/xxxx_add_department_id_to_teletravail_requests_table.php
public function up()
{
    Schema::table('teletravail_requests', function (Blueprint $table) {
        $table->unsignedBigInteger('department_id')->nullable()->after('status');
        $table->foreign('department_id')->references('id')->on('departments');
    });
}

public function down()
{
    Schema::table('teletravail_requests', function (Blueprint $table) {
        $table->dropForeign(['department_id']);
        $table->dropColumn('department_id');
    });
}
};
