<?php

declare(strict_types=1);

namespace Copie\Contexts\Beneficiary\Infrastructure\Laravel\Providers;

use Copie\Contexts\Beneficiary\Application\BeneficiaryQueryInterface;
use Copie\Contexts\Beneficiary\Application\BeneficiaryRepositoryInterface;
use Copie\Contexts\Beneficiary\Infrastructure\Laravel\Eloquent\BeneficiaryModel;
use Copie\Contexts\Beneficiary\Infrastructure\Laravel\EloquentBeneficiaryRepository;
use Copie\Contexts\Beneficiary\Infrastructure\Laravel\Queries\EloquentBeneficiaryQuery;
use Illuminate\Support\ServiceProvider;

/** Service provider for Beneficiary context. */
class BeneficiaryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            BeneficiaryRepositoryInterface::class,
            fn (): EloquentBeneficiaryRepository => new EloquentBeneficiaryRepository(new BeneficiaryModel)
        );

        $this->app->bind(
            BeneficiaryQueryInterface::class,
            fn (): EloquentBeneficiaryQuery => new EloquentBeneficiaryQuery(new BeneficiaryModel)
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Migrations');
    }
}
