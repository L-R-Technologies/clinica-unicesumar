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
        Schema::create('lab_captures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_agent_id')->constrained()->onDelete('cascade');
            $table->foreignId('machine_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('exam_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->longText('raw_text');
            $table->string('raw_hash', 64)->unique();
            $table->json('serial_settings')->nullable();
            $table->dateTime('captured_at');
            $table->dateTime('received_at');
            $table->enum('status', [
                'received', 'analyzing', 'analyzed', 'unrecognized', 'failed', 'linked', 'discarded',
            ])->default('received');
            $table->json('analysis')->nullable();
            $table->string('ai_model', 100)->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'captured_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_captures');
    }
};
