<?php

namespace DoubleThreeDigital\DigitalProducts\Http\Controllers;

use DoubleThreeDigital\SimpleCommerce\Facades\Order;
use DoubleThreeDigital\SimpleCommerce\Facades\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    public function show(Request $request)
    {
        if (! $request->hasValidSignature()) {
            abort(401);
        }

        $order = Order::find($request->order_id);
        $item = $order->lineItems()->firstWhere('id', $request->item_id);

        if (! isset($item['metadata']['license_key']) || $item['metadata']['license_key'] !== $request->license_key) {
            abort(401);
        }

        $product = Product::find($item['product']);
        $asset = $product->toAugmentedArray()['downloadable_asset']->value();

        $download = Storage::disk($asset->container()->toArray()['disk'])->get($asset->path());

        if (isset($item['download_history']) && $product->has('download_limit')) {
            if (collect($item['download_history'])->count() >= $product->get('download_limit')) {
                abort(405, "You've reached the download limit for this product.");
            }
        }

        $order->updateLineItem($item['id'], [
            'metadata' => array_merge([
                'download_history' => array_merge(
                    [
                        [
                            'timestamp'  => now()->timestamp,
                            'ip_address' => $request->ip(),
                        ],
                    ],
                    isset($item['metadata']['download_history']) ? $item['metadata']['download_history'] : [],
                ),
            ], Arr::get($item, 'metadata', [])),
        ]);

        return response($download)
            ->header('Content-Type', Storage::disk($asset->container()->toArray()['disk'])->mimeType($asset->path()))
            ->header('Content-Length', strlen($download));
    }
}
