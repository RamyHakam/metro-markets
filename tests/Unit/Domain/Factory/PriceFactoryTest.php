<?php

namespace App\Tests\Unit\Domain\Factory;

use App\Domain\Factory\PriceFactory;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class PriceFactoryTest extends TestCase
{
    public function testCreateCorrectPrice(): void
    {
        $dt = new DateTimeImmutable('2025-07-02T12:34:56Z');
        $price = PriceFactory::create('2', 'VendorY', 123.456, $dt);

        $this->assertSame(2, $price->productId->getValue());
        $this->assertSame('VendorY', $price->vendorName);
        $this->assertSame(123.46, $price->priceAmount->getValue());
        $this->assertSame($dt, $price->fetchedAt);
    }
}
