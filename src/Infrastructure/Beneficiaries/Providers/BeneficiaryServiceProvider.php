<?php

namespace Infrastructure\Beneficiaries\Providers;

use Application\Beneficiaries\Listeners\LogBeneficiaryDeleted;
use Application\Beneficiaries\Listeners\LogBeneficiaryRegistered;
use Application\Beneficiaries\Listeners\LogBeneficiaryUpdated;
use Application\Beneficiaries\Queries\BeneficiaryQueryInterface;
use Application\Beneficiaries\UseCases\DeleteBeneficiaryUseCase;
use Application\Beneficiaries\UseCases\GetBeneficiaryUseCase;
use Application\Beneficiaries\UseCases\ListBeneficiariesUseCase;
use Application\Beneficiaries\UseCases\RegisterBeneficiaryUseCase;
use Application\Beneficiaries\UseCases\UpdateBeneficiaryUseCase;
use Domain\Beneficiaries\Events\BeneficiaryDeleted;
use Domain\Beneficiaries\Events\BeneficiaryRegistered;
use Domain\Beneficiaries\Events\BeneficiaryUpdated;
use Domain\Beneficiaries\Repositories\BeneficiaryRepositoryInterface;
use Domain\Beneficiaries\Repositories\FamilyCardRepositoryInterface;
use Domain\Beneficiaries\Repositories\GuardianRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Beneficiaries\Queries\EloquentBeneficiaryQuery;
use Infrastructure\Beneficiaries\Repositories\EloquentBeneficiaryRepository;
use Infrastructure\Beneficiaries\Repositories\EloquentFamilyCardRepository;
use Infrastructure\Beneficiaries\Repositories\EloquentGuardianRepository;
use Presentation\Beneficiaries\Http\Controllers\DeleteBeneficiaryController;
use Presentation\Beneficiaries\Http\Controllers\GetBeneficiaryController;
use Presentation\Beneficiaries\Http\Controllers\ListBeneficiariesController;
use Presentation\Beneficiaries\Http\Controllers\RegisterBeneficiaryController;
use Presentation\Beneficiaries\Http\Controllers\UpdateBeneficiaryController;

final class BeneficiaryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repositories
        $this->app->bind(BeneficiaryRepositoryInterface::class, EloquentBeneficiaryRepository::class);
        $this->app->bind(FamilyCardRepositoryInterface::class, EloquentFamilyCardRepository::class);
        $this->app->bind(GuardianRepositoryInterface::class, EloquentGuardianRepository::class);

        // Query
        $this->app->bind(BeneficiaryQueryInterface::class, EloquentBeneficiaryQuery::class);

        // Use cases
        $this->app->bind(RegisterBeneficiaryUseCase::class, fn ($app): RegisterBeneficiaryUseCase => new RegisterBeneficiaryUseCase(
            $app->make(BeneficiaryRepositoryInterface::class),
            $app->make(FamilyCardRepositoryInterface::class),
            $app->make(Dispatcher::class),
        ));

        $this->app->bind(UpdateBeneficiaryUseCase::class, fn ($app): UpdateBeneficiaryUseCase => new UpdateBeneficiaryUseCase(
            $app->make(BeneficiaryRepositoryInterface::class),
            $app->make(FamilyCardRepositoryInterface::class),
            $app->make(Dispatcher::class),
        ));

        $this->app->bind(DeleteBeneficiaryUseCase::class, fn ($app): DeleteBeneficiaryUseCase => new DeleteBeneficiaryUseCase(
            $app->make(BeneficiaryRepositoryInterface::class),
            $app->make(Dispatcher::class),
        ));

        $this->app->bind(GetBeneficiaryUseCase::class, fn ($app): GetBeneficiaryUseCase => new GetBeneficiaryUseCase(
            $app->make(BeneficiaryRepositoryInterface::class),
        ));

        $this->app->bind(ListBeneficiariesUseCase::class, fn ($app): ListBeneficiariesUseCase => new ListBeneficiariesUseCase(
            $app->make(BeneficiaryQueryInterface::class),
        ));
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Migrations');

        // Register domain event listeners
        Event::listen(BeneficiaryRegistered::class, LogBeneficiaryRegistered::class);
        Event::listen(BeneficiaryUpdated::class, LogBeneficiaryUpdated::class);
        Event::listen(BeneficiaryDeleted::class, LogBeneficiaryDeleted::class);

        Route::prefix('beneficiaries')
            ->as('beneficiaries.')
            ->group(function (): void {
                Route::get('/', ListBeneficiariesController::class)->name('index');
                Route::post('/', RegisterBeneficiaryController::class)->name('store');
                Route::get('{id}', GetBeneficiaryController::class)->name('show');
                Route::put('{id}', UpdateBeneficiaryController::class)->name('update');
                Route::delete('{id}', DeleteBeneficiaryController::class)->name('delete');
            });
    }
}
