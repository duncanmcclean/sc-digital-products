<?php

namespace DoubleThreeDigital\DigitalProducts;

use DoubleThreeDigital\SimpleCommerce\Events\OrderPaid;
use Illuminate\Support\Facades\Route;
use Statamic\Events\EntryBlueprintFound;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Statamic;

class ServiceProvider extends AddonServiceProvider
{
    protected $listen = [
        EntryBlueprintFound::class => [
            Listeners\AddFieldsToProductBlueprint::class,
        ],
        OrderPaid::class => [
            Listeners\ProcessCheckout::class,
        ],
    ];

    protected $routes = [
        'actions' => __DIR__.'/../routes/actions.php',
    ];

    public function boot()
    {
        parent::boot();

        $this
            ->bootVendorAssets()
            ->bootRepositries()
            ->registerApiRoutes();
    }

    protected function bootVendorAssets()
    {
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/sc-digital-products'),
        ], 'sc-digital-products-views');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'sc-digital-products');

        return $this;
    }

    protected function bootRepositries()
    {
        Statamic::repository(
            Contracts\LicenseKeyRepository::class,
            Repositories\LicenseKeyRepository::class
        );

        return $this;
    }

    protected function registerApiRoutes()
    {
        if (config('statamic.api.enabled') === true) {
            Route::middleware(config('statamic.api.middleware'))
                ->name('sc-digital-products.api.')
                ->prefix(config('statamic.api.route').'/sc-digital-downloads/')
                ->group(__DIR__.'/../routes/api.php');
        }

        return $this;
    }
}
