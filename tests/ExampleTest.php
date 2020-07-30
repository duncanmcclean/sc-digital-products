<?php

namespace DoubleThreeDigital\DigitalProducts\Tests;

use DoubleThreeDigital\DigitalProducts\Listeners\AddFieldsToProductBlueprint;
use Statamic\Events\EntryBlueprintFound;
use Statamic\Facades\Blueprint;

class ExampleText extends TestCase
{
    /** @test */
    public function it_can_return_true()
    {
        $this->assertTrue(true);
    }
    
    /** @test */
    // public function fields_get_added_to_blueprint()
    // {
    //     $blueprint = Blueprint::find('product');
    //     $event = new EntryBlueprintFound($blueprint, null);

    //     dd($event);

    //     $listener = new AddFieldsToProductBlueprint();
    //     $listener->handle($event);
    // }
}