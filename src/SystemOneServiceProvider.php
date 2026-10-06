<?php

declare(strict_types=1);

namespace Obelaw\SystemOne;

use Illuminate\Support\ServiceProvider;

class SystemOneServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('systemone', function ($app) {
            return new SystemOneManager($app);
        });

        $this->app->alias('systemone', SystemOneManager::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Core initialization
    }
}
