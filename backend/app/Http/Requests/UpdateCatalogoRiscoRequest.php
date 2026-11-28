<?php

namespace App\Http\Requests;

use App\Models\CatalogoRisco;

class UpdateCatalogoRiscoRequest extends StoreCatalogoRiscoRequest
{
    /**
     * Verifica a permissao administrativa
     * e a existencia do item do catalogo.
     */
    public function authorize(): bool
    {
        // Exige privilegios administrativos.
        if (!parent::authorize()) {
            return false;
        }

        // Valida o identificador recebido pela rota.
        $id = filter_var(
            $this->route('catalogo_risco'),
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                ],
            ]
        );

        // Rejeita identificadores invalidos.
        if ($id === false) {
            abort(404, 'Item do catalogo nao encontrado.');
        }

        // Verifica se o item existe.
        $existe = CatalogoRisco::query()
            ->whereKey($id)
            ->exists();

        if (!$existe) {
            abort(404, 'Item do catalogo nao encontrado.');
        }

        return true;
    }

    /**
     * Define as regras de atualizacao.
     */
    public function rules(): array
    {
        // Reaproveita as regras de cadastro.
        $regras = parent::rules();

        // Permite atualizar somente os campos enviados.
        foreach ($regras as $campo => $validacoes) {

            $regras[$campo] = array_merge(
                ['sometimes'],
                $validacoes
            );

        }

        return $regras;
    }
}