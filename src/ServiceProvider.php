<?php

namespace DoubleThreeDigital\DigitalDownloads;

use DoubleThreeDigital\DigitalDownloads\Listeners\ProcessCheckout;
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
    }
}
