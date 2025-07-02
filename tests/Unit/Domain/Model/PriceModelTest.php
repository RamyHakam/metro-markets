<?php

namespace App\Tests\Unit\Domain\Model;

use App\Domain\Model\Price;
use App\Domain\ValueObject\PriceAmount;
use App\Domain\ValueObject\ProductId;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class PriceModelTest extends TestCase
{
    public function testPriceProperties(): void
    {
        $pid = ProductId::create(1);
        $amount = PriceAmount::create(99.99);
        $dt = new DateTimeImmutable('2025-07-01T00:00:00Z');

        $price = new Price($pid, 'VendorX', $amount, $dt);

        $this->assertSame($pid, $price->productId);
        $this->assertSame('VendorX', $price->vendorName);
        $this->assertSame($amount, $price->priceAmount);
        $this->assertSame($dt, $price->fetchedAt);
    }
}
