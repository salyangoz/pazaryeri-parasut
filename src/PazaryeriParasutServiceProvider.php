<?php

namespace salyangoz\pazaryeriparasut;

use salyangoz\pazaryeriparasut\Commands\EinvoiceTransfer;
use salyangoz\pazaryeriparasut\Commands\Transfer;
use salyangoz\pazaryeriparasut\Commands\TransferEInvoice;
use salyangoz\pazaryeriparasut\Commands\Import;
use Illuminate\Support\ServiceProvider;
use salyangoz\pazaryeriparasut\Commands\Pull;
use salyangoz\pazaryeriparasut\Commands\Push;
use salyangoz\pazaryeriparasut\Commands\Einvoice;

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
                Pull::class,
                Push::class,
                Einvoice::class,
            ]);
        }

        $this->publishes([
            __DIR__ . '/Config/pazaryeri-parasut.php' => config_path('pazaryeri-parasut.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
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

        $this->loadViewsFrom(__DIR__ . '/Resources/Views', 'pazaryeri-parasut');

        $this->publishes([
            __DIR__ . '//Resources/Views' => resource_path('views/salyangoz/pazaryeri-parasut'),
        ]);

        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
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
