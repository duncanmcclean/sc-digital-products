<?php

namespace DoubleThreeDigital\DigitalProducts\Listeners;

use DoubleThreeDigital\DigitalProducts\Facades\LicenseKey;
use DoubleThreeDigital\DigitalProducts\Mail\CustomerDownload;
use DoubleThreeDigital\SimpleCommerce\Events\CartCompleted;
use DoubleThreeDigital\SimpleCommerce\Facades\Customer;
use DoubleThreeDigital\SimpleCommerce\Facades\Order;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Statamic\Entries\Entry;

class ProcessCheckout
{
    public function handle(CartCompleted $event)
    {
        $hasDownloads = false;
        $order = Order::find($event->cart->id());

        collect($order->get('items'))
            ->reject(function ($item) {
                $product = Entry::find($item['product']);

                return ! $product->has('is_digital_product') ?
                    $product->get('is_digital_product') :
                    false;
            })
            ->each(function ($item) use ($order, &$hasDownloads) {
                $item['license_key'] = LicenseKey::generate();
                $item['download_url'] = URL::signedRoute('statamic.digital-downloads.download', [
                    'order_id' => $order->id,
                    'item_id' => $item['id'],
                    'license_key' => $item['license_key'],
                ]);

                $data = [
                    'items' => [
                        $item,
                    ],
                ];

                $order->data($data)->save();

                $hasDownloads = true;
            });

        if ($hasDownloads && isset($order->data['customer'])) {
            Mail::to(Customer::find($order->data['customer'])->data['email'])
                ->send(new CustomerDownload($order->entry()));
        }
    }
}
