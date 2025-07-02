<?php

namespace App\Tests\Unit\Domain\ValueObject;

use App\Domain\ValueObject\ProductId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ProductIdTest extends TestCase
{
    public function testCreateWithValidId(): void
    {
        $id = ProductId::create(5);
        $this->assertSame(5, $id->getValue());
        $this->assertTrue($id->equals(ProductId::create(5)));
        $this->assertFalse($id->equals(ProductId::create(6)));
    }

    public function testCreateWithZeroThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ProductId::create(0);
    }

    public function testCreateWithNegativeThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ProductId::create(-1);
    }
}
