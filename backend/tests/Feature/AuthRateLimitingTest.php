<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRateLimitingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Dados ficticios para os testes de login.
     */
    private function dadosLogin(
        string $email = 'teste@example.com'
    ): array {
        return [
            'email' => $email,
            'password' => 'Senha incorreta de teste',
        ];
    }

    /**
     * Bloqueia o login apos cinco requisicoes
     * utilizando o mesmo e-mail e IP.
     */
    public function test_login_bloqueado_apos_cinco_tentativas(): void
    {
        for ($i = 0; $i < 5; $i++) {

            $this->postJson(
                '/api/login',
                $this->dadosLogin()
            )->assertUnprocessable();

        }

        $this->postJson(
            '/api/login',
            $this->dadosLogin()
        )
            ->assertStatus(429)
            ->assertHeader('Retry-After')
            ->assertJsonPath(
                'message',
                'Limite de tentativas de login atingido. Aguarde antes de tentar novamente.'
            );
    }

    /**
     * Verifica o limite geral de login por IP.
     */
    public function test_limite_geral_de_login_por_ip(): void
    {
        for ($i = 0; $i < 30; $i++) {

            $this->postJson(
                '/api/login',
                $this->dadosLogin("teste{$i}@example.com")
            )->assertUnprocessable();

        }

        $this->postJson(
            '/api/login',
            $this->dadosLogin('outro@example.com')
        )
            ->assertStatus(429)
            ->assertHeader('Retry-After')
            ->assertJsonPath(
                'message',
                'Muitas tentativas de login. Aguarde antes de tentar novamente.'
            );
    }

    /**
     * Verifica se o limite considera o e-mail
     * independentemente de maiusculas e minusculas.
     */
    public function test_limitador_normaliza_email(): void
    {
        for ($i = 0; $i < 5; $i++) {

            $this->postJson(
                '/api/login',
                $this->dadosLogin('teste@example.com')
            )->assertUnprocessable();

        }

        $this->postJson(
            '/api/login',
            $this->dadosLogin('TESTE@EXAMPLE.COM')
        )->assertStatus(429);
    }

    /**
     * Bloqueia requisicoes excessivas de cadastro.
     */
    public function test_cadastro_bloqueado_apos_cinco_requisicoes(): void
    {
        for ($i = 0; $i < 5; $i++) {

            $this->postJson('/api/register', [])
                ->assertUnprocessable();

        }

        $this->postJson('/api/register', [])
            ->assertStatus(429)
            ->assertHeader('Retry-After')
            ->assertJsonPath(
                'message',
                'Muitas tentativas de cadastro. Aguarde antes de tentar novamente.'
            );

        $this->assertDatabaseCount('users', 0);
    }

    /**
     * Verifica se o bloqueio termina
     * apos o periodo configurado.
     */
    public function test_login_liberado_apos_periodo_de_bloqueio(): void
    {
        for ($i = 0; $i < 5; $i++) {

            $this->postJson(
                '/api/login',
                $this->dadosLogin()
            )->assertUnprocessable();

        }

        $this->postJson(
            '/api/login',
            $this->dadosLogin()
        )->assertStatus(429);

        // Avanca o relogio virtual do ambiente de testes.
        $this->travel(61)->seconds();

        $this->postJson(
            '/api/login',
            $this->dadosLogin()
        )->assertUnprocessable();

        $this->travelBack();
    }
}