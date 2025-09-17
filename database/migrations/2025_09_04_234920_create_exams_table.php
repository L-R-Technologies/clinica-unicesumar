<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_history_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('sample_id')->constrained()->onDelete('cascade');
            $table->enum('type', [
                'complete_blood_count',
                'glucose',
                'glycated_hemoglobin',
                'creatinine',
                'alt_sgpt',
                'ast_sgot',
                'alkaline_phosphatase',
                'gamma_gt',
                'albumin',
                'total_cholesterol',
                'triglycerides',
                'hcg',
                'blood_typing',
                'vdrl',
                'syphilis_rapid_test',
                'urinalysis',
                'gram_stain',
                'chromogenic_growth',
                'stool_parasitology',
                'generic_test',
            ]);
            $table->timestamp('date');
            $table->json('results')->nullable();
            $table->enum('status', ['pending', 'pending_approval', 'rejected', 'approved'])->default('pending');
            $table->text('observation')->nullable();
            $table->text('justification_rejection')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
