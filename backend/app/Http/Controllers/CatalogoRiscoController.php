<?php

namespace App\Http\Controllers;

use App\Models\CatalogoRisco;
use Illuminate\Http\Request;

class CatalogoRiscoController extends Controller
{
    public function index()
    {
        return response()->json(
            CatalogoRisco::orderBy('categoria')->orderBy('descricao_etapa')->get()
        );
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'categoria'             => 'required|string|max:255',
            'descricao_etapa'       => 'required|string|max:255',
            'riscos_envolvidos'     => 'required|string',
            'epis_medidas_controle' => 'required|string',
        ]);

        $item = CatalogoRisco::create($dados);

        return response()->json($item, 201);
    }

    public function show(string $id)
    {
        return response()->json(CatalogoRisco::findOrFail($id));
    }

    public function update(Request $request, string $id)
    {
        $item = CatalogoRisco::findOrFail($id);

        $dados = $request->validate([
            'categoria'             => 'sometimes|required|string|max:255',
            'descricao_etapa'       => 'sometimes|required|string|max:255',
            'riscos_envolvidos'     => 'sometimes|required|string',
            'epis_medidas_controle' => 'sometimes|required|string',
        ]);

        $item->update($dados);

        return response()->json($item);
    }

    public function destroy(string $id)
    {
        $item = CatalogoRisco::findOrFail($id);
        $item->delete();

        return response()->json(['mensagem' => 'Item removido do catálogo.']);
    }
}