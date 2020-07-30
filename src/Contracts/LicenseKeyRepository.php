<?php

namespace DoubleThreeDigital\DigitalProducts\Contracts;

interface LicenseKeyRepository
{
    public function generate(): string;
}