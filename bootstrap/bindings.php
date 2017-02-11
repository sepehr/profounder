<?php

// Vendors
$container->bind(GuzzleHttp\ClientInterface::class, GuzzleHttp\Client::class);

$container->bind(Symfony\Component\Console\Input\ArrayInput::class, function ($container, $params) {
    return new Symfony\Component\Console\Input\ArrayInput($params);
});

// Services
$container->bind(\Profounder\Service\IdentityPoolContract::class, \Profounder\Service\IdentityPool::class);

// Query
$container->bind(\Profounder\Query\StorerContract::class, \Profounder\Query\Storer::class);
$container->bind(\Profounder\Query\BuilderContract::class, \Profounder\Query\Builder::class);
$container->bind(\Profounder\Query\RequestContract::class, \Profounder\Query\Request::class);
$container->bind(\Profounder\Query\ResponseParserContract::class, \Profounder\Query\ResponseParser::class);

// Augment
$container->bind(\Profounder\Augment\RequestContract::class, \Profounder\Augment\Request::class);
$container->bind(\Profounder\Augment\AugmentorContract::class, \Profounder\Augment\Augmentor::class);
$container->bind(\Profounder\Augment\ResponseParserContract::class, \Profounder\Augment\ResponseParser::class);
