<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Permite o acesso publico ao login.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Define as regras de validacao do login.
     */
    public function rules(): array
    {
        return [

            'email' => [
                'bail',
                'required',
                'string',
                'email',
                'max:255',
            ],

            'password' => [
                'bail',
                'required',
                'string',
                'max:1024',

                function ($attribute, $value, $fail) {

                    // Impede caracteres nulos na senha.
                    if (str_contains($value, "\0")) {
                        $fail(
                            'A senha possui caracteres invalidos.'
                        );

                        return;
                    }

                    // Impede senhas acima do limite do bcrypt.
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

        ];
    }

    /**
     * Personaliza as mensagens de validacao.
     */
    public function messages(): array
    {
        return [

            'email.required' =>
                'Informe seu e-mail.',

            'email.string' =>
                'O e-mail deve ser um texto.',

            'email.email' =>
                'Informe um e-mail valido.',

            'email.max' =>
                'O e-mail deve possuir no maximo 255 caracteres.',

            'password.required' =>
                'Informe uma senha.',

            'password.string' =>
                'A senha deve ser um texto.',

            'password.max' =>
                'A senha ultrapassa o tamanho permitido.',

        ];
    }
}