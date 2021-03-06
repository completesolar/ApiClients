<?php

namespace CompleteSolar\ApiClients\Models;

use BadMethodCallException;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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
 * @property Carbon $created_at
 * @property Carbon $updated_at
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

    protected static function boot(): void
    {
        parent::boot();

        self::creating(function(ApiClient $model) {
            $model->setApiKey();
        });
    }

    public static function getHeaderKey(): string
    {
        return 'x-api-key';
    }

    public function setApiKey(): self
    {
        do {
            $apiKey = Str::random(32); // We need to generate an unique API key.
        } while (self::where('api_key', $apiKey)->count() > 0);

        $this->api_key = $apiKey;
        return $this;
    }

    public function getAuthIdentifierName(): string
    {
        return 'api_key';
    }

    public function getAuthIdentifier(): string
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

    public function scopes(): BelongsToMany
    {
        return $this->belongsToMany(ApiClientScope::class)->withTimestamps();
    }

    /**
     * Check if client has a scope.
     *
     * @param string|ApiClientScope $scope
     */
    public function hasScope($scope): bool
    {
        if ($scope instanceof ApiClientScope) {
            $scope = $scope->name;
        }

        return $this->scopes()->where('name', $scope)->exists();
    }

    public static function findByScope(string $name): Collection
    {
        return self::whereHas(
            'scopes',
            function (Builder $query) use ($name) {
                $query->where('name', $name);
            }
        )->get();
    }

    public static function findByApiKey($apiKey): ApiClient
    {
        return static::where('api_key', $apiKey)->firstOrFail();
    }
}
