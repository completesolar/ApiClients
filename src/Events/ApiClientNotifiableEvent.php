<?php

namespace CompleteSolar\ApiClients\Events;

use CompleteSolar\ApiClients\Models\ApiClient;

interface ApiClientNotifiableEvent
{
    /**
     * Get Api Client
     *
     * @return mixed
     */
    public function getApiClient(): ApiClient;

    /**
     * The data we send to webhook
     *
     * @return array
     */
    public function getWebhookData(): array;
}
