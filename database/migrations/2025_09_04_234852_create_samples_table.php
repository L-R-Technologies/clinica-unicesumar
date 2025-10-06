<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('samples', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('code', 12)->unique();
            $table->enum('type', [
                'whole_blood',
                'serum',
                'plasma',
                'urine',
                'stool',
                'vaginal_swab',
                'urethral_swab',
                'throat_swab',
                'sputum',
                'csf',
                'secretion',
            ]);
            $table->date('date');
            $table->string('location', 100);
            $table->enum('status', ['under_review', 'stored', 'discarded'])->default('under_review');
            $table->boolean('notified')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('samples');
    }
};
