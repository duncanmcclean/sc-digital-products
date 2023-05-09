<?php

namespace DoubleThreeDigital\DigitalProducts\Listeners;

use DoubleThreeDigital\SimpleCommerce\SimpleCommerce;
use Statamic\Events\EntryBlueprintFound;

class AddFieldsToProductBlueprint
{
    public function handle(EntryBlueprintFound $event)
    {
        if (! isset(SimpleCommerce::productDriver()['collection'])) {
            return $event->blueprint;
        }

        if ($event->blueprint->namespace() !== 'collections.'.SimpleCommerce::productDriver()['collection']) {
            return $event->blueprint;
        }

        $event->blueprint->ensureField('is_digital_product', [
            'type' => 'toggle',
            'display' => 'Is Digital Product?',
        ], 'Digital Product');

        if ($event->blueprint->hasField('product_variants')) {
            $productVariantsField = $event->blueprint->field('product_variants');

            $event->blueprint->ensureFieldHasConfig(
                'product_variants',
                array_merge(
                    $productVariantsField->toArray(),
                    [
                        'option_fields' => array_merge(
                            $productVariantsField->get('option_fields', []),
                            collect($this->getDigitalProductFields())
                                ->map(function ($value, $key) {
                                    return [
                                        'handle' => $key,
                                        'field' => $value,
                                    ];
                                })
                                ->values()
                                ->toArray()
                        ),
                    ]
                )
            );

            return $event->blueprint;
        }

        collect($this->getDigitalProductFields())->each(function ($value, $key) use (&$event) {
            $event->blueprint->ensureField($key, $value, 'Digital Product');
        });

        return $event->blueprint;
    }

    protected function getDigitalProductFields()
    {
        return [
            'download_limit' => [
                'type' => 'integer',
                'display' => 'Download Limit',
                'instructions' => "If you'd like to limit the amount if times this product can be downloaded, set it here. Keep it blank if you'd like it to be unlimited.",
            ],
            'downloadable_asset' => [
                'type' => 'assets',
                'mode' => 'grid',
                'display' => 'Downloadable Asset',
                'if' => [
                    'is_digital_product' => 'equals true',
                ],
            ],
        ];
    }
}
