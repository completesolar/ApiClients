<?php

namespace CompleteSolar\ApiClients;

use Illuminate\Support\ServiceProvider;

class ApiClientServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $timestamp = date('Y_m_d_His');
        $this->publishes(
            [
                __DIR__ . '/migrations/create_api_client_tables.php' => $this->app->databasePath(
                    "migrations/{$timestamp}_create_api_client_tables.php"
                ),
            ],
            'migrations'
        );

        $this->loadRoutes();
        $this->publishRoutes();
    }

    protected function publishRoutes()
    {
        $this->publishes([
            __DIR__ . 'Http/api_clients_routes.php' => $this->app->basePath() . '/routes',
        ], 'routes');
    }

    protected function loadRoutes()
    {
        $fileName = 'api_clients_routes.php';

        if (!file_exists($this->app->basePath() . '/routes/' . $fileName)) {
            $this->loadRoutesFrom(__DIR__ . '/Http/' .  $fileName);
        }
    }
}