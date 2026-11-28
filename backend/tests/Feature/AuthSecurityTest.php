<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthSecurityTest extends TestCase
{
    use RefreshDatabase;

    // Verifica a configuração do prazo de expiração.
    public function test_tokens_possuem_validade_de_duas_horas(): void
    {
        $this->assertSame(
            120,
            config('sanctum.expiration')
        );
    }

    // Um token válido deve permitir acesso à API.
    public function test_token_valido_permite_acesso(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('teste')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('id', $user->id);
    }

    // Um token com mais de 120 minutos deve ser rejeitado.
    public function test_token_expirado_nao_permite_acesso(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('teste-expiracao');

        $token->accessToken->forceFill([
            'created_at' => now()->subMinutes(121),
        ])->save();

        $this->withToken($token->plainTextToken)
            ->getJson('/api/user')
            ->assertUnauthorized();
    }

    // O logout deve excluir o token utilizado.
    public function test_logout_revoga_token_atual(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('sessao-teste');

        $this->withToken($token->plainTextToken)
            ->postJson('/api/logout')
            ->assertOk();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token->accessToken->id,
        ]);
    }

    // O logout não deve excluir os tokens de outras sessões.
    public function test_logout_preserva_tokens_de_outras_sessoes(): void
    {
        $user = User::factory()->create();

        $tokenAtual = $user->createToken('sessao-atual');

        $outroToken = $user->createToken('outra-sessao');

        $this->withToken($tokenAtual->plainTextToken)
            ->postJson('/api/logout')
            ->assertOk();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $tokenAtual->accessToken->id,
        ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $outroToken->accessToken->id,
        ]);
    }
}