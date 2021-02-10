<?php

namespace DoubleThreeDigital\DigitalProducts\Tests;

use Statamic\Extend\Manifest;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use DoubleThreeDigital\DigitalProducts\ServiceProvider;
use Illuminate\Encryption\Encrypter;
use Statamic\Facades\Blueprint;
use Statamic\Providers\StatamicServiceProvider;
use Statamic\Stache\Stores\UsersStore;
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

        $app['config']->set('app.key', 'base64:'.base64_encode(
            Encrypter::generateKey($app['config']['app.cipher'])
        ));
        $app['config']->set('statamic.users.repository', 'file');
        $app['config']->set('statamic.stache.stores.users', [
            'class' => UsersStore::class,
            'directory' => __DIR__.'/__fixtures/users',
        ]);
        $app['config']->set('statamic.api.enabled', true);
        $app['config']->set('simple-commerce', require(__DIR__.'/../vendor/doublethreedigital/simple-commerce/config/simple-commerce.php'));

        Blueprint::setDirectory(__DIR__.'/vendor/doublethreedigital/simple-commerce/resources/blueprints');

        $app->booted(function () use ($app) {
            $this->bootSimpleCommerceRepositories($app);
        });
    }

    protected function bootSimpleCommerceRepositories($app)
    {
        collect([
            \DoubleThreeDigital\SimpleCommerce\Contracts\Order::class    => \DoubleThreeDigital\SimpleCommerce\Orders\Order::class,
            \DoubleThreeDigital\SimpleCommerce\Contracts\Coupon::class   => \DoubleThreeDigital\SimpleCommerce\Coupons\Coupon::class,
            \DoubleThreeDigital\SimpleCommerce\Contracts\Currency::class => \DoubleThreeDigital\SimpleCommerce\Support\Currency::class,
            \DoubleThreeDigital\SimpleCommerce\Contracts\Customer::class => \DoubleThreeDigital\SimpleCommerce\Customers\Customer::class,
            \DoubleThreeDigital\SimpleCommerce\Contracts\Gateway::class  => \DoubleThreeDigital\SimpleCommerce\Gateways\GatewayManager::class,
            \DoubleThreeDigital\SimpleCommerce\Contracts\Product::class  => \DoubleThreeDigital\SimpleCommerce\Products\Product::class,
            \DoubleThreeDigital\SimpleCommerce\Contracts\Shipping::class => \DoubleThreeDigital\SimpleCommerce\Shipping\ShippingManager::class,
        ])->each(function ($concrete, $abstract) use ($app) {
            if (! $app->bound($abstract)) {
                Statamic::repository($abstract, $concrete);
            }
        });

        return $this;
    }
}
