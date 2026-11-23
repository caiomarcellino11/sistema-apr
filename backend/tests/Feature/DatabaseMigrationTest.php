<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_tabelas_apr_existem(): void
    {
        $this->assertSame(
            'sqlite',
            config('database.default')
        );

        $this->assertSame(
            ':memory:',
            config('database.connections.sqlite.database')
        );

        $this->assertTrue(Schema::hasTable('users'));
        $this->assertTrue(Schema::hasTable('clientes'));
        $this->assertTrue(Schema::hasTable('aprs'));

        $this->assertTrue(
            Schema::hasColumn('aprs', 'user_id')
        );
    }
}