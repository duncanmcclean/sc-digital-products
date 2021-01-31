<?php

namespace DoubleThreeDigital\SimpleCommerce\Tests\Http;

use DoubleThreeDigital\DigitalProducts\Facades\LicenseKey;
use DoubleThreeDigital\DigitalProducts\Tests\TestCase;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry as FacadesEntry;

class VerificationControllerTest extends TestCase
{
    /** @test */
    public function can_get_verification_index()
    {
        $licenseKey = LicenseKey::generate();

        $collection = Collection::make('orders')->save();

        $order = FacadesEntry::make()
            ->collection('orders')
            ->set('is_paid', true)
            ->set('items', [
                [
                    'license_key' => $licenseKey,
                ],
            ])
            ->save();

        $response = $this->postJson('/api/sc-digital-downloads/verification', [
            'license_key' => $licenseKey,
        ]);

        $response->assertOk();
        $response->assertJson([
            'license_key' => $licenseKey,
            'valid' => true,
        ]);
    }
}
