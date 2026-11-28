<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\SessionGuard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Session\SessionManager;
use Illuminate\Session\Store;
use Illuminate\Support\Str;
use Tests\TestCase;

class SessionExpirationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'session.driver' => 'database',
            'session.connection' => 'sqlite',
            'session.lifetime' => 120,
            'sanctum.stateful' => ['127.0.0.1:5500'],
        ]);

        $this->withHeaders([
            'Origin' => 'http://127.0.0.1:5500',
            'Referer' => 'http://127.0.0.1:5500/frontend/',
            'Accept' => 'application/json',
        ]);
    }

    /**
     * Cria uma sessao utilizando o gerenciador
     * oficial do Laravel.
     */
    private function criarSessao(
        User $user,
        int $minutosInatividade
    ): string {

        $sessionId = Str::random(40);

        /** @var SessionGuard $guard */
        $guard = Auth::guard('web');

        $chave = $guard->getName();

        // Utiliza uma instancia independente
        // do gerenciador de sessoes.
        $manager = new SessionManager($this->app);

        /** @var Store $session */
        $session = $manager->driver('database');

        $session->setId($sessionId);

        $session->start();

        // Armazena o usuario autenticado.
        $session->put($chave, $user->id);

        // O Laravel realiza a serializacao correta.
        $session->save();

        // Simula o tempo de inatividade.
        $atualizados = DB::connection('sqlite')
            ->table('sessions')
            ->where('id', $sessionId)
            ->update([
                'user_id' => $user->id,
                'last_activity' => now()
                    ->subMinutes($minutosInatividade)
                    ->timestamp,
            ]);

        $this->assertSame(1, $atualizados);

        return $sessionId;
    }

    // Uma sessao com 119 minutos continua valida.
    public function test_sessao_dentro_do_prazo_permite_acesso(): void
    {
        $user = User::factory()->create();

        $sessionId = $this->criarSessao($user, 119);

        $this->withCredentials()
            ->withCookie(
                config('session.cookie'),
                $sessionId
            )
            ->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('id', $user->id);
    }

    // Uma sessao com 121 minutos deve ser rejeitada.
    public function test_sessao_expirada_nao_permite_acesso(): void
    {
        $user = User::factory()->create();

        $sessionId = $this->criarSessao($user, 121);

        $this->withCredentials()
            ->withCookie(
                config('session.cookie'),
                $sessionId
            )
            ->getJson('/api/user')
            ->assertUnauthorized();
    }
}