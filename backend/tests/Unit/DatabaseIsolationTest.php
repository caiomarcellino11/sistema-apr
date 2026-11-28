<?php

namespace Tests\Unit;

use Tests\TestCase;

class DatabaseIsolationTest extends TestCase
{
    public function test_banco_de_testes_esta_isolado(): void
    {
        $this->assertSame(
            'testing',
            $this->app->environment()
        );

        $this->assertSame(
            'sqlite',
            config('database.default')
        );

        $this->assertSame(
            ':memory:',
            config('database.connections.sqlite.database')
        );

        $this->assertEmpty(
            config('database.connections.sqlite.url')
        );
    }
}