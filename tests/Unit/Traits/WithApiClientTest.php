<?php

namespace CompleteSolar\ApiClientsTests\Unit\Traits;

use CompleteSolar\ApiClientsTests\TestCase;
use CompleteSolar\ApiClients\Traits\WithApiClient;
use Illuminate\Database\Eloquent\Model;

class WithApiClientTest extends TestCase
{
    protected function getModelWithTrait()
    {
        return new class extends Model {
            use WithApiClient;
        };
    }

    /**
     * @covers \CompleteSolar\ApiClients\Traits\WithApiClient::getApiClientKey()
     */
    public function testGetApiClientKey()
    {
        $this->assertEquals('api_client_id', $this->getModelWithTrait()->getApiClientKey());
    }

    public function testApiClient()
    {
        $class = $this->getModelWithTrait();
        $apiClient = $this->createApiClient();
        $class->apiClient()->associate($apiClient);

        $this->assertEquals($apiClient->id, $class->getAttribute('api_client_id'));
    }
}