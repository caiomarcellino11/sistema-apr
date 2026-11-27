<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AprCamposOpcionaisTest extends TestCase
{
    use RefreshDatabase;

    // Monta os dados mínimos de uma APR para o cliente informado.
    private function dadosApr(int $clienteId, array $extra = []): array
    {
        return array_merge([
            'cliente_id' => $clienteId,
            'local' => 'Oficina de Manutenção',
            'atividade' => 'Troca de disjuntores',
            'responsavel_nome' => 'Responsável Teste',
            'data_emissao' => '2026-11-27',
            'etapas' => [
                [
                    'descricao' => 'Desenergizar o painel',
                    'riscos_envolvidos' => 'Choque elétrico',
                    'medidas_controle' => 'Bloqueio e etiquetagem',
                ],
            ],
            'executantes' => [
                [
                    'nome' => 'Executante Teste',
                    'funcao' => 'Aluno',
                ],
            ],
        ], $extra);
    }

    private function criarCliente(User $usuario)
    {
        return $usuario->clientes()->create([
            'nome' => 'SENAI Americana',
            'endereco' => 'Av. Brasil',
            'numero' => '2801',
            'bairro' => 'Parque Residencial Nardini',
            'cidade' => 'Americana',
            'estado' => 'SP',
            'tel_celular' => '(19) 3471-2600',
        ]);
    }

    public function test_apr_pode_ser_salva_sem_crea_e_sem_matricula(): void
    {
        $usuario = User::factory()->create();
        $cliente = $this->criarCliente($usuario);

        Sanctum::actingAs($usuario);

        // CREA e matrícula não são enviados.
        $resposta = $this->postJson('/api/aprs', $this->dadosApr($cliente->id));

        $resposta->assertCreated()
            ->assertJsonPath('responsavel_crea', null)
            ->assertJsonPath('executantes.0.matricula', null);

        $this->assertDatabaseHas('aprs', [
            'id' => $resposta->json('id'),
            'responsavel_crea' => null,
        ]);

        $this->assertDatabaseHas('executante_a_p_r_s', [
            'apr_id' => $resposta->json('id'),
            'nome' => 'Executante Teste',
            'matricula' => null,
        ]);
    }

    public function test_campos_vazios_sao_aceitos_como_nulos(): void
    {
        $usuario = User::factory()->create();
        $cliente = $this->criarCliente($usuario);

        Sanctum::actingAs($usuario);

        // O formulário pode mandar texto vazio: deve ser aceito.
        $dados = $this->dadosApr($cliente->id, ['responsavel_crea' => '']);
        $dados['executantes'][0]['matricula'] = '';

        $resposta = $this->postJson('/api/aprs', $dados);

        $resposta->assertCreated()
            ->assertJsonPath('responsavel_crea', null)
            ->assertJsonPath('executantes.0.matricula', null);
    }

    public function test_crea_e_matricula_continuam_sendo_salvos_quando_informados(): void
    {
        $usuario = User::factory()->create();
        $cliente = $this->criarCliente($usuario);

        Sanctum::actingAs($usuario);

        $dados = $this->dadosApr($cliente->id, ['responsavel_crea' => 'SP-506123456/D']);
        $dados['executantes'][0]['matricula'] = '20261045';

        $resposta = $this->postJson('/api/aprs', $dados);

        $resposta->assertCreated()
            ->assertJsonPath('responsavel_crea', 'SP-506123456/D')
            ->assertJsonPath('executantes.0.matricula', '20261045');
    }

    public function test_crea_pode_ser_apagado_ao_atualizar(): void
    {
        $usuario = User::factory()->create();
        $cliente = $this->criarCliente($usuario);

        $apr = $usuario->aprs()->create([
            'cliente_id' => $cliente->id,
            'local' => 'Oficina',
            'atividade' => 'Inspeção',
            'responsavel_nome' => 'Responsável Teste',
            'responsavel_crea' => 'SP-123',
            'data_emissao' => '2026-11-27',
        ]);

        Sanctum::actingAs($usuario);

        $resposta = $this->patchJson("/api/aprs/{$apr->id}", [
            'responsavel_crea' => null,
        ]);

        $resposta->assertOk()->assertJsonPath('responsavel_crea', null);

        $this->assertDatabaseHas('aprs', [
            'id' => $apr->id,
            'responsavel_crea' => null,
        ]);
    }
}