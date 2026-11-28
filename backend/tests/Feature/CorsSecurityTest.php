<?php

namespace Tests\Feature;

use Tests\TestCase;

class CorsSecurityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'cors.allowed_origins' => [
                'http://127.0.0.1:5500',
                'http://localhost:5500',
            ],
        ]);
    }

    public function test_permite_origem_local_127(): void
    {
        $response = $this->optionsJson('/api/login', [], [
            'Origin' => 'http://127.0.0.1:5500',
            'Access-Control-Request-Method' => 'POST',
            'Access-Control-Request-Headers' => 'content-type',
        ]);

        $response->assertNoContent();

        $response->assertHeader(
            'Access-Control-Allow-Origin',
            'http://127.0.0.1:5500'
        );

        $response->assertHeader(
            'Access-Control-Allow-Credentials',
            'true'
        );
    }

    public function test_permite_origem_localhost(): void
    {
        $response = $this->optionsJson('/api/login', [], [
            'Origin' => 'http://localhost:5500',
            'Access-Control-Request-Method' => 'POST',
            'Access-Control-Request-Headers' => 'content-type',
        ]);

        $response->assertNoContent();

        $response->assertHeader(
            'Access-Control-Allow-Origin',
            'http://localhost:5500'
        );
    }

    public function test_nao_autoriza_origem_desconhecida(): void
    {
        $response = $this->optionsJson('/api/login', [], [
            'Origin' => 'https://externo.example',
            'Access-Control-Request-Method' => 'POST',
        ]);

        $response->assertHeaderMissing(
            'Access-Control-Allow-Origin'
        );
    }

    public function test_nao_autoriza_porta_diferente(): void
    {
        $response = $this->optionsJson('/api/login', [], [
            'Origin' => 'http://127.0.0.1:9999',
            'Access-Control-Request-Method' => 'POST',
        ]);

        $response->assertHeaderMissing(
            'Access-Control-Allow-Origin'
        );
    }

    public function test_nao_autoriza_origem_nula(): void
    {
        $response = $this->optionsJson('/api/login', [], [
            'Origin' => 'null',
            'Access-Control-Request-Method' => 'POST',
        ]);

        $response->assertHeaderMissing(
            'Access-Control-Allow-Origin'
        );
    }

    public function test_permite_preflight_do_sanctum(): void
    {
        $response = $this->optionsJson(
            '/sanctum/csrf-cookie',
            [],
            [
                'Origin' => 'http://127.0.0.1:5500',
                'Access-Control-Request-Method' => 'GET',
                'Access-Control-Request-Headers' => 'x-requested-with',
            ]
        );

        $response->assertNoContent();

        $response->assertHeader(
            'Access-Control-Allow-Origin',
            'http://127.0.0.1:5500'
        );

        $response->assertHeader(
            'Access-Control-Allow-Credentials',
            'true'
        );
    }

    public function test_nao_autoriza_origens_com_lista_vazia(): void
    {
        config([
            'cors.allowed_origins' => [],
        ]);

        $response = $this->optionsJson('/api/login', [], [
            'Origin' => 'http://127.0.0.1:5500',
            'Access-Control-Request-Method' => 'POST',
        ]);

        $response->assertHeaderMissing(
            'Access-Control-Allow-Origin'
        );
    }

    public function test_configuracao_nao_permite_padroes_de_origem(): void
    {
        $this->assertSame(
            [],
            config('cors.allowed_origins_patterns')
        );
    }
}