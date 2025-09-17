<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            // Esta linha diz ao banco de dados para permitir que a coluna 'location' seja nula (vazia).
            $table->string('location')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('samples', function (Blueprint $table) {
            // Esta linha reverte a alteração, fazendo 'location' ser obrigatória novamente.
            $table->string('location')->nullable(false)->change();
        });
    }
};
