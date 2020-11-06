<?php

namespace DoubleThreeDigital\DigitalProducts\Tests;

use Statamic\Extend\Manifest;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use DoubleThreeDigital\DigitalProducts\ServiceProvider;
use Statamic\Facades\Blueprint;
use Statamic\Providers\StatamicServiceProvider;
use Statamic\Statamic;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            StatamicServiceProvider::class,
            ServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Statamic' => Statamic::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app->make(Manifest::class)->manifest = [
            'doublethreedigital/sc-digital-products' => [
                'id' => 'doublethreedigital/sc-digital-products',
                'namespace' => 'DoubleThreeDigital\\DigitalProducts\\',
            ],
        ];
    }

    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        $configs = [
            'assets', 'cp', 'forms', 'static_caching',
            'sites', 'stache', 'system', 'users'
        ];

        foreach ($configs as $config) {
            $app['config']->set("statamic.$config", require(__DIR__."/../vendor/statamic/cms/config/{$config}.php"));
        }

        $app['config']->set('statamic.users.repository', 'file');
        $app['config']->set('simple-commerce', require(__DIR__.'/../vendor/doublethreedigital/simple-commerce/config/simple-commerce.php'));

        Blueprint::setDirectory(__DIR__.'/vendor/doublethreedigital/simple-commerce/resources/blueprints');

        $app->booted(function () use ($app) {
            $this->bootSimpleCommerceRepositories($app);
        });
    }

    protected function bootSimpleCommerceRepositories($app)
    {
        collect([
            \DoubleThreeDigital\SimpleCommerce\Contracts\CartRepository::class => \DoubleThreeDigital\SimpleCommerce\Repositories\CartRepository::class,
            \DoubleThreeDigital\SimpleCommerce\Contracts\CouponRepository::class => \DoubleThreeDigital\SimpleCommerce\Repositories\CouponRepository::class,
            \DoubleThreeDigital\SimpleCommerce\Contracts\CurrencyRepository::class => \DoubleThreeDigital\SimpleCommerce\Repositories\CurrencyRepository::class,
            \DoubleThreeDigital\SimpleCommerce\Contracts\CustomerRepository::class => \DoubleThreeDigital\SimpleCommerce\Repositories\CustomerRepository::class,
            \DoubleThreeDigital\SimpleCommerce\Contracts\GatewayRepository::class => \DoubleThreeDigital\SimpleCommerce\Repositories\GatewayRepository::class,
            \DoubleThreeDigital\SimpleCommerce\Contracts\ProductRepository::class => \DoubleThreeDigital\SimpleCommerce\Repositories\ProductRepository::class,
            \DoubleThreeDigital\SimpleCommerce\Contracts\ShippingRepository::class => \DoubleThreeDigital\SimpleCommerce\Repositories\ShippingRepository::class,
        ])->each(function ($concrete, $abstract) use ($app) {
            if (! $app->bound($abstract)) {
                Statamic::repository($abstract, $concrete);
            }
        });

        return $this;
    }
}
