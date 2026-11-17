<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('etapa_aprs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apr_id')->constrained('aprs')->onDelete('cascade');
            $table->integer('numero_etapa');
            $table->text('descricao');
            $table->text('riscos_envolvidos');
            $table->text('medidas_controle');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etapa_aprs');
    }
};
