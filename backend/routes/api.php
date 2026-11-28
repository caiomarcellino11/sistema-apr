<?php

use App\Http\Controllers\AprController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogoRiscoController;
use App\Http\Controllers\ClienteController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ==========================================
// ROTAS PUBLICAS COM RATE LIMITING
// ==========================================

// Protecao contra cadastros excessivos.
Route::post('/register', [AuthController::class, 'register'])
    ->middleware('throttle:register');

// Protecao contra tentativas excessivas de login.
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:login');


// ==========================================
// ROTAS PROTEGIDAS POR AUTENTICACAO
// ==========================================

Route::middleware('auth:sanctum')->group(function () {

    // Informacoes do usuario autenticado.
    Route::get('/user', fn (Request $request) => $request->user());

    // Encerramento da autenticacao.
    Route::post('/logout', [AuthController::class, 'logout']);


    // ==========================================
    // GERENCIAMENTO DE CLIENTES E APRS
    // ==========================================

    Route::apiResource(
        'clientes',
        ClienteController::class
    );

    Route::apiResource(
        'aprs',
        AprController::class
    );


    // ==========================================
    // CATALOGO DE RISCOS - CONSULTA
    // ==========================================

    // Disponivel para todos os usuarios autenticados.
    Route::apiResource(
        'catalogo-riscos',
        CatalogoRiscoController::class
    )->only(['index', 'show']);


    // ==========================================
    // CATALOGO DE RISCOS - ADMINISTRACAO
    // ==========================================

    // Alteracoes exclusivas de administradores.
    Route::middleware('role:' . User::ROLE_ADMIN)
        ->group(function () {

            Route::apiResource(
                'catalogo-riscos',
                CatalogoRiscoController::class
            )->only(['store', 'update', 'destroy']);

        });

});