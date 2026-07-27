<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Adiciona soft deletes. Mudança aditiva: as foreign keys existentes
     * mantêm onDelete('cascade'), que só é acionado em exclusão física.
     * Com soft delete, os registros relacionados são preservados.
     */
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('samples', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('patient_histories', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('samples', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('patient_histories', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
