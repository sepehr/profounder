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
$container->bind(\Profounder\Auth\Http\ParserContract::class, \Profounder\Auth\Http\Parser::class);
$container->bind(\Profounder\Auth\Http\RequestContract::class, \Profounder\Auth\Http\Request::class);
$container->bind(\Profounder\Auth\Session\StoreContract::class, \Profounder\Auth\Session\Store::class);

// Query
$container->bind(\Profounder\Query\StorerContract::class, \Profounder\Query\Storer::class);
$container->bind(\Profounder\Query\ParserContract::class, \Profounder\Query\Parser::class);
$container->bind(\Profounder\Query\RequestContract::class, \Profounder\Query\Request::class);
$container->bind(\Profounder\Query\BuilderContract::class, \Profounder\Query\Builder::class);

// Augment
$container->bind(\Profounder\Augment\ParserContract::class, \Profounder\Augment\Parser::class);
$container->bind(\Profounder\Augment\RequestContract::class, \Profounder\Augment\Request::class);
$container->bind(\Profounder\Augment\AugmentorContract::class, \Profounder\Augment\Augmentor::class);
