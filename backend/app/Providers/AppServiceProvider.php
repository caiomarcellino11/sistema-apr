<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Registra os servicos da aplicacao.
     */
    public function register(): void
    {
        //
    }

    /**
     * Inicializa os servicos da aplicacao.
     */
    public function boot(): void
    {
        $this->configurarRateLimiting();
    }

    /**
     * Configura os limites de requisicoes
     * para proteger a autenticacao.
     */
    private function configurarRateLimiting(): void
    {
        // Protecao contra tentativas excessivas de login.
        RateLimiter::for('login', function (Request $request) {

            $email = $request->input('email');

            $email = is_string($email)
                ? mb_strtolower(trim($email))
                : '';

            $ip = $request->ip() ?? 'desconhecido';

            return [

                // Limite geral de requisicoes por IP.
                Limit::perMinute(30)
                    ->by('login-ip:' . $ip)
                    ->response(function (
                        Request $request,
                        array $headers
                    ) {
                        return response()->json([
                            'message' => 'Muitas tentativas de login. Aguarde antes de tentar novamente.',
                        ], 429, $headers);
                    }),

                // Limite individual por e-mail e IP.
                Limit::perMinute(5)
                    ->by(
                        'login-email-ip:' .
                        hash('sha256', $email . '|' . $ip)
                    )
                    ->response(function (
                        Request $request,
                        array $headers
                    ) {
                        return response()->json([
                            'message' => 'Limite de tentativas de login atingido. Aguarde antes de tentar novamente.',
                        ], 429, $headers);
                    }),

            ];
        });

        // Protecao contra cadastros excessivos.
        RateLimiter::for('register', function (Request $request) {

            $ip = $request->ip() ?? 'desconhecido';

            return Limit::perMinute(5)
                ->by('register-ip:' . $ip)
                ->response(function (
                    Request $request,
                    array $headers
                ) {
                    return response()->json([
                        'message' => 'Muitas tentativas de cadastro. Aguarde antes de tentar novamente.',
                    ], 429, $headers);
                });
        });
    }
}