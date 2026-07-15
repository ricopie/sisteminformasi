<?php

declare(strict_types=1);

namespace Infrastructure\Security\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Beneficiaries\Models\BeneficiaryModel;
use Infrastructure\Security\Console\GenerateSecureIdKey;
use Infrastructure\Security\Services\SecureIdService;

final class SecurityServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register SecureIdService as singleton
        $this->app->singleton(SecureIdService::class, fn ($app): SecureIdService => new SecureIdService(
            key: config('secureid.key'),
            cipher: config('secureid.cipher', 'aes-256-gcm'),
            nonceLength: (int) config('secureid.nonce_length', 12),
        ));
    }

    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../Migrations');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateSecureIdKey::class,
            ]);
        }

        // Register route bindings for encrypted IDs
        Route::bind('secure_beneficiary', function (string $value): BeneficiaryModel {
            $service = $this->app->make(SecureIdService::class);

            // Try to decrypt first
            $id = $service->decrypt($value, 'beneficiary');

            // Fallback: treat as raw ULID (migration period)
            $id ??= $value;

            $model = BeneficiaryModel::find($id);

            if (! $model instanceof BeneficiaryModel) {
                abort(404, 'Beneficiary not found.');
            }

            return $model;
        });
    }
}
