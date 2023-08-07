<?php

namespace DoubleThreeDigital\SimpleCommerce\Tests\Http\Controllers;

use DoubleThreeDigital\DigitalProducts\Facades\LicenseKey;
use DoubleThreeDigital\DigitalProducts\Tests\Helpers\UseDatabaseContentDrivers;
use DoubleThreeDigital\DigitalProducts\Tests\TestCase;
use DoubleThreeDigital\SimpleCommerce\Orders\OrderModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Statamic\Facades\Collection;

class VerificationControllerEloquentTest extends TestCase
{
    use RefreshDatabase;
    use UseDatabaseContentDrivers;

    /** @test */
    public function can_get_verification_index()
    {
        $licenseKey = LicenseKey::generate();

        Collection::make('orders')->save();

        OrderModel::create([
            'order_status' => 'placed',
            'payment_status' => 'paid',
            'items' => [
                [
                    'metadata' => [
                        'license_key' => $licenseKey,
                    ],
                ],
            ],
        ]);

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
