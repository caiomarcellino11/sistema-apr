<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCatalogoRiscoRequest extends FormRequest
{
    /**
     * Limites de tamanho dos campos do catalogo.
     */
    public const MAX_CATEGORIA = 255;
    public const MAX_DESCRICAO = 255;
    public const MAX_RISCOS = 3000;
    public const MAX_MEDIDAS_CONTROLE = 5000;

    /**
     * Permite o cadastro somente por administradores.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    /**
     * Define as regras de validacao do catalogo.
     */
    public function rules(): array
    {
        return [

            // Categoria do risco.
            'categoria' => [
                'bail',
                'required',
                'string',
                'max:' . self::MAX_CATEGORIA,
                'not_regex:/\x00/',
            ],

            // Descricao da etapa.
            'descricao_etapa' => [
                'bail',
                'required',
                'string',
                'max:' . self::MAX_DESCRICAO,
                'not_regex:/\x00/',
            ],

            // Riscos envolvidos.
            'riscos_envolvidos' => [
                'bail',
                'required',
                'string',
                'max:' . self::MAX_RISCOS,
                'not_regex:/\x00/',
            ],

            // EPIs e medidas de controle.
            'epis_medidas_controle' => [
                'bail',
                'required',
                'string',
                'max:' . self::MAX_MEDIDAS_CONTROLE,
                'not_regex:/\x00/',
            ],

        ];
    }

    /**
     * Personaliza as mensagens de validacao.
     */
    public function messages(): array
    {
        return [

            'categoria.required' =>
                'Informe a categoria do risco.',

            'categoria.max' =>
                'A categoria deve possuir ate 255 caracteres.',

            'descricao_etapa.required' =>
                'Informe a descricao da etapa.',

            'descricao_etapa.max' =>
                'A descricao deve possuir ate 255 caracteres.',

            'riscos_envolvidos.required' =>
                'Informe os riscos envolvidos.',

            'riscos_envolvidos.max' =>
                'Os riscos devem possuir ate 3000 caracteres.',

            'epis_medidas_controle.required' =>
                'Informe os EPIs e as medidas de controle.',

            'epis_medidas_controle.max' =>
                'As medidas de controle devem possuir ate 5000 caracteres.',

            'string' =>
                'O campo :attribute deve ser um texto.',

            'not_regex' =>
                'O campo :attribute possui caracteres invalidos.',

        ];
    }

    /**
     * Define nomes amigaveis para os campos.
     */
    public function attributes(): array
    {
        return [
            'categoria' => 'categoria',
            'descricao_etapa' => 'descricao da etapa',
            'riscos_envolvidos' => 'riscos envolvidos',
            'epis_medidas_controle' => 'EPIs e medidas de controle',
        ];
    }
}