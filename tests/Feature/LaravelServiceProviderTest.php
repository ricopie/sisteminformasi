<?php

declare(strict_types=1);

namespace Tests\Feature;

use Copie\Contexts\Beneficiary\Application\BeneficiaryRepositoryInterface;
use Copie\Shared\Infrastructure\Laravel\Providers\LaravelServiceProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LaravelServiceProviderTest extends TestCase
{
    #[Test]
    public function test_discovers_all_contexts(): void
    {
        /** @var LaravelServiceProvider $provider */
        $provider = app()->getProvider(LaravelServiceProvider::class);

        $this->assertNotNull($provider, 'LaravelServiceProvider should be registered');
        $this->assertContains('Beneficiary', $provider->modules());
    }

    #[Test]
    public function test_registers_beneficiary_service_provider(): void
    {
        $this->assertTrue(
            app()->bound(BeneficiaryRepositoryInterface::class),
            'Beneficiary context should be auto-discovered and its repository interface should be bound',
        );
    }
}
