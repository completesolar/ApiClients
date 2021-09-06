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
     * @param ApiClientNotifiableEvent $event
     * @return ResponseInterface|null
     */
    public static function notifyAboutEvent(ApiClientNotifiableEvent $event): ?ResponseInterface
    {
        $apiClients = $event->getApiClients();

        foreach ($apiClients as $apiClient){

            if ($apiClient && $apiClient->webhook_url) {
                $connector = new self($apiClient);

                // TODO need added mechanism to retry failures from webhook

                $connector->callWebhook($event->getWebhookData());
            }
        }

        return null;
    }

    /**
     * Call Webhook
     *
     * @param  array  $data
     * @return void
     */
    public function callWebhook(array $data): void
    {
        Log::debug('Calling api client webhook', [
            'clientId' => $this->apiClient->id,
            'webhook' => $this->apiClient->webhook_url,
            'data' => $data,
        ]);

        $body = json_encode($data);

        try{
            $this->client->post(
                $this->apiClient->webhook_url,
                [
                    'body' => $body,
                    'headers' => $this->headers(),
                ]
            );
        }catch (\Exception $exception){
            Log::debug('webhook post failed',['message' => $exception->getMessage()]);
        }
    }
}