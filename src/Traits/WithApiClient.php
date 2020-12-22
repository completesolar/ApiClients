<?php

namespace CompleteSolar\ApiClients\Traits;

use CompleteSolar\ApiClients\Models\ApiClient;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait WithApiClient
{
    /**
     * @return BelongsTo
     */
    public function apiClient()
    {
        return $this->belongsTo(ApiClient::class, $this->getApiClientKey());
    }

    /**
     * @return string
     */
    public function getApiClientKey()
    {
        return 'api_client_id';
    }
}