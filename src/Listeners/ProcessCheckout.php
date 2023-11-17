<?php

namespace DoubleThreeDigital\DigitalProducts\Listeners;

use DoubleThreeDigital\DigitalProducts\Events\DigitalDownloadReady;
use DoubleThreeDigital\DigitalProducts\Facades\LicenseKey;
use DoubleThreeDigital\SimpleCommerce\Events\OrderStatusUpdated;
use DoubleThreeDigital\SimpleCommerce\Orders\OrderStatus;
use DoubleThreeDigital\SimpleCommerce\Products\ProductType;
use Illuminate\Support\Facades\URL;

class ProcessCheckout
{
    public function handle(OrderStatusUpdated $event)
    {
        // Ignore if the order status wasn't just set to "Placed".
        if ($event->orderStatus !== OrderStatus::Placed) {
            return;
        }

        $hasDownloads = $event->order->lineItems()
            ->filter(function ($lineItem) {
                $product = $lineItem->product();

                if ($product->purchasableType() === ProductType::Variant) {
                    $productVariant = $product->variant($lineItem->variant());

                    return $productVariant->has('is_digital_product')
                        ? $productVariant->get('is_digital_product')
                        : false;
                }

                return $product->has('is_digital_product')
                    ? $product->get('is_digital_product')
                    : false;
            })
            ->each(function ($lineItem) use ($event) {
                $event->order->updateLineItem($lineItem->id(), [
                    'metadata' => array_merge($lineItem->metadata()->toArray(), [
                        'license_key' => $licenseKey = LicenseKey::generate(),
                        'download_url' => URL::signedRoute('statamic.digital-downloads.download', [
                            'order_id' => $event->order->id,
                            'item_id' => $lineItem->id(),
                            'license_key' => $licenseKey,
                        ]),
                        'download_history' => [],
                    ]),
                ]);
            });

        if ($hasDownloads->count() >= 1 && $customer = $event->order->customer()) {
            event(new DigitalDownloadReady($event->order));
        }
    }
}
