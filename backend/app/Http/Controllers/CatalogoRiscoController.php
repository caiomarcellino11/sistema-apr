<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCatalogoRiscoRequest;
use App\Http\Requests\UpdateCatalogoRiscoRequest;
use App\Models\CatalogoRisco;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogoRiscoController extends Controller
{
    /**
     * Lista os itens do catalogo de riscos.
     */
    public function index(): JsonResponse
    {
        $itens = CatalogoRisco::query()
            ->orderBy('categoria')
            ->orderBy('descricao_etapa')
            ->get();

        return response()->json($itens);
    }

    /**
     * Cadastra um novo item no catalogo.
     * Operacao exclusiva de administradores.
     */
    public function store(
        StoreCatalogoRiscoRequest $request
    ): JsonResponse {

        // Recupera somente os dados validados.
        $dados = $request->validated();

        $item = CatalogoRisco::create($dados);

        return response()->json($item, 201);
    }

    /**
     * Consulta um item especifico do catalogo.
     */
    public function show(string $id): JsonResponse
    {
        $item = $this->buscarItem($id);

        return response()->json($item);
    }

    /**
     * Atualiza um item existente.
     * Operacao exclusiva de administradores.
     */
    public function update(
        UpdateCatalogoRiscoRequest $request,
        string $id
    ): JsonResponse {

        $item = $this->buscarItem($id);

        // Atualiza somente os campos validados.
        $item->update(
            $request->validated()
        );

        return response()->json($item);
    }

    /**
     * Exclui um item do catalogo.
     * Operacao exclusiva de administradores.
     */
    public function destroy(
        Request $request,
        string $id
    ): JsonResponse {

        // Camada adicional de autorizacao.
        abort_unless(
            $request->user()?->isAdmin() === true,
            403,
            'Voce nao possui permissao para excluir itens do catalogo.'
        );

        $item = $this->buscarItem($id);

        $item->delete();

        return response()->json([
            'mensagem' => 'Item removido do catálogo.',
        ]);
    }

    /**
     * Localiza um item valido do catalogo.
     */
    private function buscarItem(string $id): CatalogoRisco
    {
        $idValidado = filter_var(
            $id,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => 1,
                ],
            ]
        );

        // Rejeita identificadores invalidos.
        if ($idValidado === false) {
            abort(404, 'Item do catalogo nao encontrado.');
        }

        return CatalogoRisco::query()
            ->findOrFail($idValidado);
    }
}