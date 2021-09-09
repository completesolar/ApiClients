<?php

namespace CompleteSolar\ApiClients\Connectors;

use CompleteSolar\ApiClients\Events\ApiClientNotifiableEvent;
use CompleteSolar\ApiClients\Models\ApiClient;
use Exception;
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
     * @return ResponseInterface|null
     * @throws Exception
     */
    public static function notifyAboutEvent(ApiClientNotifiableEvent $event): ?ResponseInterface
    {
        $apiClients = $event->getApiClients();

        foreach ($apiClients as $apiClient){

            if ($apiClient && $apiClient->webhook_url) {
                $connector = new self($apiClient);

                // TODO need added mechanism to retry failures from webhook

                try {
                    $connector->callWebhook($event->getWebhookData());
                } catch (Exception $e) {
                    $exception = $e;
                }
            }
        }

        if (isset($exception)) {
            throw new Exception($exception->getMessage());
        }

        return null;
    }

    /**
     * Call Webhook
     *
     * @param array $data
     * @return ResponseInterface
     */
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