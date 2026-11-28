<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Log;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\TestHandler;
use RuntimeException;
use Tests\TestCase;

class SanitizarExcecoesTest extends TestCase
{
    /**
     * Configura um handler em memoria para os testes.
     */
    private function configurarHandler(string $canal): TestHandler
    {
        $handler = new TestHandler();

        Log::channel($canal)
            ->getLogger()
            ->setHandlers([$handler]);

        return $handler;
    }

    /**
     * Verifica se os dados sensiveis foram removidos.
     */
    private function verificarSanitizacao(
        TestHandler $handler
    ): void {

        $registros = $handler->getRecords();

        $this->assertCount(1, $registros);

        $registro = $registros[0];

        $this->assertSame(
            'Excecao interna registrada.',
            $registro->message
        );

        $this->assertSame(
            RuntimeException::class,
            $registro->context['tipo']
        );

        $this->assertArrayHasKey(
            'arquivo',
            $registro->context
        );

        $this->assertArrayHasKey(
            'linha',
            $registro->context
        );

        $this->assertArrayNotHasKey(
            'exception',
            $registro->context
        );

        $this->assertArrayNotHasKey(
            'password',
            $registro->context
        );

        $this->assertArrayNotHasKey(
            'token',
            $registro->context
        );

        // Verifica tambem o registro formatado.
        $conteudo = (new LineFormatter())->format(
            $registro
        );

        $this->assertStringNotContainsString(
            'SENHA_FICTICIA_TESTE',
            $conteudo
        );

        $this->assertStringNotContainsString(
            'TOKEN_FICTICIO_TESTE',
            $conteudo
        );

        $this->assertStringNotContainsString(
            'DETALHE_INTERNO_FICTICIO',
            $conteudo
        );
    }

    /**
     * TESTE 01
     * Sanitiza excecoes no canal single.
     */
    public function test_canal_single_sanitiza_excecoes(): void
    {
        $handler = $this->configurarHandler('single');

        Log::channel('single')->error(
            'DETALHE_INTERNO_FICTICIO',
            [
                'exception' => new RuntimeException(
                    'DETALHE_INTERNO_FICTICIO'
                ),
                'password' => 'SENHA_FICTICIA_TESTE',
                'token' => 'TOKEN_FICTICIO_TESTE',
            ]
        );

        $this->verificarSanitizacao($handler);
    }

    /**
     * TESTE 02
     * Sanitiza excecoes no canal daily.
     */
    public function test_canal_daily_sanitiza_excecoes(): void
    {
        $handler = $this->configurarHandler('daily');

        Log::channel('daily')->error(
            'DETALHE_INTERNO_FICTICIO',
            [
                'exception' => new RuntimeException(
                    'DETALHE_INTERNO_FICTICIO'
                ),
                'password' => 'SENHA_FICTICIA_TESTE',
                'token' => 'TOKEN_FICTICIO_TESTE',
            ]
        );

        $this->verificarSanitizacao($handler);
    }

    /**
     * TESTE 03
     * Verifica a sanitizacao no canal stack,
     * utilizando o canal daily.
     */
    public function test_canal_stack_sanitiza_excecoes(): void
    {
        config([
            'logging.default' => 'stack',
            'logging.channels.stack.channels' => [
                'daily',
            ],
        ]);

        Log::forgetChannel('stack');

        $handler = $this->configurarHandler('stack');

        Log::channel('stack')->error(
            'DETALHE_INTERNO_FICTICIO',
            [
                'exception' => new RuntimeException(
                    'DETALHE_INTERNO_FICTICIO'
                ),
                'password' => 'SENHA_FICTICIA_TESTE',
                'token' => 'TOKEN_FICTICIO_TESTE',
            ]
        );

        $this->verificarSanitizacao($handler);
    }

    /**
     * TESTE 04
     * Verifica o tratamento de excecoes
     * reportadas pelo proprio Laravel.
     */
    public function test_excecao_reportada_pelo_laravel_e_sanitizada(): void
    {
        config([
            'logging.default' => 'stack',
            'logging.channels.stack.channels' => [
                'daily',
            ],
        ]);

        Log::forgetChannel('stack');

        $handler = $this->configurarHandler('stack');

        report(
            new RuntimeException(
                'DETALHE_INTERNO_FICTICIO'
            )
        );

        $this->verificarSanitizacao($handler);
    }

    /**
     * TESTE 05
     * Preserva mensagens comuns de logging.
     */
    public function test_preserva_registros_comuns(): void
    {
        $handler = $this->configurarHandler('daily');

        Log::channel('daily')->warning(
            'Aviso ficticio do sistema APR.',
            [
                'operacao' => 'teste',
            ]
        );

        $registros = $handler->getRecords();

        $this->assertCount(1, $registros);

        $this->assertSame(
            'Aviso ficticio do sistema APR.',
            $registros[0]->message
        );

        $this->assertSame(
            'teste',
            $registros[0]->context['operacao']
        );
    }
}