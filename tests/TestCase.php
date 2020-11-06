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
    }
}
