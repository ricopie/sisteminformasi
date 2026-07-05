<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ModulesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
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

    public function boot(): void
    {
        $enableModules = config('modules.enabled', []);

        foreach ($enableModules as $module) {
            $modulePath = base_path("modules/{$module}");

            $viewPath = "{$modulePath}/Resources/views";
            if (is_dir($viewPath)) {
                $this->loadViewsFrom($viewPath, $module);
            }

            $routesPath = "{$modulePath}/Routes/web.php";
            if (file_exists($routesPath)) {
                $this->loadRoutesFrom($routesPath);
            }
        }
    }
}
