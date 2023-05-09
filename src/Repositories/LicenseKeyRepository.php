<?php

namespace DoubleThreeDigital\DigitalProducts\Repositories;

use DoubleThreeDigital\DigitalProducts\Contracts\LicenseKeyRepository as Contract;

class LicenseKeyRepository implements Contract
{
    protected $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    protected $length = 24;

    public function generate(): string
    {
        $key = '';

        for ($i = 0; $i < $this->length; $i++) {
            $key .= $this->characters[random_int(0, strlen($this->characters) - 1)];
        }

        return $key;
    }

    public static function bindings(): array
    {
        return [];
    }
}
