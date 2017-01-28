<?php

use Symfony\Component\Console\Input\ArrayInput;

$container->bind(GuzzleHttp\ClientInterface::class, GuzzleHttp\Client::class);

$container->bind(ArrayInput::class, function ($container, $params) {
    return new ArrayInput($params);
});
