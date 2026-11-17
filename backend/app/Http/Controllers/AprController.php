<?php

namespace App\Http\Controllers;

use App\Models\Apr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AprController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()?->id ?? $request->query('user_id');

        $query = Apr::with(['cliente', 'etapas', 'executantes'])
            ->orderByDesc('id');

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'user_id'                       => 'nullable|exists:users,id',
            'cliente_id'                    => 'required|exists:clientes,id',
            'local'                         => 'required|string|max:255',
            'atividade'                     => 'required|string|max:255',
            'responsavel_nome'              => 'required|string|max:255',
            'responsavel_crea'              => 'required|string|max:50',
            'data_emissao'                  => 'required|date',
            'etapas'                        => 'required|array|min:1',
            'etapas.*.numero_etapa'         => 'nullable|integer',
            'etapas.*.descricao'            => 'required|string',
            'etapas.*.riscos_envolvidos'    => 'required|string',
            'etapas.*.medidas_controle'     => 'required|string',
            'executantes'                   => 'required|array|min:1',
            'executantes.*.nome'            => 'required|string|max:255',
            'executantes.*.funcao'          => 'required|string|max:255',
            'executantes.*.matricula'       => 'required|string|max:50',
        ]);

        $userId = $request->user()?->id ?? ($dados['user_id'] ?? null);

        $apr = DB::transaction(function () use ($dados, $userId) {
            $apr = Apr::create([
                'user_id'          => $userId,
                'cliente_id'       => $dados['cliente_id'],
                'local'            => $dados['local'],
                'atividade'        => $dados['atividade'],
                'responsavel_nome' => $dados['responsavel_nome'],
                'responsavel_crea' => $dados['responsavel_crea'],
                'data_emissao'     => $dados['data_emissao'],
            ]);

            foreach ($dados['etapas'] as $index => $etapa) {
                $apr->etapas()->create([
                    'numero_etapa'      => $etapa['numero_etapa'] ?? ($index + 1),
                    'descricao'         => $etapa['descricao'],
                    'riscos_envolvidos' => $etapa['riscos_envolvidos'],
                    'medidas_controle'  => $etapa['medidas_controle'],
                ]);
            }

            foreach ($dados['executantes'] as $executante) {
                $apr->executantes()->create([
                    'nome'      => $executante['nome'],
                    'funcao'    => $executante['funcao'],
                    'matricula' => $executante['matricula'],
                ]);
            }

            return $apr;
        });

        return response()->json($apr->load(['cliente', 'etapas', 'executantes']), 201);
    }

    public function show(string $id)
    {
        $apr = Apr::with(['cliente', 'etapas', 'executantes'])->findOrFail($id);

        return response()->json($apr);
    }

    public function update(Request $request, string $id)
    {
        $apr = Apr::findOrFail($id);

        $dados = $request->validate([
            'cliente_id'                    => 'sometimes|required|exists:clientes,id',
            'local'                         => 'sometimes|required|string|max:255',
            'atividade'                     => 'sometimes|required|string|max:255',
            'responsavel_nome'              => 'sometimes|required|string|max:255',
            'responsavel_crea'              => 'sometimes|required|string|max:50',
            'data_emissao'                  => 'sometimes|required|date',
            'etapas'                        => 'sometimes|array|min:1',
            'etapas.*.numero_etapa'         => 'nullable|integer',
            'etapas.*.descricao'            => 'required_with:etapas|string',
            'etapas.*.riscos_envolvidos'    => 'required_with:etapas|string',
            'etapas.*.medidas_controle'     => 'required_with:etapas|string',
            'executantes'                   => 'sometimes|array|min:1',
            'executantes.*.nome'            => 'required_with:executantes|string|max:255',
            'executantes.*.funcao'          => 'required_with:executantes|string|max:255',
            'executantes.*.matricula'       => 'required_with:executantes|string|max:50',
        ]);

        DB::transaction(function () use ($apr, $dados) {
            $apr->update(collect($dados)->except(['etapas', 'executantes'])->toArray());

            if (isset($dados['etapas'])) {
                $apr->etapas()->delete();
                foreach ($dados['etapas'] as $index => $etapa) {
                    $apr->etapas()->create([
                        'numero_etapa'      => $etapa['numero_etapa'] ?? ($index + 1),
                        'descricao'         => $etapa['descricao'],
                        'riscos_envolvidos' => $etapa['riscos_envolvidos'],
                        'medidas_controle'  => $etapa['medidas_controle'],
                    ]);
                }
            }

            if (isset($dados['executantes'])) {
                $apr->executantes()->delete();
                foreach ($dados['executantes'] as $executante) {
                    $apr->executantes()->create([
                        'nome'      => $executante['nome'],
                        'funcao'    => $executante['funcao'],
                        'matricula' => $executante['matricula'],
                    ]);
                }
            }
        });

        return response()->json($apr->load(['cliente', 'etapas', 'executantes']));
    }

    public function destroy(string $id)
    {
        $apr = Apr::findOrFail($id);
        $apr->delete();

        return response()->json(['mensagem' => 'APR excluída com sucesso.']);
    }
}