<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Campanhas de saúde geradas por IA a partir de resultados de exames
     * anonimizados. Guarda o conjunto de dados enviado (já anonimizado), a
     * análise e a campanha estruturadas, para auditoria e consulta posterior.
     * A geração roda em job: análise/campanha ficam nulas até "completed".
     */
    public function up(): void
    {
        Schema::create('health_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->json('exam_type_ids')->nullable();
            $table->unsignedInteger('patients_count');
            $table->unsignedInteger('exams_count');
            $table->longText('dataset');
            $table->json('analysis')->nullable();
            $table->json('campaign')->nullable();
            $table->string('model', 100)->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_campaigns');
    }
};
