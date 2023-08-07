<?php

namespace DoubleThreeDigital\DigitalProducts\Tests\Helpers;

use Illuminate\Support\Facades\File;

trait UseDatabaseContentDrivers
{
    public function setUpDatabaseContentDrivers()
    {
        $this->stubsPath = __DIR__.'/../../vendor/doublethreedigital/simple-commerce/tests/__fixtures__/database/migrations';

        if (count(File::glob(database_path('migrations').'/*_create_customers_table.php')) < 1) {
            File::copy($this->stubsPath.'/create_customers_table.php', database_path('migrations/'.date('Y_m_d_His').'_create_customers_table.php'));
        }

        if (count(File::glob(database_path('migrations').'/*_create_orders_table.php')) < 1) {
            File::copy($this->stubsPath.'/create_orders_table.php', database_path('migrations/'.date('Y_m_d_His').'_create_orders_table.php'));
        }

        $this->runLaravelMigrations();

        $this->app['config']->set('simple-commerce.content.customers', [
            'repository' => \DoubleThreeDigital\SimpleCommerce\Customers\EloquentCustomerRepository::class,
            'model' => \DoubleThreeDigital\SimpleCommerce\Customers\CustomerModel::class,
        ]);

        $this->app['config']->set('simple-commerce.content.orders', [
            'repository' => \DoubleThreeDigital\SimpleCommerce\Orders\EloquentOrderRepository::class,
            'model' => \DoubleThreeDigital\SimpleCommerce\Orders\OrderModel::class,
        ]);

        $this->app->bind(
            \DoubleThreeDigital\SimpleCommerce\Contracts\CustomerRepository::class,
            $this->app['config']->get('simple-commerce.content.customers.repository')
        );

        $this->app->bind(
            \DoubleThreeDigital\SimpleCommerce\Contracts\OrderRepository::class,
            $this->app['config']->get('simple-commerce.content.orders.repository')
        );
    }
}
