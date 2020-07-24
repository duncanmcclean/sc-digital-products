<?php

namespace DoubleThreeDigital\DigitalDownloads\Support;

class LicenseKeyGenerator
{
    protected $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    protected $length = 24;

    public function generate()
    {
        $key = '';

        for ($i = 0; $i < $this->length; $i++) {
            $key .= $this->characters[random_int(0, strlen($this->characters) - 1)];
        }

        return $key;
    }
}