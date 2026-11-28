<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreAprRequest extends FormRequest
{
    // Limites de validacao das APRs.
    public const MAX_ETAPAS = 50;
    public const MAX_EXECUTANTES = 50;

    /**
     * Permite a criacao por usuarios autenticados.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Define as regras de validacao.
     */
    public function rules(): array
    {
        return [

            // ==========================================
            // DADOS PRINCIPAIS DA APR
            // ==========================================

            'cliente_id' => [
                'bail',
                'required',
                'integer',
                'min:1',
                Rule::exists('clientes', 'id')
                    ->where('user_id', $this->user()->id),
            ],

            'local' => [
                'required',
                'string',
                'max:255',
                'not_regex:/\x00/',
            ],

            'atividade' => [
                'required',
                'string',
                'max:255',
                'not_regex:/\x00/',
            ],

            'responsavel_nome' => [
                'required',
                'string',
                'max:255',
                'not_regex:/\x00/',
            ],

            'responsavel_crea' => [
                'nullable',
                'string',
                'max:50',
                'not_regex:/\x00/',
            ],

            'data_emissao' => [
                'required',
                'date_format:Y-m-d',
            ],

            // ==========================================
            // ETAPAS DA APR
            // ==========================================

            'etapas' => [
                'bail',
                'required',
                'array',
                'list',
                'min:1',
                'max:' . self::MAX_ETAPAS,
            ],

            'etapas.*' => [
                'required',
                'array:numero_etapa,descricao,riscos_envolvidos,medidas_controle',
            ],

            'etapas.*.numero_etapa' => [
                'nullable',
                'integer',
                'min:1',
                'max:2147483647',
            ],

            'etapas.*.descricao' => [
                'required',
                'string',
                'max:3000',
                'not_regex:/\x00/',
            ],

            'etapas.*.riscos_envolvidos' => [
                'required',
                'string',
                'max:3000',
                'not_regex:/\x00/',
            ],

            'etapas.*.medidas_controle' => [
                'required',
                'string',
                'max:5000',
                'not_regex:/\x00/',
            ],

            // ==========================================
            // EXECUTANTES DA APR
            // ==========================================

            'executantes' => [
                'bail',
                'required',
                'array',
                'list',
                'min:1',
                'max:' . self::MAX_EXECUTANTES,
            ],

            'executantes.*' => [
                'required',
                'array:nome,funcao,matricula',
            ],

            'executantes.*.nome' => [
                'required',
                'string',
                'max:255',
                'not_regex:/\x00/',
            ],

            'executantes.*.funcao' => [
                'required',
                'string',
                'max:255',
                'not_regex:/\x00/',
            ],

            'executantes.*.matricula' => [
                'nullable',
                'string',
                'max:50',
                'not_regex:/\x00/',
            ],

        ];
    }

    /**
     * Verifica a numeracao final das etapas.
     */
    public function after(): array
    {
        return [

            function (Validator $validator): void {

                // Evita processar dados invalidos.
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $etapas = $this->input('etapas', []);

                $numerosUtilizados = [];

                foreach ($etapas as $index => $etapa) {

                    // Preserva a numeracao automatica.
                    $numero = (int) (
                        $etapa['numero_etapa'] ?? ($index + 1)
                    );

                    if (isset($numerosUtilizados[$numero])) {

                        $validator->errors()->add(
                            "etapas.{$index}.numero_etapa",
                            'A numeracao das etapas nao pode ser duplicada.'
                        );
                    }

                    $numerosUtilizados[$numero] = true;
                }
            },

        ];
    }

    /**
     * Personaliza as mensagens de validacao.
     */
    public function messages(): array
    {
        return [

            'cliente_id.required' =>
                'Selecione um cliente.',

            'cliente_id.integer' =>
                'O identificador do cliente deve ser inteiro.',

            'cliente_id.min' =>
                'O identificador do cliente e invalido.',

            'cliente_id.exists' =>
                'O cliente informado nao esta disponivel.',

            'local.required' =>
                'Informe o local da atividade.',

            'atividade.required' =>
                'Informe a atividade.',

            'responsavel_nome.required' =>
                'Informe o nome do responsavel.',

            'data_emissao.required' =>
                'Informe a data de emissao.',

            'data_emissao.date_format' =>
                'A data deve estar no formato AAAA-MM-DD.',

            'etapas.required' =>
                'Adicione pelo menos uma etapa.',

            'etapas.min' =>
                'Adicione pelo menos uma etapa.',

            'etapas.max' =>
                'A APR pode possuir no maximo 50 etapas.',

            'etapas.list' =>
                'A lista de etapas possui uma estrutura invalida.',

            'etapas.*.array' =>
                'A etapa possui campos invalidos.',

            'etapas.*.numero_etapa.min' =>
                'O numero da etapa deve ser positivo.',

            'etapas.*.numero_etapa.max' =>
                'O numero da etapa ultrapassa o limite permitido.',

            'etapas.*.descricao.required' =>
                'Informe a descricao da etapa.',

            'etapas.*.descricao.max' =>
                'A descricao deve possuir ate 3000 caracteres.',

            'etapas.*.riscos_envolvidos.required' =>
                'Informe os riscos envolvidos.',

            'etapas.*.riscos_envolvidos.max' =>
                'Os riscos devem possuir ate 3000 caracteres.',

            'etapas.*.medidas_controle.required' =>
                'Informe as medidas de controle.',

            'etapas.*.medidas_controle.max' =>
                'As medidas devem possuir ate 5000 caracteres.',

            'executantes.required' =>
                'Adicione pelo menos um executante.',

            'executantes.min' =>
                'Adicione pelo menos um executante.',

            'executantes.max' =>
                'A APR pode possuir no maximo 50 executantes.',

            'executantes.list' =>
                'A lista de executantes possui uma estrutura invalida.',

            'executantes.*.array' =>
                'O executante possui campos invalidos.',

            'executantes.*.nome.required' =>
                'Informe o nome do executante.',

            'executantes.*.funcao.required' =>
                'Informe a funcao do executante.',

            'executantes.*.matricula.max' =>
                'A matricula deve possuir ate 50 caracteres.',

            'not_regex' =>
                'O campo :attribute possui caracteres invalidos.',

        ];
    }
}