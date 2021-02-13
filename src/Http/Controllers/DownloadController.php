<?php

namespace DoubleThreeDigital\DigitalProducts\Http\Controllers;

use DoubleThreeDigital\SimpleCommerce\Facades\Order;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Statamic\Facades\Entry;

class DownloadController extends Controller
{
    public function show(Request $request)
    {
        if (! $request->hasValidSignature()) {
            abort(401);
        }

        $order = Order::find($request->order_id);
        $item = $order->orderItems()->firstWhere('id', $request->item_id);

        if ($item['license_key'] !== $request->license_key) {
            abort(401);
        }

        $product = Entry::find($item['product']);
        $asset = $product->toAugmentedArray()['downloadable_asset']->value();

        $download = Storage::disk($asset->container()->toArray()['disk'])->get($asset->path());

        $order->updateOrderItem($item['id'], [
            'download_history' => array_merge(
                [
                    [
                        'timestamp'  => now()->timestamp,
                        'ip_address' => $request->ip(),
                    ],
                ],
                isset($item['download_history']) ? $item['download_history'] : [],
            ),
        ]);

        return response($download)
            ->header('Content-Type', Storage::disk($asset->container()->toArray()['disk'])->mimeType($asset->path()))
            ->header('Content-Length', strlen($download));
    }
}
