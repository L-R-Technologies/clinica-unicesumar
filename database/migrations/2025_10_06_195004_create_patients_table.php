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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('address_id')->nullable()->constrained()->onDelete('set null');
            $table->date('birthday');
            $table->string('ethnicity', 50);
            $table->enum('sex', ['male', 'female', 'other', 'prefer_not_say']);
            $table->string('cpf', 11);
            $table->string('rg', 20);
            $table->string('phone', 11);
            $table->timestamp('lgpd_consent_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
