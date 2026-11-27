<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Torna opcionais o registro CREA/CFT do responsável
     * e a matrícula/RA dos executantes.
     */
    public function up(): void
    {
        Schema::table('aprs', function (Blueprint $table) {
            $table->string('responsavel_crea', 50)->nullable()->change();
        });

        Schema::table('executante_a_p_r_s', function (Blueprint $table) {
            $table->string('matricula', 50)->nullable()->change();
        });
    }

    /**
     * Desfaz a alteração (preenche vazios para não quebrar o NOT NULL).
     */
    public function down(): void
    {
        DB::table('aprs')->whereNull('responsavel_crea')->update(['responsavel_crea' => '']);
        DB::table('executante_a_p_r_s')->whereNull('matricula')->update(['matricula' => '']);

        Schema::table('aprs', function (Blueprint $table) {
            $table->string('responsavel_crea', 50)->nullable(false)->change();
        });

        Schema::table('executante_a_p_r_s', function (Blueprint $table) {
            $table->string('matricula', 50)->nullable(false)->change();
        });
    }
};