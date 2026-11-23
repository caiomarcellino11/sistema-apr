<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExecutanteApr extends Model
{
    // Informa ao Laravel o nome real da tabela.
    protected $table = 'executante_a_p_r_s';

    protected $fillable = [
        'apr_id',
        'nome',
        'funcao',
        'matricula',
    ];

    public function apr()
    {
        return $this->belongsTo(Apr::class);
    }
}