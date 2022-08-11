<?php

namespace CompleteSolar\ApiClients\Connectors;

use CompleteSolar\ApiClients\Events\ApiClientNotifiableEvent;
use CompleteSolar\ApiClients\Models\ApiClient;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

use function json_encode;

class ApiClientConnector
{
    /**
     * @var ApiClient
     */
    protected $apiClient;

    /**
     * @var Client
     */
    protected $client;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
        $this->client = new Client();
    }

    protected function headers(): array
    {
        return [
            ApiClient::getHeaderKey() => $this->apiClient->api_key,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    public static function notifyAboutEvent(ApiClientNotifiableEvent $event): ?ResponseInterface
    {
        $apiClient = $event->getApiClient();

        if ($apiClient && $apiClient->webhook_url) {
            $connector = new self($apiClient);

            return $connector->callWebhook($event->getWebhookData());
        }

        return null;
    }

    public function callWebhook(array $data): ResponseInterface
    {
        Log::debug('Calling api client webhook', [
            'clientId' => $this->apiClient->id,
            'webhook' => $this->apiClient->webhook_url,
            'data' => $data,
        ]);

        $body = json_encode($data);
        $response = $this->client->post(
            $this->apiClient->webhook_url,
            [
                'body' => $body,
                'headers' => $this->headers(),
            ]
        );

        if ($response->getStatusCode() >= 400) {
            Log::debug('webhook post failed');
        }

        return $response;
    }
}