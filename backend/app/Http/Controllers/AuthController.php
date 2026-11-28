<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Realiza o cadastro de um novo usuario.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        // Recupera somente os dados validados.
        $dados = $request->validated();

        $user = new User([
            'name' => $dados['name'],
            'email' => $dados['email'],

            // Gera explicitamente o hash da senha.
            'password' => Hash::make($dados['password']),
        ]);

        // Impede a elevacao de privilegios.
        $user->role = User::ROLE_ALUNO;

        $user->save();

        return $this->responderAutenticacao(
            $request,
            $user,
            201
        );
    }

    /**
     * Autentica um usuario existente.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        // Recupera somente os dados validados.
        $dados = $request->validated();

        $user = User::where(
            'email',
            $dados['email']
        )->first();

        // Verifica as credenciais informadas.
        if (
            !$user ||
            !Hash::check(
                $dados['password'],
                $user->password
            )
        ) {
            throw ValidationException::withMessages([
                'email' => [
                    'E-mail ou senha incorretos.',
                ],
            ]);
        }

        return $this->responderAutenticacao(
            $request,
            $user
        );
    }

    /**
     * Autenticacao por sessao ou Bearer Token.
     */
    private function responderAutenticacao(
        Request $request,
        User $user,
        int $status = 200
    ): JsonResponse {

        // Autenticacao do Front-End por sessao.
        if ($request->hasSession()) {

            Auth::guard('web')->login($user);

            // Protecao contra Session Fixation.
            $request->session()->regenerate();

            return response()->json([
                'user' => $user,
                'tipo_autenticacao' => 'session',
            ], $status);
        }

        // Mantem a compatibilidade com clientes da API.
        $token = $user
            ->createToken('auth_token')
            ->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'tipo_autenticacao' => 'bearer',
        ], $status);
    }

    /**
     * Encerra a autenticacao do usuario.
     */
    public function logout(Request $request): JsonResponse
    {
        // Encerra a autenticacao por sessao.
        if (
            $request->hasSession()
            && Auth::guard('web')->check()
        ) {
            Auth::guard('web')->logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            return response()->json([
                'mensagem' => 'Logout realizado com sucesso.',
            ]);
        }

        // Encerra a autenticacao por Bearer Token.
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'mensagem' => 'Logout realizado com sucesso.',
        ]);
    }
}