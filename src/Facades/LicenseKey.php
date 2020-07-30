<?php

namespace DoubleThreeDigital\DigitalDownloads\Facades;

use Illuminate\Support\Facades\Facade;

class LicenseKey extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'LicenseKey';
    }
}