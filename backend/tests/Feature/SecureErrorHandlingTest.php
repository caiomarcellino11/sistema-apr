<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Sanctum;
use RuntimeException;
use Tests\TestCase;

class SecureErrorHandlingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Registra uma rota temporaria para simular
     * uma falha interna durante os testes.
     */
    private function registrarRotaComErro(): void
    {
        Route::get('/api/teste-erro-interno', function () {

            throw new RuntimeException(
                'Informacao interna ficticia de teste.'
            );

        });
    }

    /**
     * TESTE 01
     * Protege informacoes internas com debug ativo.
     */
    public function test_erro_interno_protegido_com_debug_ativo(): void
    {
        config(['app.debug' => true]);

        $this->registrarRotaComErro();

        $this->getJson('/api/teste-erro-interno')
            ->assertStatus(500)
            ->assertExactJson([
                'message' => 'Ocorreu um erro interno no servidor.',
            ])
            ->assertDontSee(
                'Informacao interna ficticia de teste.'
            );
    }

    /**
     * TESTE 02
     * Protege informacoes internas com debug desativado.
     */
    public function test_erro_interno_protegido_com_debug_desativado(): void
    {
        config(['app.debug' => false]);

        $this->registrarRotaComErro();

        $this->getJson('/api/teste-erro-interno')
            ->assertStatus(500)
            ->assertExactJson([
                'message' => 'Ocorreu um erro interno no servidor.',
            ]);
    }

    /**
     * TESTE 03
     * Preserva o erro de autenticacao.
     */
    public function test_usuario_nao_autenticado_recebe_401(): void
    {
        config(['app.debug' => true]);

        $this->getJson('/api/user')
            ->assertUnauthorized()
            ->assertExactJson([
                'message' => 'Usuario nao autenticado.',
            ]);
    }

    /**
     * TESTE 04
     * Preserva a protecao administrativa.
     */
    public function test_usuario_sem_permissao_recebe_403(): void
    {
        config(['app.debug' => true]);

        $user = User::factory()->create();

        $user->role = User::ROLE_ALUNO;
        $user->save();

        Sanctum::actingAs($user);

        $this->postJson('/api/catalogo-riscos', [])
            ->assertForbidden()
            ->assertJsonMissingPath('trace')
            ->assertJsonMissingPath('exception')
            ->assertJsonMissingPath('file');
    }

    /**
     * TESTE 05
     * Retorna JSON seguro para rotas inexistentes,
     * mesmo sem o cabecalho Accept: application/json.
     */
    public function test_rota_inexistente_retorna_json_seguro(): void
    {
        config(['app.debug' => true]);

        $this->get('/api/recurso-inexistente-teste')
            ->assertNotFound()
            ->assertExactJson([
                'message' => 'Recurso nao encontrado.',
            ]);
    }

    /**
     * TESTE 06
     * Preserva o codigo 405 e o cabecalho Allow.
     */
    public function test_metodo_invalido_preserva_cabecalho_allow(): void
    {
        config(['app.debug' => true]);

        $resposta = $this->getJson('/api/login')
            ->assertStatus(405)
            ->assertHeader('Allow')
            ->assertExactJson([
                'message' => 'Metodo HTTP nao permitido.',
            ]);

        $this->assertStringContainsString(
            'POST',
            $resposta->headers->get('Allow', '')
        );
    }

    /**
     * TESTE 07
     * Protege os detalhes de uma excecao de CSRF.
     */
    public function test_erro_csrf_retorna_mensagem_segura(): void
    {
        config(['app.debug' => true]);

        Route::post('/api/teste-erro-csrf', function () {

            throw new TokenMismatchException();

        });

        $this->postJson('/api/teste-erro-csrf')
            ->assertStatus(419)
            ->assertExactJson([
                'message' =>
                    'Sessao expirada ou token CSRF invalido. Tente novamente.',
            ]);
    }

    /**
     * TESTE 08
     * Preserva os detalhes dos erros de validacao.
     */
    public function test_erros_de_validacao_sao_preservados(): void
    {
        config(['app.debug' => true]);

        $this->postJson('/api/register', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
                'email',
                'password',
            ])
            ->assertJsonMissingPath('trace')
            ->assertJsonMissingPath('exception')
            ->assertJsonMissingPath('file');
    }

    /**
     * TESTE 09
     * Preserva a resposta personalizada do Rate Limiting.
     */
    public function test_rate_limiting_preserva_mensagem_e_cabecalho(): void
    {
        config(['app.debug' => true]);

        for ($i = 0; $i < 5; $i++) {

            $this->postJson('/api/login', [])
                ->assertUnprocessable();

        }

        $this->postJson('/api/login', [])
            ->assertStatus(429)
            ->assertHeader('Retry-After')
            ->assertJsonPath(
                'message',
                'Limite de tentativas de login atingido. Aguarde antes de tentar novamente.'
            )
            ->assertJsonMissingPath('trace');
    }

    /**
     * TESTE 10
     * Protege detalhes de indisponibilidade
     * e preserva o cabecalho Retry-After.
     */
    public function test_servico_indisponivel_retorna_503_seguro(): void
    {
        config(['app.debug' => true]);

        Route::get('/api/teste-indisponibilidade', function () {

            abort(
                503,
                'Detalhe interno ficticio.',
                ['Retry-After' => '60']
            );

        });

        $this->getJson('/api/teste-indisponibilidade')
            ->assertStatus(503)
            ->assertHeader('Retry-After', '60')
            ->assertExactJson([
                'message' =>
                    'Servico temporariamente indisponivel.',
            ]);
    }
}