<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_history_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('sample_id')->constrained()->onDelete('cascade');
            $table->string('type', 100);
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
