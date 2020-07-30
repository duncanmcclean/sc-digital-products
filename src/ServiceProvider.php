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

        $this->app->bind('LicenseKey', Repositories\LicenseKeyRepository::class);
    }
}
