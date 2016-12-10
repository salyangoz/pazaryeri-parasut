<?php

namespace salyangoz\pazaryeriparasut;

use salyangoz\pazaryeriparasut\Commands\Transfer;
use Illuminate\Support\ServiceProvider;

class PazaryeriParasutServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Transfer::class
            ]);
        }

        $this->publishes([
            __DIR__ . '/Config/pazaryeri-parasut.php' => config_path('pazaryeri-parasut.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PazaryeriParasut::class, function ($app) {
            return new Client(config('pazaryeri-parasut'));
        });

        $this->mergeConfigFrom(
            __DIR__ . '/Config/pazaryeri-parasut.php', 'pazaryeri-parasut'
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('PazaryeriParasut');
    }
}
