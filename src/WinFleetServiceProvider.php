<?php

namespace Seat\Akturis\WinFleet;

use Illuminate\Support\ServiceProvider;
use Seat\Akturis\WinFleet\Observers\WinFleetAwardObserver;
use Seat\Akturis\WinFleet\Observers\WinFleetOperationObserver;
use Seat\Akturis\WinFleet\Models\WinFleetOperation;
use Seat\Akturis\WinFleet\Models\WinFleetAward;

class WinFleetServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->add_routes();
        $this->add_views();
        $this->add_publications();
        $this->add_translations();
        $this->addObservers();
//        $this->add_commands();
    }

    /**
     * Include the routes.
     */
    public function add_routes()
    {
        if (! $this->app->routesAreCached()) {
            include __DIR__ . '/Http/routes.php';
        }
    }

    public function add_translations()
    {
        $this->loadTranslationsFrom(__DIR__ . '/lang', 'winfleet');
    }

    /**
     * Set the path and namespace for the views.
     */
    public function add_views()
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'winfleet');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/Config/winfleet.config.php',
            'winfleet.config'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/Config/winfleet.sidebar.php',
            'package.sidebar'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/Config/winfleet.permissions.php',
            'web.permissions'
        );
    }

    private function addObservers() 
    {
        WinFleetAward::observe(WinFleetAwardObserver::class);
        WinFleetOperation::observe(WinFleetOperationObserver::class);
    }

    public function add_publications()
    {
        $this->publishes([
            __DIR__ . '/database/migrations/' => database_path('migrations'),
        ]);
    }

}
