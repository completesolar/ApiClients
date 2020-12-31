<?php

namespace CompleteSolar\ApiClientsTests;

use CompleteSolar\ApiClients\Models\ApiClient;
use CompleteSolar\ApiClients\Models\ApiClientScope;
use CreateApiClientTables;
use ReflectionClass;
use ReflectionException;

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

    /**
     * Returns protected property value of the object.
     *
     * @param object $object
     * @param string $propertyName
     * @return mixed
     * @throws ReflectionException
     */
    protected function getObjectProperty($object, string $propertyName)
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        return $property->getValue($object);
    }

    /**
     * Updates protected property value of the object.
     *
     * @param object $object
     * @param string $propertyName
     * @param mixed $newValue
     * @return self
     * @throws ReflectionException
     */
    protected function setObjectProperty($object, string $propertyName, $newValue)
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $newValue);
        return $this;
    }

    /**
     * Invokes protected method of the object.
     *
     * @param object $object
     * @param string $methodName
     * @param array $methodParams
     * @return mixed
     * @throws ReflectionException
     */
    protected function invokeObjectMethod($object, string $methodName, array $methodParams = [])
    {
        $reflection = new ReflectionClass($object);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $methodParams);
    }
}