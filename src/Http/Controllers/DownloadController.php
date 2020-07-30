<?php

namespace DoubleThreeDigital\DigitalProducts\Http\Controllers;

use DoubleThreeDigital\SimpleCommerce\Facades\Cart;
use Illuminate\Http\Request;
use Statamic\Facades\Entry;

class DownloadController
{
    public function show(Request $request)
    {
        if (! $request->hasValidSignature()) {
            abort(401);
        }

        $orderId = $request->order_id;
        $itemId = $request->item_id;
        $licenseKey = $request->license_key;

        $order = Cart::find($orderId);

        $item = collect($order->data['items'])
            ->where('id', $itemId)
            ->first();

        if ($item['license_key'] != $request->license_key) {
            abort(401);
        }

        $product = Entry::find($item['product']);

        // TODO: figure out way to protect the file path
        return redirect($product->toAugmentedArray()['downloadable_asset']->value()->absoluteUrl());
    }
}