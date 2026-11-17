<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogoRisco extends Model
{
    protected $fillable = [
        'categoria',
        'descricao_etapa',
        'riscos_envolvidos',
        'epis_medidas_controle',
    ];
}