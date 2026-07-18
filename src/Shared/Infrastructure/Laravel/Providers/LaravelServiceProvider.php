<?php

declare(strict_types=1);

namespace Copie\Shared\Infrastructure\Laravel\Providers;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\Finder\Finder;

class LaravelServiceProvider extends ServiceProvider
{
    private string $contextsPath;

    /** @var list<string> */
    private array $discoveredModules = [];

    public function register(): void
    {
        $this->app->register(EncryptionServiceProvider::class);

        // Auto-Discover Providers from Boundary Context
        $this->contextsPath = base_path('src/Contexts');
        $this->discoverContexts();
    }

    private function discoverContexts(): void
    {
        if (! is_dir($this->contextsPath)) {
            return;
        }

        $directories = $this->findContextDirectories();

        foreach ($directories as $directory) {
            $contextName = basename($directory);
            $serviceProviderPath = $directory.'/Infrastructure/Laravel/Providers/'.$contextName.'ServiceProvider.php';

            if (file_exists($serviceProviderPath)) {
                $providerClass = $this->resolveServiceProviderClass($contextName);

                if (class_exists($providerClass)) {
                    $this->app->register($providerClass);
                    $this->discoveredModules[] = $contextName;
                }
            }
        }
    }

    /**
     * @return string[]
     */
    private function findContextDirectories(): array
    {
        $directories = [];

        $finder = new Finder;
        $finder->depth(0)->directories()->in($this->contextsPath);

        foreach ($finder as $directory) {
            $directories[] = $directory->getRealPath();
        }

        return $directories;
    }

    private function resolveServiceProviderClass(string $contextName): string
    {
        return sprintf(
            'Copie\\Contexts\\%s\\Infrastructure\\Laravel\\Providers\\%sServiceProvider',
            $contextName,
            $contextName
        );
    }

    public function modules(): array
    {
        return $this->discoveredModules;
    }
}
