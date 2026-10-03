<?php

namespace App\Providers;

use App\Cart\FavouriteService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Scoped so the memoised favourite id set survives the whole request and
        // product grids do not fire a query per card.
        $this->app->scoped(FavouriteService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        URL::forceScheme('https');
    }
}
