<?php

namespace DoubleThreeDigital\DigitalProducts\UpdateScripts;

use DoubleThreeDigital\SimpleCommerce\Facades\Product;
use Statamic\Facades\Blueprint;
use Statamic\UpdateScripts\UpdateScript;

class FixDigitalProductToggleForVariantProducts extends UpdateScript
{
    public function shouldUpdate($newVersion, $oldVersion)
    {
        return $this->isUpdatingTo('5.1.0');
    }

    public function update()
    {
        collect(Product::all())
            ->filter(fn ($entry) => $entry->get('is_digital_product') === true)
            ->filter(fn ($entry) => $entry->has('product_variants'))
            ->each(function ($entry) {
                $productVariants = $entry->get('product_variants');

                $entry
                    ->merge([
                        'is_digital_product' => null,
                        'product_variants' => [
                            'variants' => $productVariants['variants'],
                            'options' => collect($productVariants['options'])
                                ->map(function ($option) {
                                    $option['is_digital_product'] = true;

                                    return $option;
                                })
                                ->values()
                                ->toArray(),
                        ],
                    ])
                    ->save();
            });

        Blueprint::in('collections.products')
            ->filter(fn ($blueprint) => $blueprint->hasField('is_digital_product'))
            ->filter(fn ($blueprint) => $blueprint->hasField('product_variants'))
            ->each(function ($blueprint) {
                $blueprint->removeTab('Digital Product')->save();
            });
    }
}
