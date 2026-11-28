<?php

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))

    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware): void {

        // Habilita a autenticacao por sessao via Sanctum.
        $middleware->statefulApi();

        // Registra o middleware de controle de acesso.
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);

    })

    ->withExceptions(function (Exceptions $exceptions): void {

        // Padroniza as respostas de erro da API em JSON.
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) =>
                $request->is('api', 'api/*')
                || $request->is('sanctum/csrf-cookie')
                || $request->expectsJson()
        );

        // Personaliza as respostas geradas por excecoes.
        $exceptions->respond(function (
            Response $response,
            \Throwable $exception,
            Request $request
        ): Response {

            // Aplica o tratamento somente a API
            // e requisicoes que esperam JSON.
            $requisicaoJson =
                $request->is('api', 'api/*')
                || $request->is('sanctum/csrf-cookie')
                || $request->expectsJson();

            if (!$requisicaoJson) {
                return $response;
            }

            $status = $response->getStatusCode();

            // Preserva respostas sem erros.
            if ($status < 400) {
                return $response;
            }

            // Preserva os erros de validacao dos formularios.
            if (
                $status === 422
                && $exception instanceof ValidationException
            ) {
                return $response;
            }

            // Preserva as respostas personalizadas
            // dos limitadores de login e cadastro.
            if (
                $status === 429
                && $exception instanceof HttpResponseException
            ) {
                return $response;
            }

            // Define mensagens seguras para cada codigo HTTP.
            $mensagem = match ($status) {

                400 => 'Requisicao invalida.',

                401 => 'Usuario nao autenticado.',

                403 => 'Voce nao possui permissao para realizar esta operacao.',

                404 => 'Recurso nao encontrado.',

                405 => 'Metodo HTTP nao permitido.',

                409 => 'A operacao gerou um conflito de dados.',

                413 => 'O tamanho da requisicao excede o limite permitido.',

                415 => 'Formato de conteudo nao suportado.',

                419 => 'Sessao expirada ou token CSRF invalido. Tente novamente.',

                422 => 'Os dados informados sao invalidos.',

                429 => 'Muitas requisicoes. Aguarde antes de tentar novamente.',

                500 => 'Ocorreu um erro interno no servidor.',

                502 => 'Falha na comunicacao com o servidor.',

                503 => 'Servico temporariamente indisponivel.',

                504 => 'O servidor excedeu o tempo de resposta.',

                default => $status >= 500
                    ? 'Ocorreu um erro interno no servidor.'
                    : 'Nao foi possivel processar a requisicao.',

            };

            // Cria uma resposta sem detalhes internos.
            $respostaSegura = response()->json([
                'message' => $mensagem,
            ], $status);

            // Preserva os cabecalhos HTTP relevantes.
            $cabecalhosPermitidos = [
                'Allow',
                'Retry-After',
                'WWW-Authenticate',
                'X-RateLimit-Limit',
                'X-RateLimit-Remaining',
                'X-RateLimit-Reset',
                'Access-Control-Allow-Origin',
                'Access-Control-Allow-Credentials',
                'Access-Control-Expose-Headers',
                'Vary',
            ];

            foreach ($cabecalhosPermitidos as $cabecalho) {

                if ($response->headers->has($cabecalho)) {

                    $respostaSegura->headers->set(
                        $cabecalho,
                        $response->headers->all($cabecalho)
                    );
                }
            }

            // Preserva cookies presentes na resposta original.
            foreach ($response->headers->getCookies() as $cookie) {

                $respostaSegura->headers->setCookie($cookie);
            }

            // Evita o armazenamento da resposta de erro em cache.
            $respostaSegura->headers->set(
                'Cache-Control',
                'no-store, private'
            );

            return $respostaSegura;
        });

    })->create();