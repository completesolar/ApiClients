<?php

namespace CompleteSolar\ApiClients\Models;

use Illuminate\Database\Eloquent\Model;

class ApiClientScope extends Model
{
    protected $fillable = ['name','description'];

    public function getRouteKeyName()
    {
        return 'name';
    }

    public function scopeFindByNames($query, array $names = [])
    {
        return $query->whereIn('name', $names)->get();
    }

    public function scopeName($query, string $name)
    {
        return $query->where('name', $name);
    }
}
