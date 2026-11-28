<?php

namespace Tests\Feature;

use App\Http\Middleware\RoleMiddleware;
use App\Models\CatalogoRisco;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CatalogoRiscoValidationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Autentica um administrador.
     */
    private function autenticarAdmin(): User
    {
        $user = User::factory()->create();

        $user->role = User::ROLE_ADMIN;
        $user->save();

        Sanctum::actingAs($user);

        return $user;
    }

    /**
     * Autentica um aluno.
     */
    private function autenticarAluno(): User
    {
        $user = User::factory()->create();

        $user->role = User::ROLE_ALUNO;
        $user->save();

        Sanctum::actingAs($user);

        return $user;
    }

    /**
     * Dados ficticios para os testes.
     */
    private function dadosCatalogo(): array
    {
        return [
            'categoria' => 'Categoria de Teste',
            'descricao_etapa' => 'Descricao ficticia',
            'riscos_envolvidos' => 'Texto de teste',
            'epis_medidas_controle' => 'Texto de teste',
        ];
    }

    /**
     * Cria um item para testes.
     */
    private function criarItem(): CatalogoRisco
    {
        return CatalogoRisco::create(
            $this->dadosCatalogo()
        );
    }

    /**
     * TESTE 01
     * Permite o cadastro de um item valido.
     */
    public function test_cadastro_valido(): void
    {
        $this->autenticarAdmin();

        $dados = $this->dadosCatalogo();

        $this->postJson('/api/catalogo-riscos', $dados)
            ->assertCreated()
            ->assertJsonPath(
                'categoria',
                $dados['categoria']
            )
            ->assertJsonPath(
                'descricao_etapa',
                $dados['descricao_etapa']
            );

        $this->assertDatabaseHas(
            'catalogo_riscos',
            $dados
        );
    }

    /**
     * TESTE 02
     * Exige todos os campos obrigatorios.
     */
    public function test_rejeita_campos_obrigatorios_ausentes(): void
    {
        $this->autenticarAdmin();

        $this->postJson('/api/catalogo-riscos', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'categoria',
                'descricao_etapa',
                'riscos_envolvidos',
                'epis_medidas_controle',
            ]);

        $this->assertDatabaseCount(
            'catalogo_riscos',
            0
        );
    }

    /**
     * TESTE 03
     * Rejeita campos acima dos limites definidos.
     */
    public function test_rejeita_textos_acima_do_limite(): void
    {
        $this->autenticarAdmin();

        $dados = [
            'categoria' => str_repeat('a', 256),
            'descricao_etapa' => str_repeat('a', 256),
            'riscos_envolvidos' => str_repeat('a', 3001),
            'epis_medidas_controle' => str_repeat('a', 5001),
        ];

        $this->postJson('/api/catalogo-riscos', $dados)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'categoria',
                'descricao_etapa',
                'riscos_envolvidos',
                'epis_medidas_controle',
            ]);

        $this->assertDatabaseCount(
            'catalogo_riscos',
            0
        );
    }

    /**
     * TESTE 04
     * Aceita textos no limite maximo permitido.
     */
    public function test_aceita_textos_no_limite(): void
    {
        $this->autenticarAdmin();

        $dados = [
            'categoria' => str_repeat('a', 255),
            'descricao_etapa' => str_repeat('a', 255),
            'riscos_envolvidos' => str_repeat('a', 3000),
            'epis_medidas_controle' => str_repeat('a', 5000),
        ];

        $this->postJson('/api/catalogo-riscos', $dados)
            ->assertCreated();

        $this->assertDatabaseHas(
            'catalogo_riscos',
            $dados
        );
    }

    /**
     * TESTE 05
     * Rejeita caracteres nulos nos campos.
     */
    public function test_rejeita_caracteres_nulos(): void
    {
        $this->autenticarAdmin();

        $dados = [
            'categoria' => "Categoria\0Teste",
            'descricao_etapa' => "Descricao\0Teste",
            'riscos_envolvidos' => "Riscos\0Teste",
            'epis_medidas_controle' => "Medidas\0Teste",
        ];

        $this->postJson('/api/catalogo-riscos', $dados)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'categoria',
                'descricao_etapa',
                'riscos_envolvidos',
                'epis_medidas_controle',
            ]);

        $this->assertDatabaseCount(
            'catalogo_riscos',
            0
        );
    }

    /**
     * TESTE 06
     * Rejeita tipos de dados incorretos.
     */
    public function test_rejeita_tipos_invalidos(): void
    {
        $this->autenticarAdmin();

        $dados = [
            'categoria' => ['Categoria'],
            'descricao_etapa' => 123,
            'riscos_envolvidos' => true,
            'epis_medidas_controle' => ['Texto'],
        ];

        $this->postJson('/api/catalogo-riscos', $dados)
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'categoria',
                'descricao_etapa',
                'riscos_envolvidos',
                'epis_medidas_controle',
            ]);

        $this->assertDatabaseCount(
            'catalogo_riscos',
            0
        );
    }

    /**
     * TESTE 07
     * Permite atualizar somente um campo.
     */
    public function test_atualizacao_parcial_preserva_dados(): void
    {
        $this->autenticarAdmin();

        $item = $this->criarItem();

        $this->patchJson(
            "/api/catalogo-riscos/{$item->id}",
            [
                'categoria' => 'Categoria Atualizada',
            ]
        )
            ->assertOk()
            ->assertJsonPath(
                'categoria',
                'Categoria Atualizada'
            )
            ->assertJsonPath(
                'descricao_etapa',
                $item->descricao_etapa
            )
            ->assertJsonPath(
                'riscos_envolvidos',
                $item->riscos_envolvidos
            )
            ->assertJsonPath(
                'epis_medidas_controle',
                $item->epis_medidas_controle
            );

        $this->assertDatabaseHas('catalogo_riscos', [
            'id' => $item->id,
            'categoria' => 'Categoria Atualizada',
        ]);
    }

    /**
     * TESTE 08
     * Preserva os dados quando a validacao falha.
     */
    public function test_atualizacao_invalida_preserva_dados(): void
    {
        $this->autenticarAdmin();

        $item = $this->criarItem();

        $this->patchJson(
            "/api/catalogo-riscos/{$item->id}",
            [
                'categoria' => 'Categoria Alterada',
                'riscos_envolvidos' => str_repeat('a', 3001),
            ]
        )
            ->assertUnprocessable()
            ->assertJsonValidationErrors(
                'riscos_envolvidos'
            );

        $this->assertDatabaseHas('catalogo_riscos', [
            'id' => $item->id,
            'categoria' => $item->categoria,
            'riscos_envolvidos' => $item->riscos_envolvidos,
        ]);
    }

    /**
     * TESTE 09
     * Impede alteracoes em campos nao autorizados.
     */
    public function test_ignora_campos_nao_permitidos(): void
    {
        $this->autenticarAdmin();

        $item = $this->criarItem();

        $this->patchJson(
            "/api/catalogo-riscos/{$item->id}",
            [
                'id' => 999999,
                'categoria' => 'Categoria Atualizada',
                'created_at' => '2000-01-01 00:00:00',
            ]
        )
            ->assertOk()
            ->assertJsonPath('id', $item->id);

        $this->assertDatabaseHas('catalogo_riscos', [
            'id' => $item->id,
            'categoria' => 'Categoria Atualizada',
            'created_at' => $item->getRawOriginal('created_at'),
        ]);

        $this->assertDatabaseMissing('catalogo_riscos', [
            'id' => 999999,
        ]);
    }

    /**
     * TESTE 10
     * Rejeita identificadores invalidos nas rotas.
     */
    public function test_rejeita_identificadores_invalidos(): void
    {
        $this->autenticarAdmin();

        $this->getJson('/api/catalogo-riscos/invalido')
            ->assertNotFound();

        $this->patchJson('/api/catalogo-riscos/invalido', [
            'categoria' => 'Categoria de Teste',
        ])->assertNotFound();

        $this->deleteJson('/api/catalogo-riscos/invalido')
            ->assertNotFound();
    }

    /**
     * TESTE 11
     * Retorna 404 para itens inexistentes.
     */
    public function test_retorna_404_para_item_inexistente(): void
    {
        $this->autenticarAdmin();

        $this->getJson('/api/catalogo-riscos/999999')
            ->assertNotFound();

        $this->patchJson('/api/catalogo-riscos/999999', [
            'categoria' => '',
        ])->assertNotFound();

        $this->deleteJson('/api/catalogo-riscos/999999')
            ->assertNotFound();
    }

    /**
     * TESTE 12
     * Verifica a camada adicional de autorizacao.
     */
    public function test_autorizacao_adicional_bloqueia_aluno(): void
    {
        $this->autenticarAluno();

        $item = $this->criarItem();

        // Desativa somente o RoleMiddleware neste teste
        // para verificar a segunda camada de autorizacao.
        $this->withoutMiddleware(RoleMiddleware::class);

        $this->postJson(
            '/api/catalogo-riscos',
            $this->dadosCatalogo()
        )->assertForbidden();

        $this->patchJson(
            "/api/catalogo-riscos/{$item->id}",
            [
                'categoria' => 'Alteracao nao autorizada',
            ]
        )->assertForbidden();

        $this->deleteJson(
            "/api/catalogo-riscos/{$item->id}"
        )->assertForbidden();

        $this->assertDatabaseHas('catalogo_riscos', [
            'id' => $item->id,
            'categoria' => $item->categoria,
        ]);

        $this->assertDatabaseCount(
            'catalogo_riscos',
            1
        );
    }
}