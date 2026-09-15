<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Valores de referência (faixa esperada para pessoa saudável) de um campo
     * numérico do tipo de exame, opcionalmente restritos por sexo e/ou faixa etária.
     * Sexo/idades nulos significam "qualquer".
     */
    public function up(): void
    {
        Schema::create('exam_type_field_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_type_field_id')->constrained()->onDelete('cascade');
            $table->enum('sex', ['male', 'female'])->nullable();
            $table->unsignedSmallInteger('age_min')->nullable();
            $table->unsignedSmallInteger('age_max')->nullable();
            $table->decimal('min_value', 12, 4)->nullable();
            $table->decimal('max_value', 12, 4)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_type_field_references');
    }
};
