<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * ERS: calibrations.calibration_date é DATE NOT NULL.
     * O código havia migrado para DATETIME nullable — revertendo para alinhar à ERS.
     */
    public function up(): void
    {
        // Backfill de eventuais registros nulos antes de tornar NOT NULL.
        DB::table('calibrations')->whereNull('calibration_date')->update([
            'calibration_date' => DB::raw('DATE(created_at)'),
        ]);

        Schema::table('calibrations', function (Blueprint $table) {
            $table->date('calibration_date')->change();
        });
    }

    public function down(): void
    {
        Schema::table('calibrations', function (Blueprint $table) {
            $table->dateTime('calibration_date')->nullable()->change();
        });
    }
};
