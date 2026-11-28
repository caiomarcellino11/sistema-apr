<?php

use App\Http\Controllers\AprController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogoRiscoController;
use App\Http\Controllers\ClienteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;

// Rotas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rotas protegidas por autenticação
Route::middleware('auth:sanctum')->group(function () {

    // Informações do usuário e logout
    Route::get('/user', fn (Request $request) => $request->user());
    Route::post('/logout', [AuthController::class, 'logout']);

    // Gerenciamento de clientes e APRs
    Route::apiResource('clientes', ClienteController::class);
    Route::apiResource('aprs', AprController::class);

    // Catálogo: consulta disponível para usuários autenticados
    Route::apiResource('catalogo-riscos', CatalogoRiscoController::class)
        ->only(['index', 'show']);

    // Catálogo: alterações exclusivas de administradores
    Route::middleware('role:' . User::ROLE_ADMIN)->group(function () {

        Route::apiResource('catalogo-riscos', CatalogoRiscoController::class)
            ->only(['store', 'update', 'destroy']);

    });

});