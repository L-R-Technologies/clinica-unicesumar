<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * O status 'calibrating' foi substituído por 'maintenance' (Em manutenção).
     * A coluna vira string temporariamente para que a atualização dos dados não
     * viole o CHECK do enum durante a troca (relevante em SQLite/MySQL).
     */
    public function up(): void
    {
        Schema::table('machines', function (Blueprint $table) {
            $table->string('status')->default('active')->change();
        });

        DB::table('machines')
            ->where('status', 'calibrating')
            ->update(['status' => 'maintenance']);

        Schema::table('machines', function (Blueprint $table) {
            $table->enum('status', ['active', 'maintenance', 'inactive'])
                ->default('active')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('machines', function (Blueprint $table) {
            $table->string('status')->default('active')->change();
        });

        DB::table('machines')
            ->where('status', 'maintenance')
            ->update(['status' => 'calibrating']);

        Schema::table('machines', function (Blueprint $table) {
            $table->enum('status', ['active', 'calibrating', 'inactive'])
                ->default('active')
                ->change();
        });
    }
};
