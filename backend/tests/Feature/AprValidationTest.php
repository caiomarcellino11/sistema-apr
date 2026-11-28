<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AprValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Prepara um usuario autenticado e dados ficticios.
     */
    private function prepararDados(): array
    {
        $user = User::factory()->create();

        $cliente = $user->clientes()->create([
            'nome' => 'Cliente de Teste',
            'endereco' => 'Rua de Teste',
            'numero' => '100',
            'bairro' => 'Centro',
            'cidade' => 'Americana',
            'estado' => 'SP',
            'tel_celular' => '19900000000',
        ]);

        Sanctum::actingAs($user);

        return [
            'cliente_id' => $cliente->id,
            'local' => 'Laboratorio de Teste',
            'atividade' => 'Atividade ficticia',
            'responsavel_nome' => 'Responsavel de Teste',
            'responsavel_crea' => null,
            'data_emissao' => '2026-11-28',

            'etapas' => [
                [
                    'descricao' => 'Descricao de teste',
                    'riscos_envolvidos' => 'Texto de teste',
                    'medidas_controle' => 'Texto de teste',
                ],
            ],

            'executantes' => [
                [
                    'nome' => 'Executante de Teste',
                    'funcao' => 'Aluno',
                    'matricula' => null,
                ],
            ],
        ];
    }

    /**
     * Cria uma APR valida para testes de atualizacao.
     */
    private function criarApr(): array
    {
        return $this->postJson(
            '/api/aprs',
            $this->prepararDados()
        )
            ->assertCreated()
            ->json();
    }

    /**
     * TESTE 01
     * Aceita uma APR valida com numeracao automatica.
     */
    public function test_apr_valida_com_numeracao_automatica(): void
    {
        $dados = $this->prepararDados();

        $dados['etapas'][] = $dados['etapas'][0];

        $this->postJson('/api/aprs', $dados)
            ->assertCreated()
            ->assertJsonPath('etapas.0.numero_etapa', 1)
            ->assertJsonPath('etapas.1.numero_etapa', 2)
            ->assertJsonPath('responsavel_crea', null)
            ->assertJsonPath('executantes.0.matricula', null);

        $this->assertDatabaseCount('aprs', 1);
        $this->assertDatabaseCount('etapa_aprs', 2);
        $this->assertDatabaseCount('executante_a_p_r_s', 1);
    }

    /**
     * TESTE 02
     * Rejeita identificadores e datas invalidas.
     */
    public function test_rejeita_cliente_id_e_data_invalidos(): void
    {
        $dados = $this->prepararDados();

        $dados['cliente_id'] = 'invalido';
        $dados['data_emissao'] = '28/11/2026';

        $this->postJson('/api/aprs', $dados)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'cliente_id',
                'data_emissao',
            ]);

        $this->assertDatabaseCount('aprs', 0);
    }

    /**
     * TESTE 03
     * Rejeita listas com estruturas invalidas.
     */
    public function test_rejeita_listas_nao_sequenciais(): void
    {
        $dados = $this->prepararDados();

        $dados['etapas'] = [
            'primeira' => $dados['etapas'][0],
        ];

        $dados['executantes'] = [
            'primeiro' => $dados['executantes'][0],
        ];

        $this->postJson('/api/aprs', $dados)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'etapas',
                'executantes',
            ]);

        $this->assertDatabaseCount('aprs', 0);
    }

    /**
     * TESTE 04
     * Impede quantidades excessivas de registros.
     */
    public function test_rejeita_quantidade_excessiva_de_registros(): void
    {
        $dados = $this->prepararDados();

        $dados['etapas'] = array_fill(
            0,
            51,
            $dados['etapas'][0]
        );

        $dados['executantes'] = array_fill(
            0,
            51,
            $dados['executantes'][0]
        );

        $this->postJson('/api/aprs', $dados)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'etapas',
                'executantes',
            ]);

        $this->assertDatabaseCount('aprs', 0);
    }

    /**
     * TESTE 05
     * Verifica limites de tamanho e caracteres invalidos.
     */
    public function test_rejeita_textos_invalidos(): void
    {
        $dados = $this->prepararDados();

        $dados['local'] = "Local\0invalido";

        $dados['etapas'][0]['descricao'] =
            str_repeat('a', 3001);

        $dados['etapas'][0]['riscos_envolvidos'] =
            str_repeat('a', 3001);

        $dados['etapas'][0]['medidas_controle'] =
            str_repeat('a', 5001);

        $this->postJson('/api/aprs', $dados)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'local',
                'etapas.0.descricao',
                'etapas.0.riscos_envolvidos',
                'etapas.0.medidas_controle',
            ]);

        $this->assertDatabaseCount('aprs', 0);
    }

    /**
     * TESTE 06
     * Rejeita numeros de etapas invalidos.
     */
    public function test_rejeita_numeracao_invalida(): void
    {
        $dados = $this->prepararDados();

        $etapa = $dados['etapas'][0];

        $dados['etapas'] = [
            array_merge($etapa, [
                'numero_etapa' => 0,
            ]),
            array_merge($etapa, [
                'numero_etapa' => -1,
            ]),
            array_merge($etapa, [
                'numero_etapa' => 2147483648,
            ]),
        ];

        $this->postJson('/api/aprs', $dados)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'etapas.0.numero_etapa',
                'etapas.1.numero_etapa',
                'etapas.2.numero_etapa',
            ]);

        $this->assertDatabaseCount('aprs', 0);
    }

    /**
     * TESTE 07
     * Impede duplicidade entre numeracao manual e automatica.
     */
    public function test_rejeita_numeracao_duplicada(): void
    {
        $dados = $this->prepararDados();

        $etapa = $dados['etapas'][0];

        $dados['etapas'] = [
            array_merge($etapa, [
                'numero_etapa' => 2,
            ]),
            $etapa,
        ];

        $this->postJson('/api/aprs', $dados)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(
                'etapas.1.numero_etapa'
            );

        $this->assertDatabaseCount('aprs', 0);
    }

    /**
     * TESTE 08
     * Impede campos nao autorizados nas listas.
     */
    public function test_rejeita_campos_nao_permitidos(): void
    {
        $dados = $this->prepararDados();

        $dados['etapas'][0]['apr_id'] = 999;
        $dados['executantes'][0]['apr_id'] = 999;

        $this->postJson('/api/aprs', $dados)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'etapas.0',
                'executantes.0',
            ]);

        $this->assertDatabaseCount('aprs', 0);
    }

    /**
     * TESTE 09
     * Permite atualizar somente um campo.
     */
    public function test_atualizacao_parcial_preserva_relacionamentos(): void
    {
        $apr = $this->criarApr();

        $this->patchJson("/api/aprs/{$apr['id']}", [
            'local' => 'Local atualizado',
        ])
            ->assertOk()
            ->assertJsonPath('local', 'Local atualizado')
            ->assertJsonPath(
                'etapas.0.id',
                $apr['etapas'][0]['id']
            )
            ->assertJsonPath(
                'executantes.0.id',
                $apr['executantes'][0]['id']
            );

        $this->assertDatabaseCount('etapa_aprs', 1);
        $this->assertDatabaseCount('executante_a_p_r_s', 1);
    }

    /**
     * TESTE 10
     * Impede alteracoes quando a validacao falha.
     */
    public function test_atualizacao_invalida_preserva_dados(): void
    {
        $apr = $this->criarApr();

        $this->patchJson("/api/aprs/{$apr['id']}", [
            'local' => 'Local alterado',
            'etapas' => [],
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('etapas');

        $this->assertDatabaseHas('aprs', [
            'id' => $apr['id'],
            'local' => $apr['local'],
        ]);

        $this->assertDatabaseHas('etapa_aprs', [
            'id' => $apr['etapas'][0]['id'],
            'apr_id' => $apr['id'],
        ]);

        $this->assertDatabaseHas('executante_a_p_r_s', [
            'id' => $apr['executantes'][0]['id'],
            'apr_id' => $apr['id'],
        ]);
    }

    /**
     * TESTE 11
     * Verifica a propriedade antes da validacao.
     */
    public function test_propriedade_verificada_antes_da_validacao(): void
    {
        $apr = $this->criarApr();

        $outroUsuario = User::factory()->create();

        Sanctum::actingAs($outroUsuario);

        $this->patchJson("/api/aprs/{$apr['id']}", [
            'local' => '',
            'cliente_id' => 'invalido',
        ])->assertNotFound();

        $this->assertDatabaseHas('aprs', [
            'id' => $apr['id'],
            'local' => $apr['local'],
            'user_id' => $apr['user_id'],
        ]);
    }

    /**
     * TESTE 12
     * Rejeita identificadores invalidos nas rotas.
     */
    public function test_rejeita_identificadores_invalidos_na_rota(): void
    {
        $this->prepararDados();

        $this->getJson('/api/aprs/invalido')
            ->assertNotFound();

        $this->patchJson('/api/aprs/invalido', [
            'local' => 'Local de teste',
        ])->assertNotFound();

        $this->deleteJson('/api/aprs/invalido')
            ->assertNotFound();
    }
}