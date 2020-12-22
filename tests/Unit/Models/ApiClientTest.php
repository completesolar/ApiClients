<?php

namespace CompleteSolar\ApiClients\Tests\Unit\Models;

use CompleteSolar\ApiClients\Models\ApiClient;
use CompleteSolar\ApiClients\Tests\TestCase;

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
        /** @var ApiClient $apiClient */
        $apiClient = $this->createApiClient();

        $relatedScope = $this->createScope();
        $notRelatedScope = $this->createScope();
        $apiClient->scopes()->save($relatedScope);

        $this->assertTrue($apiClient->hasScope($relatedScope->name), 'We should be able to check scope by name.');
        $this->assertTrue($apiClient->hasScope($relatedScope), 'We should be able to check scope by scope model.');

        $this->assertFalse($apiClient->hasScope($notRelatedScope->name), 'We should be able to check scope by name.');
        $this->assertFalse($apiClient->hasScope($notRelatedScope), 'We should be able to check scope by scope model.');
    }
}