<?php

use App\Http\Controllers\AprController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogoRiscoController;
use App\Http\Controllers\ClienteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Rotas públicas (Cadastro e Login do aluno)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rotas protegidas (exigem aluno autenticado)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (Request $request) => $request->user());
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('clientes', ClienteController::class);
    Route::apiResource('aprs', AprController::class);
    Route::apiResource('catalogo-riscos', CatalogoRiscoController::class);
});