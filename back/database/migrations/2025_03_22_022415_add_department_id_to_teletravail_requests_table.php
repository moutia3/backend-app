<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('teletravail_requests', function (Blueprint $table) {
            // Ajouter la colonne department_id
            $table->unsignedBigInteger('department_id')->after('user_id');

            // Ajouter une clé étrangère pour lier department_id à la table departments
            $table->foreign('department_id')
                  ->references('id')
                  ->on('departments')
                  ->onDelete('cascade'); // Supprimer les demandes si le département est supprimé
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teletravail_requests', function (Blueprint $table) {
            // Supprimer la clé étrangère
            $table->dropForeign(['department_id']);

            // Supprimer la colonne department_id
            $table->dropColumn('department_id');
        });
    }
};