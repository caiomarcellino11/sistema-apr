<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\CatalogoRisco;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CatalogoRiscoAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function dadosCatalogo(): array
    {
        return [
            'categoria' => 'Categoria de teste',
            'descricao_etapa' => 'Atividade fictícia',
            'riscos_envolvidos' => 'Riscos fictícios',
            'epis_medidas_controle' => 'Controles fictícios',
        ];
    }

    private function criarAdmin(): User
    {
        $admin = User::factory()->create();

        $admin->role = User::ROLE_ADMIN;

        $admin->save();

        return $admin;
    }

    // Usuários sem autenticação não podem acessar o catálogo.
    public function test_usuario_nao_autenticado_nao_pode_acessar_catalogo(): void
    {
        $item = CatalogoRisco::create($this->dadosCatalogo());

        $url = "/api/catalogo-riscos/{$item->id}";

        $this->getJson('/api/catalogo-riscos')
            ->assertUnauthorized();

        $this->getJson($url)
            ->assertUnauthorized();

        $this->postJson('/api/catalogo-riscos', $this->dadosCatalogo())
            ->assertUnauthorized();

        $this->putJson($url, $this->dadosCatalogo())
            ->assertUnauthorized();

        $this->deleteJson($url)
            ->assertUnauthorized();
    }

    // Alunos podem consultar os modelos.
    public function test_aluno_pode_consultar_catalogo(): void
    {
        $aluno = User::factory()->create();

        Sanctum::actingAs($aluno);

        $item = CatalogoRisco::create($this->dadosCatalogo());

        $this->getJson('/api/catalogo-riscos')
            ->assertOk()
            ->assertJsonFragment([
                'id' => $item->id,
            ]);

        $this->getJson("/api/catalogo-riscos/{$item->id}")
            ->assertOk()
            ->assertJsonPath('id', $item->id);
    }

    // Alunos não podem cadastrar modelos.
    public function test_aluno_nao_pode_criar_item_no_catalogo(): void
    {
        $aluno = User::factory()->create();

        Sanctum::actingAs($aluno);

        $this->postJson(
            '/api/catalogo-riscos',
            $this->dadosCatalogo()
        )->assertForbidden();

        $this->assertDatabaseCount('catalogo_riscos', 0);
    }

    // Alunos não podem modificar modelos.
    public function test_aluno_nao_pode_atualizar_item_do_catalogo(): void
    {
        $aluno = User::factory()->create();

        Sanctum::actingAs($aluno);

        $item = CatalogoRisco::create($this->dadosCatalogo());

        $url = "/api/catalogo-riscos/{$item->id}";

        $this->putJson($url, [
            'categoria' => 'Categoria alterada',
        ])->assertForbidden();

        $this->patchJson($url, [
            'categoria' => 'Categoria alterada',
        ])->assertForbidden();

        $this->assertDatabaseHas('catalogo_riscos', [
            'id' => $item->id,
            'categoria' => 'Categoria de teste',
        ]);
    }

    // Alunos não podem excluir modelos.
    public function test_aluno_nao_pode_excluir_item_do_catalogo(): void
    {
        $aluno = User::factory()->create();

        Sanctum::actingAs($aluno);

        $item = CatalogoRisco::create($this->dadosCatalogo());

        $this->deleteJson("/api/catalogo-riscos/{$item->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('catalogo_riscos', [
            'id' => $item->id,
        ]);
    }

    // Administradores podem cadastrar modelos.
    public function test_admin_pode_criar_item_no_catalogo(): void
    {
        Sanctum::actingAs($this->criarAdmin());

        $this->postJson(
            '/api/catalogo-riscos',
            $this->dadosCatalogo()
        )->assertCreated();

        $this->assertDatabaseHas(
            'catalogo_riscos',
            $this->dadosCatalogo()
        );
    }

    // Administradores podem atualizar modelos.
    public function test_admin_pode_atualizar_item_do_catalogo(): void
    {
        Sanctum::actingAs($this->criarAdmin());

        $item = CatalogoRisco::create($this->dadosCatalogo());

        $this->putJson("/api/catalogo-riscos/{$item->id}", [
            'categoria' => 'Categoria atualizada',
        ])->assertOk();

        $this->assertDatabaseHas('catalogo_riscos', [
            'id' => $item->id,
            'categoria' => 'Categoria atualizada',
        ]);
    }

    // Administradores podem excluir modelos.
    public function test_admin_pode_excluir_item_do_catalogo(): void
    {
        Sanctum::actingAs($this->criarAdmin());

        $item = CatalogoRisco::create($this->dadosCatalogo());

        $this->deleteJson("/api/catalogo-riscos/{$item->id}")
            ->assertOk();

        $this->assertDatabaseMissing('catalogo_riscos', [
            'id' => $item->id,
        ]);
    }

    // O cadastro público sempre cria uma conta de aluno.
    public function test_cadastro_publico_cria_usuario_com_perfil_aluno(): void
    {
        $this->postJson('/api/register', [
            'name' => 'Aluno Teste',
            'email' => 'aluno@example.com',
            'password' => 'Frase de teste 2026!',
        ])->assertCreated()
          ->assertJsonPath('user.role', User::ROLE_ALUNO);

        $this->assertDatabaseHas('users', [
            'email' => 'aluno@example.com',
            'role' => User::ROLE_ALUNO,
        ]);
    }

    // Impede a elevação de privilégios pelo cadastro público.
    public function test_usuario_nao_pode_escolher_perfil_administrativo(): void
    {
        $this->postJson('/api/register', [
            'name' => 'Usuario Teste',
            'email' => 'usuario@example.com',
            'password' => 'Frase de teste 2026!',
            'role' => 'admin',
        ])->assertUnprocessable()
          ->assertJsonValidationErrors(['role']);

        $this->assertDatabaseMissing('users', [
            'email' => 'usuario@example.com',
        ]);
    }
}