<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ModulesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $enableModules = config('modules.enabled', []);

        foreach ($enableModules as $module) {
            $providerClass = "Infrastructure\\Persistence\\{$module}\\Providers\\{$module}ServiceProvider";

            if (! class_exists($providerClass)) {
                logger()->warning('Infrastructure provider not found.', ['context' => $module, 'provider' => $providerClass]);

                continue;
            }
            $this->app->register($providerClass);
        }
    }

    public function boot(): void
    {
        //
    }
}
