<?php

use App\Providers\AppServiceProvider;
use App\Providers\ModulesServiceProvider;
use Infrastructure\Security\Providers\SecurityServiceProvider;

return [
    AppServiceProvider::class,
    ModulesServiceProvider::class,
    SecurityServiceProvider::class,
];
