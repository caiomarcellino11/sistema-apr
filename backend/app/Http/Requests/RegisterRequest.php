<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Permite o cadastro publico de usuarios.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Define as regras de validacao do cadastro.
     */
    public function rules(): array
    {
        return [

            'name' => [
                'bail',
                'required',
                'string',
                'max:255',
                'not_regex:/\x00/',
            ],

            'email' => [
                'bail',
                'required',
                'string',
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

                    // Impede caracteres nulos na senha.
                    if (str_contains($value, "\0")) {
                        $fail(
                            'A senha possui caracteres invalidos.'
                        );

                        return;
                    }

                    // Respeita o limite de bytes do bcrypt.
                    if (
                        config('hashing.driver', 'bcrypt') === 'bcrypt'
                        && strlen($value) > 72
                    ) {
                        $fail(
                            'A senha ultrapassa o limite de 72 bytes.'
                        );
                    }
                },
            ],

            // Impede a escolha de privilegios no cadastro.
            'role' => [
                'missing',
            ],

        ];
    }

    /**
     * Personaliza as mensagens de validacao.
     */
    public function messages(): array
    {
        return [

            'name.required' =>
                'Informe seu nome completo.',

            'name.string' =>
                'O nome deve ser um texto.',

            'name.max' =>
                'O nome deve possuir no maximo 255 caracteres.',

            'name.not_regex' =>
                'O nome possui caracteres invalidos.',

            'email.required' =>
                'Informe seu e-mail.',

            'email.string' =>
                'O e-mail deve ser um texto.',

            'email.email' =>
                'Informe um e-mail valido.',

            'email.max' =>
                'O e-mail deve possuir no maximo 255 caracteres.',

            'email.unique' =>
                'Este e-mail ja esta cadastrado.',

            'password.required' =>
                'Informe uma senha.',

            'password.string' =>
                'A senha deve ser um texto.',

            'password.min' =>
                'A senha deve possuir pelo menos 15 caracteres.',

            'password.max' =>
                'A senha deve possuir no maximo 64 caracteres.',

            'role.missing' =>
                'Nao e permitido definir o perfil de acesso.',

        ];
    }
}