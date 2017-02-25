<?php

/*
 * Custom container bindings specific to the application.
 */

// Vendors
$container->bind(GuzzleHttp\ClientInterface::class, GuzzleHttp\Client::class);
$container->bind(\GuzzleHttp\Cookie\CookieJarInterface::class, \GuzzleHttp\Cookie\CookieJar::class);

$container->bind(Symfony\Component\Console\Input\ArrayInput::class, function ($container, $params) {
    return new Symfony\Component\Console\Input\ArrayInput($params);
});

// Identity
$container->bind(\Profounder\Service\Identity\PoolContract::class, \Profounder\Service\Identity\JsonFilePool::class);

$container->bind(\Profounder\Service\Identity\Identity::class, function ($container) {
    return ($container->make(\Profounder\Service\Identity\PoolContract::class))->retrieve();
});

// Auth
$container->bind(\Profounder\Auth\Http\RequestContract::class, \Profounder\Auth\Http\Request::class);
$container->bind(\Profounder\Auth\Session\StoreContract::class, \Profounder\Auth\Session\Store::class);
$container->bind(\Profounder\Auth\Http\Parser\ParserContract::class, \Profounder\Auth\Http\Parser\Parser::class);

// Query
$container->bind(\Profounder\Query\Http\RequestContract::class, \Profounder\Query\Http\Request::class);
$container->bind(\Profounder\Query\Storer\StorerContract::class, \Profounder\Query\Storer\Storer::class);
$container->bind(\Profounder\Query\Http\Parser\ParserContract::class, \Profounder\Query\Http\Parser\Parser::class);
$container->bind(\Profounder\Query\Http\Builder\BuilderContract::class, \Profounder\Query\Http\Builder\Builder::class);

// Augment
$container->bind(\Profounder\Augment\Http\RequestContract::class, \Profounder\Augment\Http\Request::class);
$container->bind(\Profounder\Augment\Http\Parser\ParserContract::class, \Profounder\Augment\Http\Parser\Parser::class);
$container->bind(
    \Profounder\Augment\Augmentor\AugmentorContract::class,
    \Profounder\Augment\Augmentor\Augmentor::class
);
