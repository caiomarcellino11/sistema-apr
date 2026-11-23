<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ClienteAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_nao_pode_consultar_cliente_de_outro_usuario(): void
    {
        // Criamos dois usuários diferentes.
        $usuarioA = User::factory()->create();
        $usuarioB = User::factory()->create();

        // O cliente pertence exclusivamente ao usuário B.
        $cliente = $usuarioB->clientes()->create([
            'nome' => 'Empresa Teste',
            'endereco' => 'Rua das Flores',
            'numero' => '100',
            'bairro' => 'Centro',
            'cidade' => 'Americana',
            'estado' => 'SP',
            'tel_celular' => '19999999999',
        ]);

        // Autenticamos o usuário A.
        Sanctum::actingAs($usuarioA);

        // O usuário A tenta acessar o cliente do usuário B.
        $response = $this->getJson("/api/clientes/{$cliente->id}");

        // O Laravel deve impedir o acesso.
        $response->assertNotFound();
    }

    public function test_usuario_lista_apenas_seus_proprios_clientes(): void
{
    // Criamos dois usuários.
    $usuarioA = User::factory()->create();
    $usuarioB = User::factory()->create();

    // Criamos um cliente para cada usuário.
    $clienteA = $usuarioA->clientes()->create([
        'nome' => 'Empresa A',
        'endereco' => 'Rua A',
        'numero' => '100',
        'bairro' => 'Centro',
        'cidade' => 'Americana',
        'estado' => 'SP',
        'tel_celular' => '19999999999',
    ]);

    $clienteB = $usuarioB->clientes()->create([
        'nome' => 'Empresa B',
        'endereco' => 'Rua B',
        'numero' => '200',
        'bairro' => 'Centro',
        'cidade' => 'Americana',
        'estado' => 'SP',
        'tel_celular' => '19888888888',
    ]);

    // Autenticamos o usuário A.
    Sanctum::actingAs($usuarioA);

    // Consultamos a listagem de clientes.
    $response = $this->getJson('/api/clientes');

    // A resposta deve conter somente o cliente A.
    $response->assertOk()
        ->assertJsonCount(1)
        ->assertJsonFragment(['id' => $clienteA->id])
        ->assertJsonMissing(['id' => $clienteB->id]);
}
public function test_cliente_e_associado_ao_usuario_autenticado(): void
{
    // Criamos dois usuários.
    $usuarioA = User::factory()->create();
    $usuarioB = User::factory()->create();

    // Autenticamos o usuário A.
    Sanctum::actingAs($usuarioA);

    // Tentamos cadastrar um cliente usando o ID do usuário B.
    $response = $this->postJson('/api/clientes', [
        'nome' => 'Empresa Teste',
        'endereco' => 'Rua das Flores',
        'numero' => '100',
        'bairro' => 'Centro',
        'cidade' => 'Americana',
        'estado' => 'SP',
        'tel_celular' => '19999999999',
        'email' => 'empresa@exemplo.com',
        'user_id' => $usuarioB->id,
    ]);

    // O cadastro deve ser realizado para o usuário A.
    $response->assertCreated()
        ->assertJsonPath('user_id', $usuarioA->id);

    // Confirmamos que o PostgreSQL não é utilizado neste teste.
    $this->assertDatabaseHas('clientes', [
        'nome' => 'Empresa Teste',
        'user_id' => $usuarioA->id,
    ]);
}
public function test_usuario_nao_pode_atualizar_cliente_de_outro_usuario(): void
{
    // Criamos dois usuários.
    $usuarioA = User::factory()->create();
    $usuarioB = User::factory()->create();

    // Criamos um cliente pertencente ao usuário B.
    $cliente = $usuarioB->clientes()->create([
        'nome' => 'Empresa Original',
        'endereco' => 'Rua das Flores',
        'numero' => '100',
        'bairro' => 'Centro',
        'cidade' => 'Americana',
        'estado' => 'SP',
        'tel_celular' => '19999999999',
    ]);

    // Autenticamos o usuário A.
    Sanctum::actingAs($usuarioA);

    // Tentamos modificar o cliente do usuário B.
    $response = $this->putJson("/api/clientes/{$cliente->id}", [
        'nome' => 'Empresa Alterada',
    ]);

    // O acesso deve ser negado.
    $response->assertNotFound();

    // O nome original deve permanecer no banco.
    $this->assertDatabaseHas('clientes', [
        'id' => $cliente->id,
        'nome' => 'Empresa Original',
        'user_id' => $usuarioB->id,
    ]);
}
public function test_usuario_nao_pode_excluir_cliente_de_outro_usuario(): void
{
    // Criamos dois usuários diferentes.
    $usuarioA = User::factory()->create();
    $usuarioB = User::factory()->create();

    // Criamos um cliente pertencente ao usuário B.
    $cliente = $usuarioB->clientes()->create([
        'nome' => 'Empresa Protegida',
        'endereco' => 'Rua das Flores',
        'numero' => '100',
        'bairro' => 'Centro',
        'cidade' => 'Americana',
        'estado' => 'SP',
        'tel_celular' => '19999999999',
    ]);

    // Autenticamos o usuário A.
    Sanctum::actingAs($usuarioA);

    // Tentamos excluir o cliente do usuário B.
    $response = $this->deleteJson("/api/clientes/{$cliente->id}");

    // O sistema deve impedir a exclusão.
    $response->assertNotFound();

    // Confirmamos que o cliente continua no banco.
    $this->assertDatabaseHas('clientes', [
        'id' => $cliente->id,
        'nome' => 'Empresa Protegida',
        'user_id' => $usuarioB->id,
    ]);
}
}