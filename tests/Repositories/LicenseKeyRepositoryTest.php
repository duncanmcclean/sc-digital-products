<?php

namespace DoubleThreeDigital\DigitalProducts\Tests\Repositories;

use DoubleThreeDigital\DigitalProducts\Tests\TestCase;

class LicenseKeyRepositoryTest extends TestCase
{
    protected $repository;

    public function setUp(): void
    {
        $this->repository = resolve(LicenseKeyRepository::class);
    }

    /** @test */
    public function can_generate_license_key()
    {
        $key = $this->repository->generate();

        $this->assertIsString($key);
        $this->assertSame(24, strlen($key));
    }
}
