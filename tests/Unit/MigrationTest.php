<?php
/**
 * Created by: Yaroslav Pohil
 * Date and time: 30/12/2020 12:11
 */

namespace CompleteSolar\ApiClientsTests\Unit;

use CompleteSolar\ApiClientsTests\TestCase;
use CreateApiClientTables;
use Illuminate\Support\Facades\Schema;

class MigrationTest extends TestCase
{
    /**
     * @covers \CreateApiClientTables::down
     */
    public function testDown()
    {
        $tableDoesNotExistError = 'Table does not exist.';
        $tableExistError = 'Table must not exist.';
        $this->assertTrue(Schema::hasTable('api_clients'), $tableDoesNotExistError);
        $this->assertTrue(Schema::hasTable('api_client_scopes'), $tableDoesNotExistError);
        $this->assertTrue(Schema::hasTable('api_client_api_client_scope'), $tableDoesNotExistError);
        $migration = new CreateApiClientTables();
        $migration->down();

        $this->assertFalse(Schema::hasTable('api_clients'), $tableExistError);
        $this->assertFalse(Schema::hasTable('api_client_scopes'), $tableExistError);
        $this->assertFalse(Schema::hasTable('api_client_api_client_scope'), $tableExistError);
    }

    /**
     * @covers \CreateApiClientTables::up
     */
    public function testUp()
    {
        $tableDoesNotExistError = 'Table does not exist.';
        $tableExistError = 'Table must not exist.';
        $this->assertFalse(Schema::hasTable('api_clients'), $tableExistError);
        $this->assertFalse(Schema::hasTable('api_client_scopes'), $tableExistError);
        $this->assertFalse(Schema::hasTable('api_client_api_client_scope'), $tableExistError);
        $migration = new CreateApiClientTables();
        $migration->up();

        $this->assertTrue(Schema::hasTable('api_clients'), $tableDoesNotExistError);
        $this->assertTrue(Schema::hasTable('api_client_scopes'), $tableDoesNotExistError);
        $this->assertTrue(Schema::hasTable('api_client_api_client_scope'), $tableDoesNotExistError);
    }
}