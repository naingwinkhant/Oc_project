<?php

namespace App\Providers;

use App\Cart\FavouriteService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        // Behind Railway's edge every request reaches this application over
        // plain HTTP, so a generated URL would otherwise say http and send a
        // visitor back to an insecure origin.
        //
        // Driven by the site's own configured address rather than by the
        // environment name: APP_URL is the one setting that states where this
        // deployment actually lives, and it is set on the platform as well as
        // in .env. Deriving it also means a local APP_URL of http://localhost
        // is left alone, so the dev server keeps working.
        if (Str::startsWith((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}
