<?php

namespace DoubleThreeDigital\DigitalDownloads\Contracts;

interface LicenseKeyRepository
{
    public function generate(): string;
}