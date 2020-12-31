<?php

namespace CompleteSolar\ApiClients\Connectors;

use CompleteSolar\ApiClients\Events\ApiClientNotifiableEvent;
use CompleteSolar\ApiClients\Models\ApiClient;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

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

    /**
     * Headers
     *
     * @return array
     */
    protected function headers(): array
    {
        return [
            ApiClient::getHeaderKey() => $this->apiClient->api_key,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    /**
     * Notify About Event
     *
     * @param  ApiClientNotifiableEvent  $event
     */
    public static function notifyAboutEvent(ApiClientNotifiableEvent $event): void
    {
        $apiClient = $event->getApiClient();
        $connector = new self($apiClient);
        $connector->callWebhook($event->getWebhookData());
    }

    /**
     * Call Webhook
     *
     * @param  array  $data
     */
    public function callWebhook(array $data): void
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
    }
}