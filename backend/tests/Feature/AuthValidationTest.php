<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Dados ficticios para testes de cadastro.
     */
    private function dadosCadastro(): array
    {
        return [
            'name' => 'Usuario de Teste',
            'email' => 'validacao@example.com',
            'password' => 'Minha senha segura 2026!',
        ];
    }

    /**
     * Verifica se a senha e armazenada corretamente.
     */
    public function test_senha_e_armazenada_com_hash_correto(): void
    {
        $dados = $this->dadosCadastro();

        $this->postJson('/api/register', $dados)
            ->assertCreated();

        $user = User::where('email', $dados['email'])
            ->firstOrFail();

        $this->assertNotSame(
            $dados['password'],
            $user->password
        );

        $this->assertTrue(
            Hash::check(
                $dados['password'],
                $user->password
            )
        );
    }

    /**
     * Garante que uma senha no formato de hash
     * seja tratada como uma senha recebida.
     */
    public function test_senha_no_formato_de_hash_e_processada(): void
    {
        $dados = $this->dadosCadastro();

        $dados['password'] = Hash::make(
            'Senha ficticia original'
        );

        $this->postJson('/api/register', $dados)
            ->assertCreated();

        $user = User::where('email', $dados['email'])
            ->firstOrFail();

        $this->assertNotSame(
            $dados['password'],
            $user->password
        );

        $this->assertTrue(
            Hash::check(
                $dados['password'],
                $user->password
            )
        );
    }

    /**
     * Impede caracteres nulos no cadastro.
     */
    public function test_cadastro_rejeita_senha_com_caractere_nulo(): void
    {
        $dados = $this->dadosCadastro();

        $dados['password'] = "Senha segura\0de teste 2026";

        $this->postJson('/api/register', $dados)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('password');

        $this->assertDatabaseMissing('users', [
            'email' => $dados['email'],
        ]);
    }

    /**
     * Impede caracteres nulos no login.
     */
    public function test_login_rejeita_senha_com_caractere_nulo(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => "Senha\0invalida",
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('password');

        $this->assertDatabaseCount(
            'personal_access_tokens',
            0
        );
    }

    /**
     * Impede entradas excessivamente grandes no login.
     */
    public function test_login_rejeita_senha_excessivamente_grande(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => str_repeat('a', 1025),
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('password');

        $this->assertDatabaseCount(
            'personal_access_tokens',
            0
        );
    }

    /**
     * Impede a exposicao de dados sensiveis no cadastro.
     */
    public function test_cadastro_nao_expoe_dados_sensiveis(): void
    {
        $this->postJson(
            '/api/register',
            $this->dadosCadastro()
        )
            ->assertCreated()
            ->assertJsonPath('user.role', User::ROLE_ALUNO)
            ->assertJsonMissingPath('user.password')
            ->assertJsonMissingPath('user.remember_token');
    }

    /**
     * Impede a exposicao de dados sensiveis no login.
     */
    public function test_login_nao_expoe_dados_sensiveis(): void
    {
        $senha = 'Minha senha segura 2026!';

        $user = User::factory()->create([
            'password' => Hash::make($senha),
        ]);

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => $senha,
        ])
            ->assertOk()
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonMissingPath('user.password')
            ->assertJsonMissingPath('user.remember_token');
    }
}