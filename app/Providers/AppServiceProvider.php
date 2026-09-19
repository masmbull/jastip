<?php

namespace App\Providers;

use App\Services\CartService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // TLS terminated at Cloudflare edge; keep generated URLs https in production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Register CartService as singleton for consistent cart across the session
        $this->app->singleton(CartService::class, function ($app) {
            return new CartService();
        });
    }
}
