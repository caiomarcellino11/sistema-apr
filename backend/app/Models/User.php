<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    // Perfis de acesso
    public const ROLE_ALUNO = 'aluno';
    public const ROLE_ADMIN = 'admin';

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Verifica se o usuário é administrador
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    // Verifica se o usuário é aluno
    public function isAluno(): bool
    {
        return $this->role === self::ROLE_ALUNO;
    }

    // Relacionamento com as APRs
    public function aprs()
    {
        return $this->hasMany(Apr::class);
    }

    // Relacionamento com os clientes
    public function clientes()
    {
        return $this->hasMany(Cliente::class);
    }
}