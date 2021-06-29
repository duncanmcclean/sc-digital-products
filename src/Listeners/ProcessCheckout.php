<?php

namespace DoubleThreeDigital\DigitalProducts\Listeners;

use DoubleThreeDigital\DigitalProducts\Events\DigitalDownloadReady;
use DoubleThreeDigital\DigitalProducts\Facades\LicenseKey;
use DoubleThreeDigital\SimpleCommerce\Events\OrderPaid;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\URL;
use Statamic\Entries\Entry;

class ProcessCheckout
{
    public function handle(OrderPaid $event)
    {
        $hasDownloads = $event->order->lineItems()
            ->filter(function ($item) {
                $product = Entry::find($item['product']);

                return $product->has('is_digital_product') ?
                    $product->get('is_digital_product') :
                    false;
            })
            ->each(function ($item) use ($event) {
                $event->order->updateLineItem($item['id'], [
                    'metadata' => array_merge([
                        'license_key'  => $licenseKey = LicenseKey::generate(),
                        'download_url' => URL::signedRoute('statamic.digital-downloads.download', [
                            'order_id'    => $event->order->id,
                            'item_id'     => $item['id'],
                            'license_key' => $licenseKey,
                        ]),
                        'download_history' => [],
                    ], Arr::get($item, 'metadata', [])),
                ]);
            });

        if ($hasDownloads->count() >= 1 && $customer = $event->order->customer()) {
            event(new DigitalDownloadReady($event->order));
        }
    }
}
