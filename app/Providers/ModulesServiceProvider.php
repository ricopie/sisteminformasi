<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ModulesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register Service Provider with module enabled configuration
        $enableModules = config('modules.enabled', []);

        foreach ($enableModules as $module) {
            $providerClass = "Modules\\{$module}\\Providers\\{$module}ServiceProvider";

            if (! class_exists($providerClass)) {
                logger()->warning('Module service provider not found.', ['module' => $module, 'provider' => $providerClass]);

                continue;
            }
            $this->app->register($providerClass);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
