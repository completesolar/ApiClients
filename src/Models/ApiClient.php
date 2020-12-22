<?php

namespace CompleteSolar\ApiClients\Models;

use BadMethodCallException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/**
 * Class ApiClient
 *
 * @property int $id
 * @property string $name
 * @property string $api_key
 * @property string $webhook_url
 * @property bool $is_active
 * @property string $created_at
 * @property string $updated_at
 * @property ApiClientScope[] $scopes
 */
class ApiClient extends Model implements Authenticatable
{
    protected $fillable = [
        'name',
        'webhook_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'bool'
    ];

    protected static function boot()
    {
        parent::boot();

        self::creating(function(ApiClient $model) {
            $model->setApiKey();
        });
    }

    /**
     * Returns long living access key. Used for server to server communication,
     * grants access to available resources connected to an API client.
     *
     * @return string
     */
    public static function getHeaderKey()
    {
        return 'x-api-key';
    }

    /**
     * @return $this
     */
    public function setApiKey()
    {
        do {
            $apiKey = Str::random(32); // We need to generate an unique API key.
        } while (self::where('api_key', $apiKey)->count() > 0);

        $this->api_key = $apiKey;
        return $this;
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'api_key';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->api_key;
    }

    protected function notImplemented()
    {
        throw new BadMethodCallException('Not applicable for this model');
    }

    public function getAuthPassword()
    {
        $this->notImplemented();
    }

    public function getRememberToken()
    {
        $this->notImplemented();
    }

    public function setRememberToken($value)
    {
        $this->notImplemented();
    }

    public function getRememberTokenName()
    {
        $this->notImplemented();
    }

    /**
     * Returns scopes the client has access to.
     *
     * @return BelongsToMany
     */
    public function scopes(): BelongsToMany
    {
        return $this->belongsToMany(ApiClientScope::class)->withTimestamps();
    }

    /**
     * Check if client has a scope.
     *
     * @param string|ApiClientScope $scope
     * @return bool
     */
    public function hasScope($scope): bool
    {
        if ($scope instanceof ApiClientScope) {
            $scope = $scope->name;
        }
        return $this->scopes()->where('name', $scope)->exists();
    }
}