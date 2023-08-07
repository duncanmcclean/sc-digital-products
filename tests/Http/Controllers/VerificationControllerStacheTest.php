<?php

namespace DoubleThreeDigital\SimpleCommerce\Tests\Http\Controllers;

use DoubleThreeDigital\DigitalProducts\Facades\LicenseKey;
use DoubleThreeDigital\DigitalProducts\Tests\TestCase;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry;

class VerificationControllerStacheTest extends TestCase
{
    /** @test */
    public function can_get_verification_index()
    {
        $licenseKey = LicenseKey::generate();

        Collection::make('orders')->save();

        Entry::make()
            ->collection('orders')
            ->set('order_status', 'placed')
            ->set('payment_status', 'paid')
            ->set('items', [
                [
                    'metadata' => [
                        'license_key' => $licenseKey,
                    ],
                ],
            ])
            ->save();

        $this
            ->post('/api/sc-digital-downloads/verification', [
                'license_key' => $licenseKey,
            ])
            ->assertOk()
            ->assertJson([
                'license_key' => $licenseKey,
                'valid' => true,
            ]);
    }
}
