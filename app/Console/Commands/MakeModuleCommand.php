<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeModuleCommand extends Command
{
    protected $signature = 'make:module {name} {--layers=*} {--force}';

    protected $description = 'Create a new DDD module under src/.';

    public function handle(): int
    {
        $name = str($this->argument('name'))->studly()->plural()->toString();

        if (strlen((string) $name) < 2) {
            $this->error('Module name must be at least 2 characters.');

            return self::FAILURE;
        }

        if ($this->moduleExists($name) && ! $this->option('force')) {
            $this->error(sprintf('Module %s already exists.', $name));

            return self::FAILURE;
        }

        $this->ensureModuleConfigExists();

        $layers = $this->option('layers') ?: array_keys(config('modules.paths'));

        try {
            $this->createDirectories($name, $layers);
            $this->createGitKeep($name, $layers);

            if (in_array('infrastructure', $layers)) {
                $this->createServiceProvider($name);
            }
        } catch (Exception $exception) {
            $this->error('Failed to create module: '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->info(sprintf('Module [%s] created successfully.', $name));
        $this->line('Enabled it in config/modules when ready.');

        return self::SUCCESS;
    }

    private function moduleExists(string $name): bool
    {
        foreach (config('modules.paths') as $path) {
            if (File::exists(base_path(sprintf('%s/%s', $path, $name)))) {
                return true;
            }
        }

        return false;
    }

    private function ensureModuleConfigExists(): void
    {
        if (! File::exists(config_path('modules.php'))) {
            File::copy(base_path('stubs/config-modules.php'), config_path('modules.php'));
        }
    }

    private function createDirectories(string $name, array $layers): void
    {
        foreach ($layers as $layer) {
            foreach (config('modules.structure.'.$layer) as $dir) {
                File::makeDirectory(base_path(config('modules.paths.'.$layer).sprintf('/%s/%s', $name, $dir)), 0755, true);
            }
        }
    }

    private function createGitKeep(string $name, array $layers): void
    {
        foreach ($layers as $layer) {
            foreach (config('modules.structure.'.$layer) as $dir) {
                File::put(base_path(config('modules.paths.'.$layer).sprintf('/%s/%s/.gitkeep', $name, $dir)), '');
            }
        }
    }

    private function createServiceProvider(string $name): void
    {
        $paths = config('modules.paths');
        $stubPath = File::get(base_path('stubs/module/service-provider.stub'));

        $namespace = str_replace('/', '\\', $paths['infrastructure']);
        $namespace = preg_replace('/^src\\\\/i', '', $namespace);
        $namespace = sprintf('%s\%s\Providers', $namespace, $name);

        $stub = str_replace(
            ['DummyNamespace', 'DummyClass'],
            [$namespace, $name.'ServiceProvider'],
            $stubPath
        );

        File::put(
            base_path(sprintf('%s/%s/Providers/%sServiceProvider.php', $paths['infrastructure'], $name, $name)),
            $stub
        );
    }
}
