<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            // Adiciona a nova coluna após a coluna 'id'
            // Usamos nullable() para evitar erros caso a tabela já tenha dados
            $table->string('unique_code')->unique()->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            // Remove a coluna se a migration for revertida
            $table->dropColumn('unique_code');
        });
    }
};
