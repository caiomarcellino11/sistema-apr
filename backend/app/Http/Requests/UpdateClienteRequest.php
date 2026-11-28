<?php

namespace App\Http\Requests;

class UpdateClienteRequest extends StoreClienteRequest
{
    /**
     * Verifica se o cliente pertence ao usuario autenticado.
     */
    public function authorize(): bool
    {
        if (!parent::authorize()) {
            return false;
        }

        // Valida o identificador recebido pela rota.
        $id = filter_var(
            $this->route('cliente'),
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                ],
            ]
        );

        // Rejeita identificadores invalidos.
        if ($id === false) {
            abort(404, 'Cliente nao encontrado.');
        }

        // Verifica se o cliente pertence ao usuario.
        $existe = $this->user()
            ->clientes()
            ->whereKey($id)
            ->exists();

        // Protege os registros de outros usuarios.
        if (!$existe) {
            abort(404, 'Cliente nao encontrado.');
        }

        return true;
    }

    /**
     * Define as regras de atualizacao do cliente.
     */
    public function rules(): array
    {
        // Reaproveita as regras de cadastro.
        $regras = parent::rules();

        // Permite a atualizacao parcial dos dados.
        foreach ($regras as $campo => $validacoes) {

            $regras[$campo] = array_merge(
                ['sometimes'],
                $validacoes
            );

        }

        return $regras;
    }
}