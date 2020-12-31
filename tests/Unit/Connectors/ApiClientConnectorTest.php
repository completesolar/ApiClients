<?php
/**
 * Created by: Yaroslav Pohil
 * Date and time: 31/12/2020 10:55
 */

namespace CompleteSolar\ApiClientsTests\Unit\Connectors;

use CompleteSolar\ApiClients\Connectors\ApiClientConnector;
use CompleteSolar\ApiClientsTests\FakeClient;
use CompleteSolar\ApiClientsTests\TestCase;
use GuzzleHttp\Client;

class ApiClientConnectorTest extends TestCase
{
    public function testConstructor()
    {
        $apiClient = $this->createApiClient();

        $connector = new ApiClientConnector($apiClient);

        $this->assertSame($apiClient, $this->getObjectProperty($connector, 'apiClient'));
        $this->assertInstanceOf(Client::class, $this->getObjectProperty($connector, 'client'));
    }

    public function testHeaders()
    {
        $apiClient = $this->createApiClient();
        $connector = new ApiClientConnector($apiClient);

        $headers = $this->invokeObjectMethod($connector, 'headers');

        $this->assertEquals([
            'x-api-key' => $apiClient->api_key,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ], $headers);
    }

    public function testCallWebhook()
    {
        $apiClient = $this->createApiClient();
        $connector = new ApiClientConnector($apiClient);
        $this->setObjectProperty($connector, 'client', new FakeClient());

        $dataToSend = ['foo' => 'bar'];
        $connector->callWebhook($dataToSend);

        /** @var FakeClient $client */
        $client = $this->getObjectProperty($connector, 'client');
        $history = $client->getPostHistory();
        $this->assertCount(1, $history);
        $this->assertEquals('http://test.url/webhook', $history[0]['url']);
        $this->assertEquals([
            'headers' => $this->invokeObjectMethod($connector, 'headers'),
            'body' => json_encode($dataToSend)
        ], $history[0]['options']);
    }
}