<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apr extends Model
{
    protected $fillable = [
        'user_id', 'cliente_id', 'local', 'atividade',
        'responsavel_nome', 'responsavel_crea', 'data_emissao'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function etapas()
    {
        return $this->hasMany(EtapaApr::class);
    }

    public function executantes()
    {
        return $this->hasMany(ExecutanteApr::class);
    }
}