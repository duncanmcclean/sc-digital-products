<?php

namespace DoubleThreeDigital\DigitalProducts\Events;

use DoubleThreeDigital\SimpleCommerce\Contracts\Order;

class DigitalDownloadReady
{
    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
