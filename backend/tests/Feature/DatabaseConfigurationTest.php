<?php

namespace Tests\Feature;

use Tests\TestCase;

class DatabaseConfigurationTest extends TestCase
{
    public function test_usa_sqlite_em_memoria(): void
    {
        $this->assertSame(
            'testing',
            config('app.env')
        );

        $this->assertSame(
            'sqlite',
            config('database.default')
        );

        $this->assertSame(
            ':memory:',
            config('database.connections.sqlite.database')
        );
    }
}