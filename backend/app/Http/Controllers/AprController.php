<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAprRequest;
use App\Http\Requests\UpdateAprRequest;
use App\Models\Apr;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class AprController extends Controller
{
    /**
     * Relacionamentos utilizados nas respostas da API.
     */
    private const RELACIONAMENTOS = [
        'cliente',
        'etapas',
        'executantes',
    ];

    /**
     * Campos principais permitidos na APR.
     */
    private const CAMPOS_APR = [
        'cliente_id',
        'local',
        'atividade',
        'responsavel_nome',
        'responsavel_crea',
        'data_emissao',
    ];

    /**
     * Lista somente as APRs do usuario autenticado.
     */
    public function index(Request $request): JsonResponse
    {
        $aprs = $request->user()
            ->aprs()
            ->with(self::RELACIONAMENTOS)
            ->orderByDesc('id')
            ->get();

        return response()->json($aprs);
    }

    /**
     * Cria uma nova APR.
     */
    public function store(StoreAprRequest $request): JsonResponse
    {
        $dados = $request->validated();

        $apr = DB::transaction(function () use ($dados, $request) {

            // Seleciona somente os campos permitidos.
            $dadosApr = Arr::only(
                $dados,
                self::CAMPOS_APR
            );

            $dadosApr['responsavel_crea'] =
                $dadosApr['responsavel_crea'] ?? null;

            // Associa a APR ao usuario autenticado.
            $apr = $request->user()
                ->aprs()
                ->create($dadosApr);

            $this->criarEtapas(
                $apr,
                $dados['etapas']
            );

            $this->criarExecutantes(
                $apr,
                $dados['executantes']
            );

            return $apr;
        });

        return response()->json(
            $apr->load(self::RELACIONAMENTOS),
            201
        );
    }

    /**
     * Consulta uma APR pertencente ao usuario.
     */
    public function show(
        Request $request,
        string $id
    ): JsonResponse {

        $apr = $this->buscarApr($request, $id);

        return response()->json(
            $apr->load(self::RELACIONAMENTOS)
        );
    }

    /**
     * Atualiza uma APR existente.
     */
    public function update(
        UpdateAprRequest $request,
        string $id
    ): JsonResponse {

        $apr = $this->buscarApr($request, $id);

        $dados = $request->validated();

        DB::transaction(function () use ($apr, $dados) {

            // Atualiza somente os campos permitidos.
            $apr->update(
                Arr::only(
                    $dados,
                    self::CAMPOS_APR
                )
            );

            // Substitui as etapas somente quando enviadas.
            if (isset($dados['etapas'])) {

                $apr->etapas()->delete();

                $this->criarEtapas(
                    $apr,
                    $dados['etapas']
                );
            }

            // Substitui os executantes somente quando enviados.
            if (isset($dados['executantes'])) {

                $apr->executantes()->delete();

                $this->criarExecutantes(
                    $apr,
                    $dados['executantes']
                );
            }
        });

        return response()->json(
            $apr->load(self::RELACIONAMENTOS)
        );
    }

    /**
     * Exclui uma APR pertencente ao usuario.
     */
    public function destroy(
        Request $request,
        string $id
    ): JsonResponse {

        $apr = $this->buscarApr($request, $id);

        $apr->delete();

        return response()->json([
            'mensagem' => 'APR excluída com sucesso.',
        ]);
    }

    /**
     * Localiza uma APR pertencente ao usuario autenticado.
     */
    private function buscarApr(
        Request $request,
        string $id
    ): Apr {

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
            abort(404, 'APR nao encontrada.');
        }

        // Impede o acesso a APRs de outros usuarios.
        return $request->user()
            ->aprs()
            ->findOrFail($idValidado);
    }

    /**
     * Registra as etapas da APR.
     */
    private function criarEtapas(
        Apr $apr,
        array $etapas
    ): void {

        foreach ($etapas as $index => $etapa) {

            $apr->etapas()->create([
                'numero_etapa' =>
                    $etapa['numero_etapa'] ?? ($index + 1),

                'descricao' =>
                    $etapa['descricao'],

                'riscos_envolvidos' =>
                    $etapa['riscos_envolvidos'],

                'medidas_controle' =>
                    $etapa['medidas_controle'],
            ]);
        }
    }

    /**
     * Registra os executantes da APR.
     */
    private function criarExecutantes(
        Apr $apr,
        array $executantes
    ): void {

        foreach ($executantes as $executante) {

            $apr->executantes()->create([
                'nome' =>
                    $executante['nome'],

                'funcao' =>
                    $executante['funcao'],

                'matricula' =>
                    $executante['matricula'] ?? null,
            ]);
        }
    }
}