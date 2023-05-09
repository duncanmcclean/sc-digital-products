<?php

namespace DoubleThreeDigital\DigitalProducts\Tests\Listeners;

use DoubleThreeDigital\DigitalProducts\Notifications\DigitalDownloadsNotification;
use DoubleThreeDigital\DigitalProducts\Tests\SetupCollections;
use DoubleThreeDigital\DigitalProducts\Tests\TestCase;
use DoubleThreeDigital\SimpleCommerce\Facades\Customer;
use DoubleThreeDigital\SimpleCommerce\Facades\Order;
use DoubleThreeDigital\SimpleCommerce\Facades\Product;
use DoubleThreeDigital\SimpleCommerce\Orders\OrderStatus;
use DoubleThreeDigital\SimpleCommerce\Orders\PaymentStatus;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use Statamic\Facades\Stache;

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

        $product = Product::make()
            ->price(1200)
            ->merge([
                'is_digital_product' => true,
            ]);

        $product->save();

        $customer = Customer::make()
            ->email('duncan@example.com')
            ->merge([
                'name' => 'Duncan',
            ]);

        $customer->save();

        $order = Order::make()
            ->lineItems([
                [
                    'id' => Stache::generateId(),
                    'product' => $product->id(),
                    'quantity' => 1,
                    'total' => 1200,
                ],
            ])
            ->customer($customer->id());

        $order->save();

        $order->updateOrderStatus(OrderStatus::Placed);
        $order->updatePaymentStatus(PaymentStatus::Paid);

        $order->save();

        $order = $order->fresh();

        // Asset metadata is saved
        $lineItem = $order->lineItems()->first();

        $this->assertTrue($lineItem->metadata()->has('license_key'));
        $this->assertTrue($lineItem->metadata()->has('download_url'));
        $this->assertTrue($lineItem->metadata()->has('download_history'));

        // Assert notification has been sent
        Notification::assertSentTo(
            new AnonymousNotifiable,
            DigitalDownloadsNotification::class,
        );
    }
}
