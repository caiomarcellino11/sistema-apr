<?php

$allowedOrigins = array_values(array_unique(array_filter(
    array_map(
        'trim',
        explode(',', (string) env('CORS_ALLOWED_ORIGINS', ''))
    ),
    static fn (string $origin): bool =>
        preg_match(
            '~^https?://[a-zA-Z0-9.-]+(?::[0-9]{1,5})?$~D',
            $origin
        ) === 1
)));

return [

    /*
    |--------------------------------------------------------------------------
    | Rotas abrangidas pelo CORS
    |--------------------------------------------------------------------------
    */

    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
    ],

    /*
    |--------------------------------------------------------------------------
    | Metodos HTTP permitidos
    |--------------------------------------------------------------------------
    */

    'allowed_methods' => [
        'GET',
        'HEAD',
        'POST',
        'PUT',
        'PATCH',
        'DELETE',
        'OPTIONS',
    ],

    /*
    |--------------------------------------------------------------------------
    | Origens autorizadas pelo ambiente
    |--------------------------------------------------------------------------
    */

    'allowed_origins' => $allowedOrigins,

    'allowed_origins_patterns' => [],

    /*
    |--------------------------------------------------------------------------
    | Cabecalhos autorizados
    |--------------------------------------------------------------------------
    */

    'allowed_headers' => [
        'Accept',
        'Authorization',
        'Content-Type',
        'Origin',
        'X-Requested-With',
        'X-CSRF-TOKEN',
        'X-XSRF-TOKEN',
    ],

    'exposed_headers' => [],

    /*
    |--------------------------------------------------------------------------
    | Cache das requisicoes preflight
    |--------------------------------------------------------------------------
    */

    'max_age' => 0,

    /*
    |--------------------------------------------------------------------------
    | Permite o envio de cookies de autenticacao
    |--------------------------------------------------------------------------
    */

    'supports_credentials' => true,

];