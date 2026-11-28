<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Lista somente os clientes do usuario autenticado.
     */
    public function index(Request $request): JsonResponse
    {
        $clientes = $request->user()
            ->clientes()
            ->orderBy('nome')
            ->get();

        return response()->json($clientes);
    }

    /**
     * Cadastra um novo cliente.
     */
    public function store(
        StoreClienteRequest $request
    ): JsonResponse {

        // Recupera somente os dados validados.
        $dados = $request->validated();

        // Associa o cliente ao usuario autenticado.
        $cliente = $request->user()
            ->clientes()
            ->create($dados);

        return response()->json($cliente, 201);
    }

    /**
     * Consulta um cliente pertencente ao usuario.
     */
    public function show(
        Request $request,
        string $id
    ): JsonResponse {

        $cliente = $this->buscarCliente(
            $request,
            $id
        );

        return response()->json($cliente);
    }

    /**
     * Atualiza os dados de um cliente.
     */
    public function update(
        UpdateClienteRequest $request,
        string $id
    ): JsonResponse {

        $cliente = $this->buscarCliente(
            $request,
            $id
        );

        // Atualiza somente os dados validados.
        $cliente->update(
            $request->validated()
        );

        return response()->json($cliente);
    }

    /**
     * Exclui um cliente pertencente ao usuario.
     */
    public function destroy(
        Request $request,
        string $id
    ): JsonResponse {

        $cliente = $this->buscarCliente(
            $request,
            $id
        );

        // Impede a exclusao de clientes vinculados a APRs.
        if ($cliente->aprs()->exists()) {
            return $this->respostaClienteVinculado();
        }

        try {

            $cliente->delete();

        } catch (QueryException $exception) {

            // Trata violacoes de chave estrangeira.
            // Protege contra vinculos criados durante a exclusao.
            if ($this->violacaoChaveEstrangeira($exception)) {

                return $this->respostaClienteVinculado();
            }

            // Outros erros serao tratados pelo Laravel.
            throw $exception;
        }

        return response()->json([
            'mensagem' => 'Cliente excluído com sucesso.',
        ]);
    }

    /**
     * Localiza um cliente pertencente ao usuario autenticado.
     */
    private function buscarCliente(
        Request $request,
        string $id
    ): Cliente {

        $idValidado = filter_var(
            $id,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                ],
            ]
        );

        // Impede consultas com identificadores invalidos.
        if ($idValidado === false) {
            abort(404, 'Cliente nao encontrado.');
        }

        // Protege os clientes de outros usuarios.
        return $request->user()
            ->clientes()
            ->findOrFail($idValidado);
    }

    /**
     * Retorna uma resposta segura para clientes vinculados.
     */
    private function respostaClienteVinculado(): JsonResponse
    {
        $mensagem = 'Nao e possivel excluir este cliente, pois ele esta vinculado a uma ou mais APRs.';

        return response()->json([
            'message' => $mensagem,
            'mensagem' => $mensagem,
        ], 409);
    }

    /**
     * Identifica violacoes de chave estrangeira.
     */
    private function violacaoChaveEstrangeira(
        QueryException $exception
    ): bool {

        $sqlState = (string) (
            $exception->errorInfo[0] ?? ''
        );

        $codigo = (int) (
            $exception->errorInfo[1] ?? 0
        );

        $mensagem = (string) (
            $exception->errorInfo[2] ?? ''
        );

        // PostgreSQL.
        if ($sqlState === '23503') {
            return true;
        }

        // SQLite utilizado nos testes automatizados.
        if (
            $sqlState === '23000'
            && in_array($codigo, [19, 787], true)
            && str_contains(
                strtolower($mensagem),
                'foreign key constraint failed'
            )
        ) {
            return true;
        }

        return false;
    }
}