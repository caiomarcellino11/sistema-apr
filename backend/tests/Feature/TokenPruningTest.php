<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TokenPruningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'sanctum.expiration' => 120,
        ]);
    }

    public function test_remove_token_antigo_expirado(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('teste')->accessToken;

        $token->forceFill([
            'created_at' => now()->subHours(48),
        ])->save();

        $this->artisan('sanctum:prune-expired', [
            '--hours' => 24,
        ])->assertSuccessful();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token->id,
        ]);
    }

    public function test_preserva_token_valido(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('teste')->accessToken;

        $this->artisan('sanctum:prune-expired', [
            '--hours' => 24,
        ])->assertSuccessful();

        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $token->id,
        ]);
    }

    public function test_preserva_token_expirado_recentemente(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('teste')->accessToken;

        $token->forceFill([
            'created_at' => now()->subHours(3),
        ])->save();

        $this->artisan('sanctum:prune-expired', [
            '--hours' => 24,
        ])->assertSuccessful();

        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $token->id,
        ]);
    }

    public function test_remove_token_com_expiracao_individual_antiga(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken(
            'teste',
            ['*'],
            now()->subHours(48)
        )->accessToken;

        $this->artisan('sanctum:prune-expired', [
            '--hours' => 24,
        ])->assertSuccessful();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token->id,
        ]);
    }

    public function test_limpeza_preserva_usuarios(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('teste')->accessToken;

        $token->forceFill([
            'created_at' => now()->subHours(48),
        ])->save();

        $this->artisan('sanctum:prune-expired', [
            '--hours' => 24,
        ])->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token->id,
        ]);
    }
}