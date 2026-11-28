<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $dados = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'bail',
                'required',
                'string',
                'min:15',
                'max:64',

                function ($attribute, $value, $fail) {
                    if (
                        config('hashing.driver', 'bcrypt') === 'bcrypt'
                        && strlen($value) > 72
                    ) {
                        $fail('A senha ultrapassa o limite de 72 bytes.');
                    }
                },
            ],

            'role' => 'missing',

        ], [
            'name.required' => 'Informe seu nome completo.',
            'email.required' => 'Informe seu e-mail.',
            'email.email' => 'Informe um e-mail valido.',
            'email.unique' => 'Este e-mail ja esta cadastrado.',
            'password.required' => 'Informe uma senha.',
            'password.min' => 'A senha deve possuir pelo menos 15 caracteres.',
            'password.max' => 'A senha deve possuir no maximo 64 caracteres.',
            'role.missing' => 'Nao e permitido definir o perfil de acesso.',
        ]);

        $user = new User([
            'name' => $dados['name'],
            'email' => $dados['email'],
            'password' => $dados['password'],
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

    public function login(Request $request)
    {
        $dados = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where(
            'email',
            $dados['email']
        )->first();

        if (
            !$user ||
            !Hash::check($dados['password'], $user->password)
        ) {
            throw ValidationException::withMessages([
                'email' => ['E-mail ou senha incorretos.'],
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
    ) {
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

    public function logout(Request $request)
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