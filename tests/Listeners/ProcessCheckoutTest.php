<?php

namespace DoubleThreeDigital\DigitalProducts\Tests\Listeners;

use DoubleThreeDigital\DigitalProducts\Tests\TestCase;
use DoubleThreeDigital\DigitalProducts\Tests\SetupCollections;
use DoubleThreeDigital\SimpleCommerce\Facades\Customer;
use DoubleThreeDigital\SimpleCommerce\Facades\Order;
use DoubleThreeDigital\SimpleCommerce\Facades\Product;
use Statamic\Facades\Stache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\AnonymousNotifiable;

class ProcessCheckoutTest extends TestCase
{
    use SetupCollections;

    public function setUp(): void
    {
        parent::setUp();

        $this->setupCollections();
    }

    /** @test */
    public function can_process_checkout()
    {
        Notification::fake();

        Config::set('simple-commerce.notifications', [
            'digital_download_ready' => [
                DigitalDownloadsNotification::class => [
                    'to' => 'customer',
                ],
            ],
        ]);

        $product = Product::create([
            'is_digital_product' => true,
            'price' => 1200,
        ]);

        $customer = Customer::create([
            'name' => 'Duncan',
            'email' => 'duncan@example.com',
        ]);

        $order = Order::create([
            'items' => [
                [
                    'id' => Stache::generateId(),
                    'product' => $product->id(),
                    'quantity' => 1,
                    'total' => 1200,
                ],
            ],
            'customer' => $customer->id(),
        ]);

        $order->markAsPaid();

        $order = $order->fresh();

        // Asset metadata is saved
        $lineItem = $order->lineItems()->first();

        $this->assertTrue(array_key_exists('license_key', $lineItem['metadata']));
        $this->assertTrue(array_key_exists('download_url', $lineItem['metadata']));
        $this->assertTrue(array_key_exists('download_history', $lineItem['metadata']));

        // Assert notification has been sent
        Notification::assertSentTo(
            new AnonymousNotifiable,
            DigitalDownloadsNotification::class,
        );
    }
}
