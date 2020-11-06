<?php

namespace DoubleThreeDigital\DigitalProducts\Facades;

use DoubleThreeDigital\DigitalProducts\Contracts\LicenseKeyRepository;
use Illuminate\Support\Facades\Facade;

class LicenseKey extends Facade
{
    protected static function getFacadeAccessor()
    {
        return LicenseKeyRepository::class;
    }
}
