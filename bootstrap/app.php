<?php

require_once 'autoload.php';

use Profounder\Application;

$container   = require_once 'container.php';
$application = new Application($container, $container->config->get('name'), $container->config->get('version'));

if ($commands = $container->config->get('commands')) {
    $application->addCommands(array_map(
        function ($command) {
            return new $command;
        },
        $commands
    ));
}

set_time_limit(0);
date_default_timezone_set($container->config->get('timezone'));

return $application;
