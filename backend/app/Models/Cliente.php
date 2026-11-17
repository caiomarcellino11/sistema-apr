<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        "nome", "endereco", "numero", "complemento", "bairro",
        "cidade", "estado", "tel_residencial", "tel_celular", "email"
    ];

    public function aprs()
    {
        return $this->hasMany(Apr::class);
    }
}
