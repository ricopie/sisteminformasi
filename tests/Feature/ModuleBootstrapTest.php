<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ModuleBootstrapTest extends TestCase
{
    #[Test]
    public function it_bootstraps_all_enabled_module_providers(): void
    {
        foreach (config('modules.enabled', []) as $module) {
            $moduleDir = Str::plural($module);
            $provider = sprintf('Infrastructure\%s\Providers\%sServiceProvider', $moduleDir, $module);

            if (class_exists($provider)) {
                $this->assertTrue(
                    $this->app->providerIsLoaded($provider),
                    $provider.' is not loaded.'
                );
            }
        }
    }
}
