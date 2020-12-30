<?php



namespace CompleteSolar\ApiClients\Connectors;


use CompleteSolar\ApiClients\Events\ApiClientNotifiableEvent;
use CompleteSolar\ApiClients\Models\ApiClient;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ApiClientConnector
{

    public const HEADER_API_KEY = 'x-api-key';

    protected $url;
    protected $apiClient;

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
            self::HEADER_API_KEY => $this->apiClient->apiKey,
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
        Log::debug('sending clientId='.$this->apiClient->id.' webhook='.$this->apiClient->webhook_url, $data);

        $body = \json_encode($data, JSON_PRETTY_PRINT);
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