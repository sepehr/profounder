<?php

require_once 'autoload.php';

use Profounder\Application;
use Illuminate\Database\Console\Migrations\ResetCommand;
use Illuminate\Database\Console\Migrations\StatusCommand;
use Illuminate\Database\Console\Migrations\InstallCommand;
use Illuminate\Database\Console\Migrations\MigrateCommand;
use Illuminate\Database\Console\Migrations\RefreshCommand;
use Illuminate\Database\Console\Migrations\RollbackCommand;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand;

$container   = require_once 'container.php';
$application = new Application($container, $container->config->get('name'), $container->config->get('version'));

// Register migration commands
$application->addCommands(array_map(
    function ($command) use ($container) {
        $command = $container->make($command);
        $command->setLaravel($container);

        return $command;
    }, [
        ResetCommand::class,
        StatusCommand::class,
        MigrateCommand::class,
        InstallCommand::class,
        RefreshCommand::class,
        RollbackCommand::class,
        MigrateMakeCommand::class,
    ]
));

// Register user commands
if ($commands = $container->config->get('commands')) {
    $application->addCommands(array_map(
        function ($command) use ($container) {
            return $container->make($command);
        },
        $commands
    ));
}

set_time_limit(0);
date_default_timezone_set($container->config->get('timezone'));

return $application;
