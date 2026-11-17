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
        Schema::create('catalogo_riscos', function (Blueprint $table) {
            $table->id();
            $table->string('categoria');             // Ex: Elétrica, Altura, Solda, Mecânica
            $table->string('descricao_etapa');       // Texto pronto para a coluna Descrição
            $table->text('riscos_envolvidos');       // Texto pronto para Riscos Envolvidos
            $table->text('epis_medidas_controle');   // EPIs obrigatórios + Medidas de Controle
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalogo_riscos');
    }
};
