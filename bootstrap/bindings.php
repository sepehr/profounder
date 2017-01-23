<?php

$container->singleton('identityPool', Profounder\Services\IdentityPool::class);

$container->bind('http', GuzzleHttp\Client::class);
