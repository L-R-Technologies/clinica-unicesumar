<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('patient_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->datetime('date');
            $table->boolean('fasting')->default(false);
            $table->integer('fasting_hours')->nullable();
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
            $table->boolean('pregnant_or_lactating')->default(false);
            $table->boolean('smokes')->default(false);
            $table->integer('cigarettes_per_day')->nullable();
            $table->boolean('physically_active')->default(false);
            $table->enum('menstrual_period', ['yes', 'no', 'n/a'])->default('n/a');
            $table->boolean('recent_fever_or_flu')->default(false);
            $table->text('observation')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_histories');
    }
};
