# ApiClients
Helps to interact with the system using API login keys.

## Requirements 
- PHP 7.1 or newer;
- Laravel 5.5;

## Installation
You need to add this repo under `repositories` inside your `composer.json` and call
```
composer require completesolar/api-clients 1.*
```

After you installed it the service provider 
[ApiClientServiceProvider](https://github.com/completesolar/ApiClients/blob/master/src/ApiClientServiceProvider.php) will be loaded automatically.

To copy pre-defined migration and routes you need to call
```
php artisan vendor:publish
```
And enter the number that represents ApiClientServiceProvider from the list you'll see.
As a result, you'll see files `migrations/{current_timestamp}_create_api_client_tables.php` and `routes/api_clients_routes.php`.
When the migration is important to make the core logic work, using routes file is optional.

## Available functionaly

### Models

The core files are [ApiClient](https://github.com/completesolar/ApiClients/blob/master/src/Models/ApiClient.php) and 
[ApiClientScope](https://github.com/completesolar/ApiClients/blob/master/src/Models/ApiClientScope.php). For each client a key is autogenerated, 
so you can use it for authentication or other purposes.
Scopes are abilities your client can have. By default, clients can have as many scopes as possible, but one scope can be assigned only once.

Additionally trait [WithApiClient](https://github.com/completesolar/ApiClients/blob/master/src/Traits/WithApiClient.php) is available, 
so you can use it for models you want to be connected with a client. Relation is one to one.

### Event

The module has an interface [ApiClientNotifiableEvent](https://github.com/completesolar/ApiClients/blob/master/src/Events/ApiClientNotifiableEvent.php) 
you can implement for every event you want your client to be notified about.
The easiest way to use it is to add a listener with
```
public function handle(ApiClientNotifiableEvent $event) 
{
    ApiClientConnector::notifyAboutEvent($event);
}
```
This listener will send a POST notification to a url specified inside a client.

### Connector
The module has a connector class [ApiClientConnector](https://github.com/completesolar/ApiClients/blob/master/src/Connectors/ApiClientConnector.php), 
that you can you for notifying your clients about your events.