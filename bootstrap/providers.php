<?php

use App\Providers\AppServiceProvider;
use Copie\Shared\Infrastructure\Laravel\Providers\EncryptionServiceProvider;
use Copie\Shared\Infrastructure\Laravel\Providers\LaravelServiceProvider;

return [
    AppServiceProvider::class,
    LaravelServiceProvider::class,
    EncryptionServiceProvider::class,
];
