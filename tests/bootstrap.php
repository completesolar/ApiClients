<?php

use CompleteSolar\ApiClients\Tests\TestApplication;
use CompleteSolar\ApiClients\Tests\TestEventDispatcher;
use Illuminate\Database\ConnectionResolver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Support\Facades\Facade;

require_once __DIR__ . '/../vendor/autoload.php';
echo 'test1';
// Create app
$testApplication = new TestApplication();

// Create testing DB connection
$pdo = new PDO(
    'sqlite::memory:',
    null,
    null,
    array(PDO::ATTR_PERSISTENT => true)
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$connection = new SQLiteConnection($pdo, 'main');
$connectionResolver = new ConnectionResolver(['sqlite' => $connection]);
$connectionResolver->setDefaultConnection('sqlite');

$testApplication['db'] = $connectionResolver;

Facade::setFacadeApplication($testApplication);
Model::setConnectionResolver($connectionResolver);
Model::setEventDispatcher(new TestEventDispatcher());
