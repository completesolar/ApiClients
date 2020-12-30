<?php

namespace CompleteSolar\ApiClientsTests;

use CompleteSolar\ApiClients\Models\ApiClient;
use CompleteSolar\ApiClients\Models\ApiClientScope;
use CreateApiClientTables;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected static $calledMigrations = [];

    protected function setUp()
    {
        parent::setUp();
        $this->runMigration(CreateApiClientTables::class);
    }

    protected function runMigration($migrationClass, $method = 'up')
    {
        if (in_array($migrationClass, self::$calledMigrations)) {
            return;
        }

        self::$calledMigrations[] = $migrationClass;
        $migration = new $migrationClass();
        if (method_exists($migration, $method)) {
            $migration->{$method}();
        }
    }

    protected function createScope(string $name = null): ApiClientScope
    {
        $name = $name ?? uniqid('test_scope_');
        return ApiClientScope::create(['name' => $name, 'description' => 'Test scope.']);
    }

    protected function createApiClient(string $name = null): ApiClient
    {
        return ApiClient::create([
            'name' => $name ?? uniqid('api_client'),
            'webhook_url' => 'http://test.url/webhook',
        ]);
    }
}