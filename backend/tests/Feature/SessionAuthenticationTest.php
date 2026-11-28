<?php

namespace Tests\Feature;

use Illuminate\Auth\SessionGuard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SessionAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'sanctum.stateful' => ['127.0.0.1:5500'],
        ]);

        // Simula uma requisicao originada pelo Front-End.
        $this->withHeaders([
            'Origin' => 'http://127.0.0.1:5500',
            'Referer' => 'http://127.0.0.1:5500/frontend/',
            'Accept' => 'application/json',
        ]);
    }

    // Verifica a autenticacao por sessao.
    public function test_login_autentica_por_sessao(): void
    {
        $senha = 'Frase segura de teste 2026!';

        $user = User::factory()->create([
            'password' => Hash::make($senha),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => $senha,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('tipo_autenticacao', 'session')
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonMissingPath('token')
            ->assertJsonMissingPath('user.password')
            ->assertCookie(config('session.cookie'));

        $this->assertAuthenticatedAs($user, 'web');

        $this->assertDatabaseCount(
            'personal_access_tokens',
            0
        );
    }

    // Verifica o cadastro com autenticacao automatica.
    public function test_cadastro_autentica_por_sessao(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Aluno Teste',
            'email' => 'sessao@example.com',
            'password' => 'Frase segura de teste 2026!',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('tipo_autenticacao', 'session')
            ->assertJsonPath('user.role', 'aluno')
            ->assertJsonMissingPath('token')
            ->assertJsonMissingPath('user.password');

        $user = User::where(
            'email',
            'sessao@example.com'
        )->firstOrFail();

        $this->assertAuthenticatedAs($user, 'web');

        $this->assertDatabaseCount(
            'personal_access_tokens',
            0
        );
    }

    // Impede autenticacao com senha incorreta.
    public function test_senha_incorreta_nao_cria_sessao(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make(
                'Frase segura de teste 2026!'
            ),
        ]);

        $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'Senha incorreta',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);

        $this->assertGuest('web');

        $this->assertDatabaseCount(
            'personal_access_tokens',
            0
        );
    }

    // Bloqueia consultas sem autenticacao.
    public function test_usuario_sem_sessao_nao_pode_acessar_api(): void
    {
        $this->getJson('/api/user')
            ->assertUnauthorized();
    }

    // Permite consultar o usuario autenticado.
    public function test_usuario_autenticado_pode_consultar_perfil(): void
    {
        $user = User::factory()->create();

       /** @var SessionGuard $guard */
    $guard = Auth::guard('web');

    $chave = $guard->getName();

        $this->withSession([
            $chave => $user->id,
        ])->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('id', $user->id)
            ->assertJsonMissingPath('password');
    }

    // Verifica se o logout remove a autenticacao.
    public function test_logout_encerra_sessao(): void
    {
        $user = User::factory()->create();

        $chave = Auth::guard('web')->getName();

        $this->withSession([
            $chave => $user->id,
        ])->postJson('/api/logout')
            ->assertOk()
            ->assertSessionMissing($chave);

        $this->assertGuest('web');
    }

    // Verifica a criacao dos cookies de seguranca.
    public function test_cookie_de_sessao_possui_httponly(): void
    {
        $response = $this->get('/sanctum/csrf-cookie');

        $response
            ->assertNoContent()
            ->assertCookie(config('session.cookie'))
            ->assertCookie('XSRF-TOKEN');

        $cookies = collect(
            $response->baseResponse->headers->getCookies()
        );

        $sessao = $cookies->first(
            fn ($cookie) =>
                $cookie->getName() === config('session.cookie')
        );

        $csrf = $cookies->first(
            fn ($cookie) =>
                $cookie->getName() === 'XSRF-TOKEN'
        );

        $this->assertNotNull($sessao);
        $this->assertNotNull($csrf);

        $this->assertTrue($sessao->isHttpOnly());
        $this->assertFalse($csrf->isHttpOnly());

        $this->assertSame(
            'lax',
            $sessao->getSameSite()
        );
    }
}