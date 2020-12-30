<?php

namespace CompleteSolar\ApiClientsTests\Unit\Models;

use BadMethodCallException;
use CompleteSolar\ApiClients\Models\ApiClient;
use CompleteSolar\ApiClientsTests\TestCase;
use Illuminate\Database\Eloquent\Collection;
use ReflectionClass;

class ApiClientTest extends TestCase
{
    public function testCreatingEvent()
    {
        $apiClient = $this->createApiClient();
        $this->assertNotNull($apiClient->api_key, 'API must be auto-set.');
    }

    /**
     * @covers \CompleteSolar\ApiClients\Models\ApiClient::getHeaderKey()
     */
    public function testGetHeaderKey()
    {
        $this->assertEquals('x-api-key', ApiClient::getHeaderKey());
    }

    /**
     * @covers \CompleteSolar\ApiClients\Models\ApiClient::setApiKey()
     */
    public function testSetApiKey()
    {
        $apiClient = new class extends ApiClient {
            public $newQueryCalledTimes = 0;

            // We need to ensure that we check in DB if api_key is unique
            public function newQuery()
            {
                $this->newQueryCalledTimes++;

                return new class ($this->newQueryCalledTimes) {

                    public $iteration;

                    public function __construct($iteration)
                    {
                        $this->iteration = 3 - $iteration;
                    }

                    public function where()
                    {
                        return $this;
                    }

                    public function count()
                    {
                        return $this->iteration;
                    }
                };
            }
        };

        /** @var ApiClient $apiClient */
        $this->assertNull($apiClient->api_key);
        $apiClient->setApiKey();
        $this->assertNotNull($apiClient->api_key);
        $this->assertEquals(3, $apiClient->newQueryCalledTimes, 'Api key must be checked 3 times.');
    }

    /**
     * @covers \CompleteSolar\ApiClients\Models\ApiClient::scopes()
     */
    public function testScopes()
    {
        $apiClient = $this->createApiClient();
        $this->assertCount(0, $apiClient->scopes);

        $scope = $this->createScope();
        $apiClient->scopes()->save($scope);
        $this->assertEquals(1, $apiClient->scopes()->count());
    }

    /**
     * @covers \CompleteSolar\ApiClients\Models\ApiClient::hasScope()
     */
    public function testHasScope()
    {
        $apiClient = $this->createApiClient();

        $relatedScope = $this->createScope();
        $notRelatedScope = $this->createScope();
        $apiClient->scopes()->save($relatedScope);

        $this->assertTrue($apiClient->hasScope($relatedScope->name), 'We should be able to check scope by name.');
        $this->assertTrue($apiClient->hasScope($relatedScope), 'We should be able to check scope by scope model.');

        $this->assertFalse($apiClient->hasScope($notRelatedScope->name), 'We should be able to check scope by name.');
        $this->assertFalse($apiClient->hasScope($notRelatedScope), 'We should be able to check scope by scope model.');
    }

    public function testIsActiveCastsToBool()
    {
        $apiClient = new ApiClient(['is_active' => '1']);
        $this->assertSame(true, $apiClient->is_active);
    }

    /**
     * @covers \CompleteSolar\ApiClients\Models\ApiClient::getAuthIdentifierName()
     */
    public function testGetAuthIdentifierName()
    {
        $apiClient = new ApiClient();
        $this->assertEquals('api_key', $apiClient->getAuthIdentifierName());
    }

    /**
     * @covers \CompleteSolar\ApiClients\Models\ApiClient::getAuthIdentifier()
     */
    public function testGetAuthIdentifier()
    {
        $apiClient = $this->createApiClient();
        $this->assertSame($apiClient->api_key, $apiClient->getAuthIdentifier());
    }

    /**
     * @dataProvider getNotImplementedMethods
     * @param string $method
     * @covers       \CompleteSolar\ApiClients\Models\ApiClient::notImplemented()
     * @covers       \CompleteSolar\ApiClients\Models\ApiClient::getAuthPassword()
     * @covers       \CompleteSolar\ApiClients\Models\ApiClient::getRememberToken()
     * @covers       \CompleteSolar\ApiClients\Models\ApiClient::setRememberToken()
     * @covers       \CompleteSolar\ApiClients\Models\ApiClient::getRememberTokenName()
     */
    public function testNotImplementedMethods(string $methodName)
    {
        $apiClient = new ApiClient();
        $reflection = new ReflectionClass(ApiClient::class);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Not applicable for this model');

        $method->invoke($apiClient, 'optional-param');
    }

    public function getNotImplementedMethods()
    {
        return [
            ['notImplemented'],
            ['getAuthPassword'],
            ['getRememberToken'],
            ['setRememberToken'],
            ['getRememberTokenName'],
        ];
    }

    /**
     * @covers \CompleteSolar\ApiClients\Models\ApiClient::findByScope()
     */
    public function testFindByScope()
    {
        $apiClient = $this->createApiClient();
        $scopesWithApiClients = $this->createScope();
        $apiClient->scopes()->save($scopesWithApiClients);

        $apiClients = ApiClient::findByScope($scopesWithApiClients->name);

        $this->assertInstanceOf(Collection::class, $apiClients);
        $this->assertCount(1, $apiClients);
        $this->assertEquals($apiClient->id, $apiClients->first()->id);
    }

    /**
     * @covers \CompleteSolar\ApiClients\Models\ApiClient::findByScope()
     */
    public function testFindByScopeWithoutApiClientsAttached()
    {
        $apiClient = $this->createApiClient();
        $scopeWithoutApiClients = $this->createScope();

        $apiClients = ApiClient::findByScope($scopeWithoutApiClients->name);

        $this->assertInstanceOf(Collection::class, $apiClients);
        $this->assertCount(0, $apiClients);
    }
}