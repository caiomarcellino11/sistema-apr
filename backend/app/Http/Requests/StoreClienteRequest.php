<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClienteRequest extends FormRequest
{
    /**
     * Unidades federativas brasileiras permitidas.
     */
    private const ESTADOS = [
        'AC', 'AL', 'AP', 'AM', 'BA', 'CE',
        'DF', 'ES', 'GO', 'MA', 'MT', 'MS',
        'MG', 'PA', 'PB', 'PR', 'PE', 'PI',
        'RJ', 'RN', 'RS', 'RO', 'RR', 'SC',
        'SP', 'SE', 'TO',
    ];

    /**
     * Permite o cadastro por usuarios autenticados.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Normaliza a sigla do estado antes da validacao.
     */
    protected function prepareForValidation(): void
    {
        $estado = $this->input('estado');

        if (is_string($estado)) {
            $this->merge([
                'estado' => strtoupper(trim($estado)),
            ]);
        }
    }

    /**
     * Define as regras de validacao dos clientes.
     */
    public function rules(): array
    {
        return [

            // ==========================================
            // IDENTIFICACAO DO CLIENTE
            // ==========================================

            'nome' => [
                'bail',
                'required',
                'string',
                'max:255',
                'not_regex:/\x00/',
            ],

            // ==========================================
            // ENDERECO
            // ==========================================

            'endereco' => [
                'bail',
                'required',
                'string',
                'max:255',
                'not_regex:/\x00/',
            ],

            'numero' => [
                'bail',
                'required',
                'string',
                'max:20',
                'not_regex:/\x00/',
            ],

            'complemento' => [
                'bail',
                'nullable',
                'string',
                'max:255',
                'not_regex:/\x00/',
            ],

            'bairro' => [
                'bail',
                'required',
                'string',
                'max:255',
                'not_regex:/\x00/',
            ],

            'cidade' => [
                'bail',
                'required',
                'string',
                'max:255',
                'not_regex:/\x00/',
            ],

            'estado' => [
                'bail',
                'required',
                'string',
                'size:2',
                Rule::in(self::ESTADOS),
            ],

            // ==========================================
            // INFORMACOES DE CONTATO
            // ==========================================

            'tel_residencial' => [
                'bail',
                'nullable',
                'string',
                'max:20',
                'regex:/\A(?=.*[0-9])[0-9+(). \-]+\z/',
            ],

            'tel_celular' => [
                'bail',
                'required',
                'string',
                'max:20',
                'regex:/\A(?=.*[0-9])[0-9+(). \-]+\z/',
            ],

            'email' => [
                'bail',
                'required',
                'string',
                'max:255',
                'not_regex:/\x00/',
                'email',
            ],

        ];
    }

    /**
     * Personaliza as mensagens de validacao.
     */
    public function messages(): array
    {
        return [

            'nome.required' =>
                'Informe o nome do cliente.',

            'nome.max' =>
                'O nome deve possuir ate 255 caracteres.',

            'endereco.required' =>
                'Informe o endereco do cliente.',

            'endereco.max' =>
                'O endereco deve possuir ate 255 caracteres.',

            'numero.required' =>
                'Informe o numero do endereco.',

            'numero.max' =>
                'O numero deve possuir ate 20 caracteres.',

            'complemento.max' =>
                'O complemento deve possuir ate 255 caracteres.',

            'bairro.required' =>
                'Informe o bairro.',

            'bairro.max' =>
                'O bairro deve possuir ate 255 caracteres.',

            'cidade.required' =>
                'Informe a cidade.',

            'cidade.max' =>
                'A cidade deve possuir ate 255 caracteres.',

            'estado.required' =>
                'Informe o estado.',

            'estado.size' =>
                'O estado deve possuir uma sigla de dois caracteres.',

            'estado.in' =>
                'Informe uma unidade federativa brasileira valida.',

            'tel_residencial.max' =>
                'O telefone residencial deve possuir ate 20 caracteres.',

            'tel_residencial.regex' =>
                'Informe um telefone residencial valido.',

            'tel_celular.required' =>
                'Informe o telefone celular.',

            'tel_celular.max' =>
                'O telefone celular deve possuir ate 20 caracteres.',

            'tel_celular.regex' =>
                'Informe um telefone celular valido.',

            'email.required' =>
                'Informe o e-mail do cliente.',

            'email.email' =>
                'Informe um e-mail valido.',

            'email.max' =>
                'O e-mail deve possuir ate 255 caracteres.',

            'string' =>
                'O campo :attribute deve ser um texto.',

            'not_regex' =>
                'O campo :attribute possui caracteres invalidos.',

        ];
    }
}