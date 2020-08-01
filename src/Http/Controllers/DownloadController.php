<?php

namespace DoubleThreeDigital\DigitalProducts\Http\Controllers;

use DoubleThreeDigital\SimpleCommerce\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Statamic\Facades\Entry;

class DownloadController
{
    public function show(Request $request)
    {
        if (! $request->hasValidSignature()) {
            abort(401);
        }

        $order = Cart::find($request->order_id);

        $item = collect($order->data['items'])
            ->where('id', $request->item_id)
            ->first();

        if ($item['license_key'] != $request->license_key) {
            abort(401);
        }

        $product = Entry::find($item['product']);
        $asset = $product->toAugmentedArray()['downloadable_asset']->value();

        $download = Storage::disk($asset->container()->toArray()['disk'])->get($asset->path());

        return response($download)
            ->header('Content-Type', Storage::disk($asset->container()->toArray()['disk'])->mimeType($asset->path()))
            ->header('Content-Length', strlen($download));
    }
}
