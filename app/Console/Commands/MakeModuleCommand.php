<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeModuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module.';

    /**
     * Execute the console command.
     */
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

        $this->info("Module [$module] created successfuly.");
        $this->line("Remember to enale it in config/modules when module it's ready.");

        return self::SUCCESS;
    }

    private function moduleExists(string $module): bool
    {
        return File::exists(base_path("modules/$module/"));
    }

    private function ensureModuleConfigExists(): void
    {
        if (File::exists(config_path('modules.php'))) {
            return;
        }

        File::copy(base_path('stubs/config-modules.php'), config_path('modules.php'));
    }

    private function createDirectories(string $module): void
    {
        File::makeDirectory(base_path("modules/{$module}/Database/Migrations"), recursive: true);
        File::makeDirectory(base_path("modules/{$module}/Providers"), recursive: true);
    }

    private function createGitKeep(string $module): void
    {
        File::put(base_path("modules/{$module}/Database/Migrations/.gitkeep"), '');
    }

    private function createServiceProvider(string $module): void
    {
        // Generate Serive Provider
        $stubPath = File::get(base_path('stubs/module/service-provider.stub'));
        $stub = str_replace(['DummyNamespace', 'DummyClass'],
            [
                "Modules\\{$module}\\Providers",
                "{$module}ServiceProvider",
            ], $stubPath);

        File::put(base_path("modules/{$module}/Providers/{$module}ServiceProvider.php"), $stub);
    }
}
