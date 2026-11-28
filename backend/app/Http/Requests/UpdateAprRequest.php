<?php

namespace App\Http\Requests;

class UpdateAprRequest extends StoreAprRequest
{
    /**
     * Verifica se a APR pertence ao usuario autenticado.
     */
    public function authorize(): bool
    {
        if (!parent::authorize()) {
            return false;
        }

        // Valida o identificador recebido pela rota.
        $id = filter_var(
            $this->route('apr'),
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                ],
            ]
        );

        if ($id === false) {
            abort(404, 'APR nao encontrada.');
        }

        // Verifica a propriedade da APR.
        $existe = $this->user()
            ->aprs()
            ->whereKey($id)
            ->exists();

        // Nao revela a existencia de APRs de outros usuarios.
        if (!$existe) {
            abort(404, 'APR nao encontrada.');
        }

        return true;
    }

    /**
     * Define as regras de atualizacao da APR.
     */
    public function rules(): array
    {
        // Reaproveita as regras de criacao.
        $regras = parent::rules();

        // Campos que podem ser atualizados individualmente.
        $camposOpcionais = [
            'cliente_id',
            'local',
            'atividade',
            'responsavel_nome',
            'responsavel_crea',
            'data_emissao',
            'etapas',
            'executantes',
        ];

        foreach ($camposOpcionais as $campo) {

            $regras[$campo] = array_merge(
                ['sometimes'],
                $regras[$campo]
            );

        }

        return $regras;
    }
}