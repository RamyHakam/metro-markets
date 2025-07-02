<?php

namespace App\Tests\Unit\Domain\ValueObject;

use App\Domain\ValueObject\PriceAmount;
use PHPUnit\Framework\TestCase;

class PriceAmountTest extends TestCase
{
    public function testCreateRoundsToTwoDecimals(): void
    {
        $pa1 = PriceAmount::create(10.005);
        $this->assertSame(10.01, $pa1->getValue());

        $pa2 = PriceAmount::create(10.004);
        $this->assertSame(10.00, $pa2->getValue());
    }

    public function testEqualsComparesValue(): void
    {
        $pa1 = PriceAmount::create(20.00);
        $pa2 = PriceAmount::create(20.00);
        $pa3 = PriceAmount::create(30.00);

        $this->assertTrue($pa1->equals($pa2));
        $this->assertFalse($pa1->equals($pa3));
    }
}

