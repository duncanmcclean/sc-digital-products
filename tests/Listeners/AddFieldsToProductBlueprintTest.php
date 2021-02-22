<?php

namespace DoubleThreeDigital\DigitalProducts\Tests\Listeners;

use DoubleThreeDigital\DigitalProducts\Listeners\AddFieldsToProductBlueprint;
use DoubleThreeDigital\DigitalProducts\Tests\TestCase;
use Statamic\Events\EntryBlueprintFound;
use Statamic\Facades\Blueprint;

class AddFieldsToProductBlueprintTest extends TestCase
{
    /** @test */
    public function fields_can_be_addded_to_product_blueprint()
    {
        $blueprint = Blueprint::make('product')
            ->setNamespace('collections.products')
            ->save();

        $event = new EntryBlueprintFound($blueprint);

        $handle = (new AddFieldsToProductBlueprint())->handle($event);

        $this->assertTrue($handle->hasField('is_digital_product'));
        $this->assertTrue($handle->hasField('download_limit'));
        $this->assertTrue($handle->hasField('downloadable_asset'));
        $this->assertTrue($handle->hasSection('Digital Product'));
    }

    /** @test */
    public function fields_are_not_added_to_orders_blueprint()
    {
        $blueprint = Blueprint::make('orders')
            ->setNamespace('collections.orders')
            ->save();

        $event = new EntryBlueprintFound($blueprint);

        $handle = (new AddFieldsToProductBlueprint())->handle($event);

        $this->assertFalse($handle->hasField('is_digital_product'));
        $this->assertFalse($handle->hasField('download_limit'));
        $this->assertFalse($handle->hasField('downloadable_asset'));
        $this->assertFalse($handle->hasSection('Digital Product'));
    }
}
