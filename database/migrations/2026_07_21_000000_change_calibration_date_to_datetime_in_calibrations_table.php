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
        if (Schema::hasColumn('calibrations', 'calibration_date')) {
            Schema::table('calibrations', function (Blueprint $table) {
                $table->dropColumn('calibration_date');
            });
        }

        Schema::table('calibrations', function (Blueprint $table) {
            $table->dateTime('calibration_date')->nullable()->after('machine_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('calibrations', 'calibration_date')) {
            Schema::table('calibrations', function (Blueprint $table) {
                $table->dropColumn('calibration_date');
            });
        }

        Schema::table('calibrations', function (Blueprint $table) {
            $table->date('calibration_date')->nullable()->after('machine_id');
        });
    }
};
