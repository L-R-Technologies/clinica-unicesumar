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
        Schema::create('patient_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            // Chaves estrangeiras
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Profissional que preencheu
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            
            // Data do histórico
            $table->datetime('date');
            
            // Informações de jejum
            $table->boolean('fasting')->default(false);
            $table->integer('fasting_hours')->nullable();
            
            // Histórico de saúde
            $table->boolean('alcohol_last_24h')->default(false);
            $table->boolean('on_medication')->default(false);
            $table->text('medications')->nullable();
            $table->boolean('on_supplements')->default(false);
            $table->text('supplements')->nullable();
            $table->boolean('chronic_disease')->default(false);
            $table->text('chronic_disease_details')->nullable();
            $table->boolean('recent_surgery')->default(false);
            $table->text('surgery_details')->nullable();
            $table->boolean('allergies')->default(false);
            $table->text('allergy_details')->nullable();
            
            // Condições específicas
            $table->boolean('pregnant_or_lactating')->default(false);
            $table->boolean('smokes')->default(false);
            $table->integer('cigarettes_per_day')->nullable();
            $table->boolean('physically_active')->default(false);
            
            // Saúde menstrual (para pacientes do sexo feminino)
            $table->enum('menstrual_period', ['yes', 'no', 'n/a'])->default('n/a');
            
            // Saúde recente
            $table->boolean('recent_fever_or_flu')->default(false);
            
            // Observações gerais
            $table->text('observation')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Índices
            $table->index(['patient_id', 'date']);
            $table->index('user_id');
            $table->index('date');            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_histories');
    }
};
