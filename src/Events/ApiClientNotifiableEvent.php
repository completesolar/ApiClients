<?php

namespace CompleteSolar\ApiClients\Events;

use Illuminate\Support\Collection;

interface ApiClientNotifiableEvent
{
    /**
     * Get Api Clients
     *
     * @return Collection
     */
    public function getApiClients(): Collection;

    /**
     * The data we send to webhook
     *
     * @return array
     */
    public function getWebhookData(): array;
}
