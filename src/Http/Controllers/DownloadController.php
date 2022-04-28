<?php

namespace DoubleThreeDigital\DigitalProducts\Http\Controllers;

use DoubleThreeDigital\SimpleCommerce\Facades\Order;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Statamic\Assets\Asset;
use ZipArchive;

class DownloadController extends Controller
{
    public function show(Request $request)
    {
        $order = Order::find($request->order_id);
        $item = $order->lineItems()->firstWhere('id', $request->item_id);
        $product = $item->product();

        if (! $item->metadata()->has('license_key') || $item->metadata()->get('license_key') !== $request->license_key) {
            abort(401);
        }

        if (! $product->has('downloadable_asset')) {
            throw new \Exception("Product [{$product->id()}] does not have any digital downloadable assets.");
        }

        $zip = new ZipArchive;
        $zip->open(storage_path("{$order->id()}__{$item->id()}__{$product->id()}.zip"), ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $product->toAugmentedArray()['downloadable_asset']->value()->get()
            ->each(function (Asset $asset) use ($request, $order, $item, $product, &$zip) {
                if (config('sc-digital-products.download_history')) {
                    if ($item->metadata()->has('download_history') && $product->has('download_limit')) {
                        if (collect($item->datadata()->get('download_history'))->count() >= $product->get('download_limit')) {
                            abort(405, "You've reached the download limit for this product.");
                        }
                    }

                    $order->updateLineItem($item->id(), [
                        'metadata' => array_merge($item->metadata()->toArray(), [
                            'download_history' => array_merge([
                                [
                                    'timestamp'  => now()->timestamp,
                                    'ip_address' => $request->ip(),
                                ],
                            ], $item->metadata()->get('download_history', [])),
                        ]),
                    ]);
                }

                $zip->addFile($asset->resolvedPath(), "{$product->get('slug')}/{$asset->basename()}");
            });

        $zip->close();

        return response()->download(storage_path("{$order->id()}__{$item->id()}__{$product->id()}.zip"), "{$product->get('slug')}.zip");
    }
}
