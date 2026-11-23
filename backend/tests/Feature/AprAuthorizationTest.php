<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AprAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    // Cria um cliente associado ao usuário informado.
    private function criarCliente(User $usuario, string $nome = 'Cliente de Teste')
    {
        return $usuario->clientes()->create([
            'nome' => $nome,
            'endereco' => 'Rua Exemplo',
            'numero' => '100',
            'bairro' => 'Centro',
            'cidade' => 'Americana',
            'estado' => 'SP',
            'tel_celular' => '19999999999',
        ]);
    }

    // Cria uma APR associada ao usuário e ao seu cliente.
    private function criarApr(User $usuario, int $clienteId)
    {
        return $usuario->aprs()->create([
            'cliente_id' => $clienteId,
            'local' => 'Setor de Manutenção',
            'atividade' => 'Inspeção de equipamentos',
            'responsavel_nome' => 'Responsável Teste',
            'responsavel_crea' => '123456',
            'data_emissao' => '2026-11-23',
        ]);
    }

    public function test_usuario_nao_pode_visualizar_apr_de_outro_usuario(): void
    {
        $usuarioA = User::factory()->create();
        $usuarioB = User::factory()->create();

        $clienteA = $this->criarCliente($usuarioA);
        $apr = $this->criarApr($usuarioA, $clienteA->id);

        // Usuário B tenta visualizar a APR de A.
        Sanctum::actingAs($usuarioB);

        $resposta = $this->getJson("/api/aprs/{$apr->id}");

        $resposta->assertNotFound();
    }

    public function test_usuario_nao_pode_atualizar_apr_de_outro_usuario(): void
    {
        $usuarioA = User::factory()->create();
        $usuarioB = User::factory()->create();

        $clienteA = $this->criarCliente($usuarioA);
        $apr = $this->criarApr($usuarioA, $clienteA->id);

        // Usuário B tenta modificar a APR de A.
        Sanctum::actingAs($usuarioB);

        $resposta = $this->patchJson("/api/aprs/{$apr->id}", [
            'local' => 'Local alterado indevidamente',
        ]);

        $resposta->assertNotFound();

        // A APR deve permanecer inalterada.
        $this->assertDatabaseHas('aprs', [
            'id' => $apr->id,
            'user_id' => $usuarioA->id,
            'local' => 'Setor de Manutenção',
        ]);
    }

    public function test_usuario_nao_pode_excluir_apr_de_outro_usuario(): void
    {
        $usuarioA = User::factory()->create();
        $usuarioB = User::factory()->create();

        $clienteA = $this->criarCliente($usuarioA);
        $apr = $this->criarApr($usuarioA, $clienteA->id);

        // Usuário B tenta excluir a APR de A.
        Sanctum::actingAs($usuarioB);

        $resposta = $this->deleteJson("/api/aprs/{$apr->id}");

        $resposta->assertNotFound();

        // A APR deve continuar existindo.
        $this->assertDatabaseHas('aprs', [
            'id' => $apr->id,
            'user_id' => $usuarioA->id,
        ]);
    }

    public function test_usuario_nao_autenticado_nao_pode_listar_aprs(): void
    {
        // A API deve exigir autenticação.
        $resposta = $this->getJson('/api/aprs');

        $resposta->assertUnauthorized();
    }

    public function test_usuario_pode_visualizar_sua_propria_apr(): void
    {
        $usuario = User::factory()->create();

        $cliente = $this->criarCliente($usuario);
        $apr = $this->criarApr($usuario, $cliente->id);

        Sanctum::actingAs($usuario);

        $resposta = $this->getJson("/api/aprs/{$apr->id}");

        $resposta->assertOk();
        $resposta->assertJsonPath('id', $apr->id);
        $resposta->assertJsonPath('user_id', $usuario->id);
    }

    public function test_usuario_lista_apenas_suas_proprias_aprs(): void
    {
        $usuarioA = User::factory()->create();
        $usuarioB = User::factory()->create();

        // Cada usuário recebe seu próprio cliente.
        $clienteA = $this->criarCliente($usuarioA, 'Cliente A');
        $clienteB = $this->criarCliente($usuarioB, 'Cliente B');

        // Cada APR pertence ao usuário e ao cliente corretos.
        $aprA = $this->criarApr($usuarioA, $clienteA->id);
        $aprB = $this->criarApr($usuarioB, $clienteB->id);

        Sanctum::actingAs($usuarioA);

        $resposta = $this->getJson('/api/aprs');

        $resposta->assertOk();
        $resposta->assertJsonCount(1);
        $resposta->assertJsonPath('0.id', $aprA->id);

        // A APR do usuário B não pode aparecer.
        $resposta->assertJsonMissing([
            'id' => $aprB->id,
        ]);
    }

    public function test_apr_criada_pertence_ao_usuario_autenticado(): void
    {
        $usuarioA = User::factory()->create();
        $usuarioB = User::factory()->create();

        $clienteA = $this->criarCliente($usuarioA);

        Sanctum::actingAs($usuarioA);

        // Tentamos atribuir a APR ao usuário B.
        $resposta = $this->postJson('/api/aprs', [
            'user_id' => $usuarioB->id,
            'cliente_id' => $clienteA->id,
            'local' => 'Setor de Manutenção',
            'atividade' => 'Inspeção de equipamentos',
            'responsavel_nome' => 'Responsável Teste',
            'responsavel_crea' => '123456',
            'data_emissao' => '2026-11-23',
            'etapas' => [
                [
                    'numero_etapa' => 1,
                    'descricao' => 'Inspecionar equipamento',
                    'riscos_envolvidos' => 'Risco elétrico',
                    'medidas_controle' => 'Desligar o equipamento',
                ],
            ],
            'executantes' => [
                [
                    'nome' => 'Executante Teste',
                    'funcao' => 'Técnico',
                    'matricula' => '12345',
                ],
            ],
        ]);

        $resposta->assertCreated();

        // O proprietário deve ser o usuário autenticado.
        $resposta->assertJsonPath('user_id', $usuarioA->id);

        $this->assertDatabaseHas('aprs', [
            'id' => $resposta->json('id'),
            'user_id' => $usuarioA->id,
            'cliente_id' => $clienteA->id,
        ]);

        // Verificamos se a etapa foi cadastrada.
        $this->assertDatabaseHas('etapa_aprs', [
            'apr_id' => $resposta->json('id'),
            'descricao' => 'Inspecionar equipamento',
        ]);

        // Verificamos se o executante foi cadastrado.
        $this->assertDatabaseHas('executante_a_p_r_s', [
            'apr_id' => $resposta->json('id'),
            'nome' => 'Executante Teste',
        ]);
    }

    public function test_usuario_pode_atualizar_sua_propria_apr(): void
    {
        $usuario = User::factory()->create();

        $cliente = $this->criarCliente($usuario);
        $apr = $this->criarApr($usuario, $cliente->id);

        Sanctum::actingAs($usuario);

        $resposta = $this->patchJson("/api/aprs/{$apr->id}", [
            'local' => 'Laboratório de Manutenção',
        ]);

        $resposta->assertOk();

        $resposta->assertJsonPath(
            'local',
            'Laboratório de Manutenção'
        );

        $this->assertDatabaseHas('aprs', [
            'id' => $apr->id,
            'user_id' => $usuario->id,
            'local' => 'Laboratório de Manutenção',
        ]);
    }

    public function test_usuario_pode_excluir_sua_propria_apr(): void
    {
        $usuarioA = User::factory()->create();
        $usuarioB = User::factory()->create();

        $clienteA = $this->criarCliente($usuarioA, 'Cliente A');
        $clienteB = $this->criarCliente($usuarioB, 'Cliente B');

        $aprA = $this->criarApr($usuarioA, $clienteA->id);
        $aprB = $this->criarApr($usuarioB, $clienteB->id);

        Sanctum::actingAs($usuarioA);

        $resposta = $this->deleteJson("/api/aprs/{$aprA->id}");

        $resposta->assertOk();

        // APR do usuário A foi excluída.
        $this->assertDatabaseMissing('aprs', [
            'id' => $aprA->id,
        ]);

        // APR do usuário B permanece intacta.
        $this->assertDatabaseHas('aprs', [
            'id' => $aprB->id,
            'user_id' => $usuarioB->id,
            'cliente_id' => $clienteB->id,
        ]);
    }

    public function test_usuario_nao_pode_criar_apr_com_cliente_de_outro_usuario(): void
    {
        $usuarioA = User::factory()->create();
        $usuarioB = User::factory()->create();

        $clienteB = $this->criarCliente($usuarioB, 'Empresa Protegida');

        Sanctum::actingAs($usuarioA);

        $response = $this->postJson('/api/aprs', [
            'cliente_id' => $clienteB->id,
            'local' => 'Área de manutenção',
            'atividade' => 'Inspeção de equipamentos',
            'responsavel_nome' => 'Responsável Teste',
            'responsavel_crea' => '123456',
            'data_emissao' => '2026-11-23',
            'etapas' => [
                [
                    'numero_etapa' => 1,
                    'descricao' => 'Inspeção inicial',
                    'riscos_envolvidos' => 'Riscos operacionais',
                    'medidas_controle' => 'Verificação preventiva',
                ],
            ],
            'executantes' => [
                [
                    'nome' => 'Executante Teste',
                    'funcao' => 'Técnico',
                    'matricula' => '001',
                ],
            ],
        ]);

        // Cliente de outro usuário deve ser rejeitado.
        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['cliente_id']);

        // Nenhuma APR deve ter sido cadastrada.
        $this->assertDatabaseCount('aprs', 0);
    }

    public function test_usuario_nao_pode_atualizar_apr_com_cliente_de_outro_usuario(): void
    {
        $usuarioA = User::factory()->create();
        $usuarioB = User::factory()->create();

        $clienteA = $this->criarCliente($usuarioA, 'Empresa A');
        $clienteB = $this->criarCliente($usuarioB, 'Empresa B');

        $apr = $this->criarApr($usuarioA, $clienteA->id);

        Sanctum::actingAs($usuarioA);

        // Tentamos associar a APR ao cliente de outro usuário.
        $response = $this->putJson("/api/aprs/{$apr->id}", [
            'cliente_id' => $clienteB->id,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['cliente_id']);

        // A associação original deve permanecer intacta.
        $this->assertDatabaseHas('aprs', [
            'id' => $apr->id,
            'user_id' => $usuarioA->id,
            'cliente_id' => $clienteA->id,
        ]);
    }
}
