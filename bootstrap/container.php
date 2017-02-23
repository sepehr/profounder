<?php

use Monolog\Logger;
use Illuminate\Log\Writer;
use Illuminate\Config\Repository;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Facade;
use Illuminate\Database\ConnectionResolver;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Database\Migrations\MigrationRepositoryInterface;

$container = new Profounder\Core\Container;

# Enable using facades
Facade::setFacadeApplication($container);

# Config
$container->instance('config', $config = new Repository(require config_path('app.php')));

# Filesystem
$container->singleton('files', Filesystem::class);

# Storage
$container->bind(\Profounder\Core\StorageContract::class, \Profounder\Core\Storage::class);

# Events
$container->singleton('events', function ($container) {
    return new Dispatcher($container);
});

# Logs
$container->singleton('log', function ($container) {
    $logger = new Writer(
        new Logger($container->config->get('log.channel'))
    );

    $logger->useFiles($container->config->get('log.path'));

    return $logger;
});

# Database
$container->singleton(Capsule::class, function ($container) {
    $capsule = new Capsule($container);

    $capsule->addConnection($container->config->get('database'));
    $capsule->setEventDispatcher($container->make('events'));

    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    return $capsule;
});

$container->bind('db', function ($container) {
    return $container->make(Capsule::class);
});

$container->bind('db.connection', function ($container) {
    return $container->db->connection('default');
});

# Migrations
$container->bind(ConnectionResolver::class, function ($container) {
    $default  = 'default';
    $resolver = new ConnectionResolver([$default => $container->make('db.connection')]);

    $resolver->setDefaultConnection($default);

    return $resolver;
});

$container->bind(ConnectionResolverInterface::class, ConnectionResolver::class);

$container->bind(MigrationRepositoryInterface::class, function ($container) {
    return new DatabaseMigrationRepository(
        $container->make(ConnectionResolver::class),
        $container->config->get('migration-table')
    );
});

# Load custom container bindings
require_once 'bindings.php';

return $container;
