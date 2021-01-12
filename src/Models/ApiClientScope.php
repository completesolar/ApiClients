<?php

namespace CompleteSolar\ApiClients\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class ApiClientScope extends Model
{
    protected $fillable = ['name','description'];

    public function getRouteKeyName(): string
    {
        return 'name';
    }

    public static function findByNames(array $names = []): Collection
    {
        return static::whereIn('name', $names)->get();
    }
}
