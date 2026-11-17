<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExecutanteAPR extends Model
{
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