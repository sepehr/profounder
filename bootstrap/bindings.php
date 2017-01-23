<?php

$container->singleton('identityPool', Profounder\Services\IdentityPool::class);

$container->singleton('watch', Symfony\Component\Stopwatch\Stopwatch::class);

$container->bind('http', GuzzleHttp\Client::class);
