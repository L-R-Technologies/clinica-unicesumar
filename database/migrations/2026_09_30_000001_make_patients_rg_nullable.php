<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * A nova Carteira de Identidade Nacional não traz o número do RG,
     * então o campo passa a ser opcional no cadastro do paciente.
     */
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->string('rg', 20)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->string('rg', 20)->nullable(false)->change();
        });
    }
};
