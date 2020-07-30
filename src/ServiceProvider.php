<?php

namespace DoubleThreeDigital\DigitalProducts;

use DoubleThreeDigital\DigitalProducts\Listeners\ProcessCheckout;
use DoubleThreeDigital\SimpleCommerce\Events\CartCompleted;
use Statamic\Events\EntryBlueprintFound;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $listen = [
        EntryBlueprintFound::class => [
            Listeners\AddFieldsToProductBlueprint::class,
        ],
        CartCompleted::class => [
            ProcessCheckout::class,
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
            ->bootRepositries();
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
        $this->app->bind('LicenseKey', Repositories\LicenseKeyRepository::class);

        return $this;
    }
}
