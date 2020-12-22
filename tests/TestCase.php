<?php

namespace CompleteSolar\ApiClients\Tests;

use CompleteSolar\ApiClients\Models\ApiClient;
use CompleteSolar\ApiClients\Models\ApiClientScope;
use CreateApiClientTables;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\ConnectionResolver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Support\Facades\Facade;
use PDO;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Application
     */
    protected $app;
    protected static $booted = false;
    protected function setUp()
    {
        parent::setUp();

        // Set application
        $this->app = new TestApplication();
        Facade::setFacadeApplication($this->app);

        $this->setupDatabase();

        // Set model dependencies
        Model::setConnectionResolver($this->app['db']);

        if (!self::$booted) {
            Model::setEventDispatcher(new TestEventDispatcher());
            self::$booted = true;
        }
    }

    protected function setupDatabase()
    {
        $dbName = 'main';
        $pdo = new PDO(
            'sqlite::memory:',
            null,
            null,
            array(PDO::ATTR_PERSISTENT => true)
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $connection = new SQLiteConnection($pdo, $dbName);
        $connectionResolver = new ConnectionResolver(['sqlite' => $connection]);
        $connectionResolver->setDefaultConnection('sqlite');

        $this->app['db'] = $connectionResolver;

        if (!self::$booted) { // In memory DB will be used for all tests
            $this->runMigration(CreateApiClientTables::class);
        }
    }

    protected function runMigration($migrationClass, $method = 'up')
    {
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