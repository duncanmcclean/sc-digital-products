<?php

namespace DoubleThreeDigital\DigitalProducts\Tests\Listeners;

use DoubleThreeDigital\DigitalProducts\Tests\TestCase;
use DoubleThreeDigital\SimpleCommerce\Events\CartCompleted;
use DoubleThreeDigital\SimpleCommerce\Facades\Order;
use DoubleThreeDigital\SimpleCommerce\Facades\Product;
use Statamic\Facades\Stache;

class ProcessCheckoutTest extends TestCase
{
    /** @test */
    public function can_process_checkout()
    {
        $this->markTestIncomplete();

        $product = Product::create([
            'is_digital_product' => true,
            'price' => 1200,
        ]);

        $cart = Order::create([
            'items' => [
                [
                    'id' => Stache::generateId(),
                    'product' => $product->id(),
                    'quantity' => 1,
                    'total' => 1200,
                ],
            ],
        ]);

        $event = new CartCompleted($cart->entry());

        $cart = Order::find($cart->id());

        $this->assertArrayHasKey($cart->data, 'license_key');
        $this->assertArrayHasKey($cart->data, 'download_url');

        // TODO: assert email has been sent, if customer is attached to order
    }
}
