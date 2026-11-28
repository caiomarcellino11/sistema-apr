<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Cliente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ClienteValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Prepara um usuario autenticado.
     */
    private function autenticarUsuario(): User
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        return $user;
    }

    /**
     * Dados ficticios para os testes.
     */
    private function dadosCliente(): array
    {
        return [
            'nome' => 'Cliente de Teste',
            'endereco' => 'Rua de Teste',
            'numero' => '100A',
            'complemento' => null,
            'bairro' => 'Centro',
            'cidade' => 'Americana',
            'estado' => 'SP',
            'tel_residencial' => null,
            'tel_celular' => '(19) 99999-9999',
            'email' => 'cliente@example.com',
        ];
    }

    /**
     * Cria um cliente pertencente ao usuario.
     */
    private function criarCliente(User $user): Cliente
    {
        return $user->clientes()->create(
            $this->dadosCliente()
        );
    }

    /**
     * TESTE 01
     * Permite o cadastro de um cliente valido.
     */
    public function test_cadastro_valido_de_cliente(): void
    {
        $user = $this->autenticarUsuario();

        $this->postJson(
            '/api/clientes',
            $this->dadosCliente()
        )
            ->assertCreated()
            ->assertJsonPath('user_id', $user->id)
            ->assertJsonPath('estado', 'SP')
            ->assertJsonPath('complemento', null)
            ->assertJsonPath('tel_residencial', null);

        $this->assertDatabaseCount('clientes', 1);
    }

    /**
     * TESTE 02
     * Normaliza a sigla do estado.
     */
    public function test_normaliza_sigla_do_estado(): void
    {
        $this->autenticarUsuario();

        $dados = $this->dadosCliente();

        $dados['estado'] = 'sp';

        $this->postJson('/api/clientes', $dados)
            ->assertCreated()
            ->assertJsonPath('estado', 'SP');

        $this->assertDatabaseHas('clientes', [
            'email' => $dados['email'],
            'estado' => 'SP',
        ]);
    }

    /**
     * TESTE 03
     * Rejeita unidades federativas inexistentes.
     */
    public function test_rejeita_estado_invalido(): void
    {
        $this->autenticarUsuario();

        $dados = $this->dadosCliente();

        $dados['estado'] = 'XX';

        $this->postJson('/api/clientes', $dados)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('estado');

        $this->assertDatabaseCount('clientes', 0);
    }

    /**
     * TESTE 04
     * Respeita os limites de tamanho do PostgreSQL.
     */
    public function test_rejeita_campos_acima_do_limite(): void
    {
        $this->autenticarUsuario();

        $dados = $this->dadosCliente();

        $dados['numero'] = str_repeat('1', 21);
        $dados['tel_residencial'] = str_repeat('9', 21);
        $dados['tel_celular'] = str_repeat('9', 21);

        $this->postJson('/api/clientes', $dados)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'numero',
                'tel_residencial',
                'tel_celular',
            ]);

        $this->assertDatabaseCount('clientes', 0);
    }

    /**
     * TESTE 05
     * Rejeita telefones com caracteres invalidos.
     */
    public function test_rejeita_telefones_invalidos(): void
    {
        $this->autenticarUsuario();

        $dados = $this->dadosCliente();

        $dados['tel_residencial'] = 'telefone123';
        $dados['tel_celular'] = 'celular123';

        $this->postJson('/api/clientes', $dados)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'tel_residencial',
                'tel_celular',
            ]);

        $this->assertDatabaseCount('clientes', 0);
    }

    /**
     * TESTE 06
     * Rejeita enderecos de e-mail invalidos.
     */
    public function test_rejeita_email_invalido(): void
    {
        $this->autenticarUsuario();

        $dados = $this->dadosCliente();

        $dados['email'] = 'email-invalido';

        $this->postJson('/api/clientes', $dados)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');

        $this->assertDatabaseCount('clientes', 0);
    }

    /**
     * TESTE 07
     * Impede caracteres nulos nos campos de texto.
     */
    public function test_rejeita_caracteres_nulos(): void
    {
        $this->autenticarUsuario();

        $dados = $this->dadosCliente();

        $dados['nome'] = "Cliente\0Teste";
        $dados['endereco'] = "Rua\0Teste";
        $dados['bairro'] = "Bairro\0Teste";

        $this->postJson('/api/clientes', $dados)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'nome',
                'endereco',
                'bairro',
            ]);

        $this->assertDatabaseCount('clientes', 0);
    }

    /**
     * TESTE 08
     * Permite atualizacoes parciais.
     */
    public function test_atualizacao_parcial_preserva_dados(): void
    {
        $user = $this->autenticarUsuario();

        $cliente = $this->criarCliente($user);

        $this->patchJson(
            "/api/clientes/{$cliente->id}",
            [
                'nome' => 'Cliente Atualizado',
            ]
        )
            ->assertOk()
            ->assertJsonPath('nome', 'Cliente Atualizado')
            ->assertJsonPath('email', $cliente->email)
            ->assertJsonPath('estado', 'SP')
            ->assertJsonPath(
                'tel_celular',
                $cliente->tel_celular
            );

        $this->assertDatabaseHas('clientes', [
            'id' => $cliente->id,
            'nome' => 'Cliente Atualizado',
            'user_id' => $user->id,
        ]);
    }

    /**
     * TESTE 09
     * Impede a alteracao do proprietario do cliente.
     */
    public function test_usuario_nao_pode_alterar_proprietario(): void
    {
        $user = $this->autenticarUsuario();

        $cliente = $this->criarCliente($user);

        $outroUsuario = User::factory()->create();

        $this->patchJson(
            "/api/clientes/{$cliente->id}",
            [
                'nome' => 'Nome Atualizado',
                'user_id' => $outroUsuario->id,
            ]
        )
            ->assertOk()
            ->assertJsonPath('user_id', $user->id);

        $this->assertDatabaseHas('clientes', [
            'id' => $cliente->id,
            'user_id' => $user->id,
        ]);
    }

    /**
     * TESTE 10
     * Rejeita identificadores invalidos nas rotas.
     */
    public function test_rejeita_identificadores_invalidos(): void
    {
        $this->autenticarUsuario();

        $this->getJson('/api/clientes/invalido')
            ->assertNotFound();

        $this->patchJson('/api/clientes/invalido', [
            'nome' => 'Cliente Atualizado',
        ])->assertNotFound();

        $this->deleteJson('/api/clientes/invalido')
            ->assertNotFound();
    }

    /**
     * TESTE 11
     * Impede a exclusao de clientes vinculados a APRs.
     */
    public function test_nao_exclui_cliente_vinculado_a_apr(): void
    {
        $user = $this->autenticarUsuario();

        $cliente = $this->criarCliente($user);

        $apr = $user->aprs()->create([
            'cliente_id' => $cliente->id,
            'local' => 'Laboratorio de Teste',
            'atividade' => 'Atividade ficticia',
            'responsavel_nome' => 'Responsavel de Teste',
            'responsavel_crea' => null,
            'data_emissao' => '2026-11-28',
        ]);

        $this->deleteJson(
            "/api/clientes/{$cliente->id}"
        )
            ->assertStatus(409)
            ->assertJsonStructure([
                'message',
                'mensagem',
            ]);

        $this->assertDatabaseHas('clientes', [
            'id' => $cliente->id,
        ]);

        $this->assertDatabaseHas('aprs', [
            'id' => $apr->id,
            'cliente_id' => $cliente->id,
        ]);
    }

    /**
     * TESTE 12
     * Permite excluir clientes sem APRs vinculadas.
     */
    public function test_exclui_cliente_sem_aprs_vinculadas(): void
    {
        $user = $this->autenticarUsuario();

        $cliente = $this->criarCliente($user);

        $this->deleteJson(
            "/api/clientes/{$cliente->id}"
        )
            ->assertOk()
            ->assertJsonStructure([
                'mensagem',
            ]);

        $this->assertDatabaseMissing('clientes', [
            'id' => $cliente->id,
        ]);
    }
}