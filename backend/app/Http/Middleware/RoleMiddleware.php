<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        string ...$roles
    ): Response {
        $user = $request->user();

        // Verifica se o usuário está autenticado.
        if (!$user) {
            return response()->json([
                'message' => 'Usuário não autenticado.',
            ], 401);
        }

        // Verifica se o perfil possui a permissão necessária.
        if (!in_array($user->role, $roles, true)) {
            return response()->json([
                'message' => 'Você não possui permissão para realizar esta ação.',
            ], 403);
        }

        // Permite que a requisição continue.
        return $next($request);
    }
}