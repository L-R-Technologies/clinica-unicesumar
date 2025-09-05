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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();

            // Chaves estrangeiras
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Profissional responsável
            $table->foreignId('patient_history_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('sample_id')->constrained()->onDelete('cascade');
            
            // Tipo e data do exame
            $table->string('type', 100);
            $table->timestamp('date');
            
            // Resultados em JSON (flexível para diferentes tipos de exame)
            $table->json('results')->nullable();
            
            // Status do exame
            $table->enum('status', ['pending', 'pending_approval', 'rejected', 'approved'])->default('pending');
            
            // Observações e justificativas
            $table->text('observation')->nullable();
            $table->text('justification_rejection')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Índices para otimização
            $table->index(['patient_id', 'date']);
            $table->index('user_id');
            $table->index('sample_id');
            $table->index('patient_history_id');
            $table->index('status');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
