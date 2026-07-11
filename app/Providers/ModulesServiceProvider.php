<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class ModulesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $enableModules = config('modules.enabled', []);

        foreach ($enableModules as $module) {
            $moduleDir = Str::plural($module);
            $providerClass = sprintf('Infrastructure\%s\Providers\%sServiceProvider', $moduleDir, $module);

            if (! class_exists($providerClass)) {
                Log::warning('Infrastructure provider not found.', ['context' => $module, 'provider' => $providerClass]);

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
