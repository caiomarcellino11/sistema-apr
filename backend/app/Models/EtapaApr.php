<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EtapaApr extends Model
{
    protected $fillable = [
        'apr_id',
        'numero_etapa',
        'descricao',
        'riscos_envolvidos',
        'medidas_controle',
    ];

    public function apr()
    {
        return $this->belongsTo(Apr::class);
    }
}