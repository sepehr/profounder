<?php

// Vendors
$container->bind(GuzzleHttp\ClientInterface::class, GuzzleHttp\Client::class);

$container->bind(Symfony\Component\Console\Input\ArrayInput::class, function ($container, $params) {
    return new Symfony\Component\Console\Input\ArrayInput($params);
});

// Services
$container->bind(\Profounder\Service\Identity\PoolContract::class, \Profounder\Service\Identity\JsonFilePool::class);

// Query
$container->bind(\Profounder\Query\StorerContract::class, \Profounder\Query\Storer::class);
$container->bind(\Profounder\Query\ParserContract::class, \Profounder\Query\Parser::class);
$container->bind(\Profounder\Query\BuilderContract::class, \Profounder\Query\Builder::class);
$container->bind(\Profounder\Query\RequestContract::class, \Profounder\Query\Request::class);

// Augment
$container->bind(\Profounder\Augment\ParserContract::class, \Profounder\Augment\Parser::class);
$container->bind(\Profounder\Augment\RequestContract::class, \Profounder\Augment\Request::class);
$container->bind(\Profounder\Augment\AugmentorContract::class, \Profounder\Augment\Augmentor::class);
