<?php

declare(strict_types=1);

namespace Copie\Shared\Infrastructure\Laravel\Providers;

use Copie\Shared\Domain\Ports\EncryptionKeyProviderInterface;
use Copie\Shared\Infrastructure\Laravel\Encryption\AwsKmsKeyProvider;
use Copie\Shared\Infrastructure\Laravel\Encryption\EnvKeyProvider;
use Copie\Shared\Infrastructure\Laravel\Encryption\VaultKeyProvider;
use Illuminate\Support\ServiceProvider;

class EncryptionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../Config/encryption.php',
            'encryption'
        );

        $this->app->bind(
            EncryptionKeyProviderInterface::class,
            function (): AwsKmsKeyProvider|VaultKeyProvider|EnvKeyProvider {
                $provider = config('encryption.key_provider');

                return match ($provider) {
                    'aws' => new AwsKmsKeyProvider,
                    'vault' => new VaultKeyProvider,
                    default => new EnvKeyProvider,
                };
            }
        );
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../Config/encryption.php' => config_path('encryption.php'),
            ], 'encryption-config');
        }
    }
}
