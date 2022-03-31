<?php

namespace DoubleThreeDigital\DigitalProducts\Http\Controllers;

use DoubleThreeDigital\SimpleCommerce\Facades\Order;
use DoubleThreeDigital\SimpleCommerce\Facades\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Statamic\Assets\Asset;
use ZipArchive;

class DownloadController extends Controller
{
    public function show(Request $request)
    {
        $order = Order::find($request->order_id);
        $item = $order->lineItems()->firstWhere('id', $request->item_id);
        $product = Product::find($item['product']);

        if (! isset($item['metadata']['license_key']) || $item['metadata']['license_key'] !== $request->license_key) {
            abort(401);
        }

        if (! $product->has('downloadable_asset')) {
            throw new \Exception("Product [{$product->id()}] does not have any digital downloadable assets.");
        }

        $zip = new ZipArchive;
        $zip->open(storage_path("{$order->id()}__{$item['id']}__{$product->id()}.zip"), ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $product->toAugmentedArray()['downloadable_asset']->value()->get()
            ->each(function (Asset $asset) use ($request, $order, $item, $product, &$zip) {
                if (config('sc-digital-products.download_history')) {
                    if (isset($item['metadata']['download_history']) && $product->has('download_limit')) {
                        if (collect($item['metadata']['download_history'])->count() >= $product->get('download_limit')) {
                            abort(405, "You've reached the download limit for this product.");
                        }
                    }

                    $order->updateLineItem($item['id'], [
                        'metadata' => array_merge(Arr::get($item, 'metadata', []), [
                            'download_history' => array_merge(
                                [
                                    [
                                        'timestamp'  => now()->timestamp,
                                        'ip_address' => $request->ip(),
                                    ],
                                ],
                                isset($item['metadata']['download_history']) ? $item['metadata']['download_history'] : [],
                            ),
                        ]),
                    ]);
                }

                $zip->addFile($asset->resolvedPath(), "{$product->slug()}/{$asset->basename()}");
            });

        $zip->close();

        return response()->download(storage_path("{$order->id()}__{$item['id']}__{$product->id()}.zip"), "{$product->slug()}.zip");
    }
}
