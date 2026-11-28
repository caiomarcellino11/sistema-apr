<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordPolicyTest extends TestCase
{
    use RefreshDatabase;

    private function dadosCadastro(string $senha): array
    {
        return [
            'name' => 'Aluno Teste',
            'email' => 'aluno@example.com',
            'password' => $senha,
        ];
    }

    // Rejeita senhas menores que 15 caracteres.
    public function test_senha_curta_e_rejeitada(): void
    {
        $this->postJson(
            '/api/register',
            $this->dadosCadastro(str_repeat('a', 14))
        )
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);

        $this->assertDatabaseCount('users', 0);
    }

    // Rejeita senhas maiores que 64 caracteres.
    public function test_senha_longa_demais_e_rejeitada(): void
    {
        $this->postJson(
            '/api/register',
            $this->dadosCadastro(str_repeat('a', 65))
        )
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);

        $this->assertDatabaseCount('users', 0);
    }

    // Verifica o limite de 72 bytes do bcrypt.
    public function test_senha_acima_do_limite_do_bcrypt_e_rejeitada(): void
    {
        config(['hashing.driver' => 'bcrypt']);

        $senha = str_repeat("\u{00E1}", 40);

        $this->postJson(
            '/api/register',
            $this->dadosCadastro($senha)
        )
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['password']);

        $this->assertDatabaseCount('users', 0);
    }

    // Aceita uma senha valida e verifica o hash.
    public function test_senha_valida_e_armazenada_com_hash(): void
    {
        $senha = 'Frase teste 123';

        $this->postJson(
            '/api/register',
            $this->dadosCadastro($senha)
        )
            ->assertCreated()
            ->assertJsonMissingPath('user.password');

        $user = User::where(
            'email',
            'aluno@example.com'
        )->firstOrFail();

        // A senha nao pode estar armazenada em texto puro.
        $this->assertNotSame(
            $senha,
            $user->password
        );

        // Confirma que o hash corresponde a senha original.
        $this->assertTrue(
            Hash::check($senha, $user->password)
        );
    }

    // Aceita senhas com exatamente 64 caracteres.
    public function test_senha_com_64_caracteres_e_aceita(): void
    {
        $senha = str_repeat('a', 64);

        $this->postJson(
            '/api/register',
            $this->dadosCadastro($senha)
        )->assertCreated();

        $this->assertDatabaseHas('users', [
            'email' => 'aluno@example.com',
        ]);
    }

    // Mantem o login de usuarios cadastrados anteriormente.
    public function test_usuario_antigo_pode_continuar_acessando(): void
    {
        $senhaAntiga = 'Senha123';

        $user = User::factory()->create([
            'password' => Hash::make($senhaAntiga),
        ]);

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => $senhaAntiga,
        ])
            ->assertOk()
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonStructure(['user', 'token'])
            ->assertJsonMissingPath('user.password');
    }
}