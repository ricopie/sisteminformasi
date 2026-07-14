<?php

declare(strict_types=1);

namespace Infrastructure\Auth\Providers;

use Illuminate\Support\ServiceProvider;

final class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Tidak ada binding spesifik untuk auth
    }

    public function boot(): void
    {
        //
    }
}
