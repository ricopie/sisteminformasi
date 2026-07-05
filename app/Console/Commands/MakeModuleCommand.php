<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeModuleCommand extends Command
{
    protected $signature = 'make:module {name} {--api : Generate API-only module without views}';

    protected $description = 'Create a new module.';

    /** Execute the console command */
    public function handle(): int
    {
        $module = str($this->argument('name'))->studly()->toString();

        $this->ensureModuleConfigExists();

        if ($this->moduleExists($module)) {
            $this->error("Module {$module} already exists.");

            return self::FAILURE;
        }

        $this->createDirectories($module);
        $this->createGitKeep($module);
        $this->createServiceProvider($module);
        $this->createController($module);
        $this->createRoutes($module);

        $this->info("Module [$module] created successfully.");
        $this->line('Remember to enable it in config/modules when the module is ready.');

        return self::SUCCESS;
    }

    /** Check if module directory already exists */
    private function moduleExists(string $module): bool
    {
        return File::exists(base_path("modules/$module/"));
    }

    /** Copy default config if modules.php doesn't exist */
    private function ensureModuleConfigExists(): void
    {
        if (File::exists(config_path('modules.php'))) {
            return;
        }

        File::copy(base_path('stubs/config-modules.php'), config_path('modules.php'));
    }

    /** Create module directory structure */
    private function createDirectories(string $module): void
    {
        File::makeDirectory(base_path("modules/{$module}/Database/Migrations"), recursive: true);
        File::makeDirectory(base_path("modules/{$module}/Http/Controllers"), recursive: true);
        File::makeDirectory(base_path("modules/{$module}/Providers"), recursive: true);
        File::makeDirectory(base_path("modules/{$module}/Routes"), recursive: true);

        if (! $this->option('api')) {
            File::makeDirectory(base_path("modules/{$module}/Resources/views"), recursive: true);
        }
    }

    /** Create .gitkeep files for empty directories */
    private function createGitKeep(string $module): void
    {
        File::put(base_path("modules/{$module}/Database/Migrations/.gitkeep"), '');

        if (! $this->option('api')) {
            File::put(base_path("modules/{$module}/Resources/views/.gitkeep"), '');
        }
    }

    /** Generate service provider from stub */
    private function createServiceProvider(string $module): void
    {
        $stubPath = File::get(base_path('stubs/module/service-provider.stub'));
        $stub = str_replace(['DummyNamespace', 'DummyClass'],
            [
                "Modules\\{$module}\\Providers",
                "{$module}module}ServiceProvider",
            ], $stubPath);

        File::put(base_path("modules/{$module}/Providers/{$module}ServiceProvider.php"), $stub);
    }

    /** Generate controller from stub (Blade or API) */
    private function createController(string $module): void
    {
        $stub = $this->option('api')
            ? 'api-controller.stub'
            : 'controller.stub';

        $stubPath = File::get(base_path("stubs/module/{$stub}"));
        $stub = str_replace(
            ['DummyNamespace', 'DummyClass'],
            [
                "Modules\\{$module}",
                "{$module}Controller",
            ],
            $stubPath
        );

        File::put(base_path("modules/{$module}/Http/Controllers/{$module}Controller.php"), $stub);
    }

    /** Generate routes file from stub (web.php or api.php) */
    private function createRoutes(string $module): void
    {
        $stub = $this->option('api')
            ? 'api-route.stub'
            : 'web.php.stub';

        $stubPath = File::get(base_path("stubs/module/{$stub}"));
        $kebab = str($module)->kebab()->toString();
        $stub = str_replace(
            ['DummyController', 'DummyModule'],
            [
                "Modules\\{$module}\\Http\\Controllers\\{$module}Controller",
                $kebab,
            ],
            $stubPath
        );

        $filename = $this->option('api') ? 'api.php' : 'web.php';

        File::put(base_path("modules/{$module}/Routes/{$filename}"), $stub);
    }
}
