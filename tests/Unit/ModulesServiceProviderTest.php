<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Providers\ModulesServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Infrastructure\Beneficiaries\Providers\BeneficiaryServiceProvider;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ModulesServiceProviderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Log::spy();
    }

    #[Test]
    public function it_registers_enabled_module_providers(): void
    {
        // Only enable Beneficiary (Organization doesn't have a provider yet)
        Config::set('modules.enabled', ['Beneficiary']);

        $provider = new ModulesServiceProvider($this->app);
        $provider->register();

        $this->assertTrue(
            $this->app->providerIsLoaded(
                BeneficiaryServiceProvider::class
            )
        );
    }

    #[Test]
    public function it_skips_when_no_modules_enabled(): void
    {
        Config::set('modules.enabled', []);

        $provider = new ModulesServiceProvider($this->app);
        $provider->register();

        Log::shouldNotHaveReceived('warning');
    }

    #[Test]
    public function it_logs_warning_when_provider_not_found(): void
    {
        Config::set('modules.enabled', ['NonExistent']);

        $provider = new ModulesServiceProvider($this->app);
        $provider->register();

        Log::shouldHaveReceived('warning')
            ->once()
            ->with('Infrastructure provider not found.', Mockery::on(fn (array $context): bool => ($context['context'] ?? null) === 'NonExistent'));
    }
}
