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
        $order = Order::find($event->cart->id());

        $hasDownloads = collect($order->get('items'))
            ->reject(function ($item) {
                $product = Entry::find($item['product']);

                return ! ($product->has('is_digital_product') ?
                    $product->get('is_digital_product') :
                    false);
            })
            ->each(function ($item) use ($order) {
                $order->updateOrderItem($item['id'], [
                    'license_key'  => $licenseKey = LicenseKey::generate(),
                    'download_url' => URL::signedRoute('statamic.digital-downloads.download', [
                        'order_id'    => $order->id,
                        'item_id'     => $item['id'],
                        'license_key' => $licenseKey,
                    ]),
                    'download_history' => [],
                ]);
            });

        if ($hasDownloads->count() >= 1 && isset($order->data['customer'])) {
            Mail::to($order->customer()->email())
                ->send(new CustomerDownload($order->entry()));
        }
    }
}
