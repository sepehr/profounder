<?php

$container->bind('http', GuzzleHttp\Client::class);

$container->singleton('identityPool', Profounder\Services\IdentityPool::class);

$container->singleton('Illuminate\Contracts\Debug\ExceptionHandler', Profounder\Exceptions\Handler::class);
