<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Caminho (no disco privado) da foto/scan do termo LGPD assinado pelo paciente.
     */
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->string('lgpd_term_path')->nullable()->after('lgpd_consent_at');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('lgpd_term_path');
        });
    }
};
