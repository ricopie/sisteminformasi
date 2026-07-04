<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ModuleBootstrapTest extends TestCase
{
    #[Test]
    public function it_bootstraps_all_enabled_module_providers(): void
    {
        foreach (config('modules.enabled', []) as $module) {
            $provider = "Modules\\{$module}\\Providers\\{$module}ServiceProvider";

            if (class_exists($provider)) {
                $this->assertTrue(
                    $this->app->providerIsLoaded($provider),
                    "{$provider} is not loaded."
                );
            }
        }
    }
}
