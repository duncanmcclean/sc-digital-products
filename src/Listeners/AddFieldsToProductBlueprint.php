<?php

namespace DoubleThreeDigital\DigitalProducts\Listeners;

use DoubleThreeDigital\SimpleCommerce\Products\Product;
use DoubleThreeDigital\SimpleCommerce\SimpleCommerce;
use Statamic\Events\EntryBlueprintFound;

class AddFieldsToProductBlueprint
{
    public function handle(EntryBlueprintFound $event)
    {
        if (SimpleCommerce::productDriver()['driver'] !== Product::class) {
            return $event->blueprint;
        }

        if ($event->blueprint->namespace() !== 'collections.'.SimpleCommerce::productDriver()['collection']) {
            return $event->blueprint;
        }

        $event->blueprint->ensureField('is_digital_product', [
            'type' => 'toggle',
            'display' => 'Is Digital Product?',
        ], 'Digital Product');

        $event->blueprint->ensureField('download_limit', [
            'type' => 'integer',
            'display' => 'Download Limit',
            'instructions' => "If you'd like to limit the amount if times this product can be downloaded, set it here. Keep it blank if you'd like it to be unlimited.",
        ], 'Digital Product');

        $event->blueprint->ensureField('downloadable_asset', [
            'type' => 'assets',
            'mode' => 'grid',
            'display' => 'Downloadable Asset',
            'if' => [
                'is_digital_product' => 'equals true',
            ],
        ], 'Digital Product');

        return $event->blueprint;
    }
}
