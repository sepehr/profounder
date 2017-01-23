<?php

$container->bind('http', 'GuzzleHttp\Client');

$container->singleton('identityPool', 'Profounder\Services\IdentityPool');

$container->singleton('Illuminate\Contracts\Debug\ExceptionHandler', 'Profounder\Exceptions\Handler');
