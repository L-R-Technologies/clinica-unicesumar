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
        Schema::create('samples', function (Blueprint $table) {
            $table->id();

            // Chaves estrangeiras
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Profissional que coletou
            
            // Identificação da amostra
            $table->string('code', 12)->unique();
            $table->string('type', 100);
            
            // Data e local
            $table->date('date');
            $table->string('location', 100);
            
            // Status e notificação
            $table->enum('status', ['under_review', 'stored', 'discarded'])->default('under_review');
            $table->boolean('notified')->default(false);
            
            // Timestamps
            $table->timestamps();
            
            // Índices para otimização
            $table->index(['patient_id', 'date']);
            $table->index('user_id');
            $table->index('code');
            $table->index('status');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('samples');
    }
};
