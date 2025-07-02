<?php

namespace App\Tests\Unit\Infrastructure\Service;

use App\Application\DTO\PriceDTO;
use App\Infrastructure\Service\LowestPriceSelector;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;

class LowestPriceSelectorTest extends TestCase
{
    private LowestPriceSelector $selector;

    protected function setUp(): void
    {
        $this->selector = new LowestPriceSelector();
    }

    public function testGetLowestReturnsNullWhenEmpty(): void
    {
        $result = $this->selector->getLowest([]);
        $this->assertNull($result);
    }

    public function testGetLowestReturnsSingleElement(): void
    {
        $dto = new PriceDTO(
            productId: 1,
            vendorName: 'VendorA',
            price: 10.00,
            fetchedAt: new DateTimeImmutable('2025-01-01T00:00:00Z')
        );

        $result = $this->selector->getLowest([$dto]);
        $this->assertSame($dto, $result);
    }

    public function testGetLowestPicksLowestPrice(): void
    {
        $dto1 = new PriceDTO(
            productId: 1,
            vendorName: 'VendorA',
            price: 15.00,
            fetchedAt: new DateTimeImmutable('2025-01-01T00:00:00Z')
        );
        $dto2 = new PriceDTO(
            productId: 1,
            vendorName: 'VendorB',
            price: 12.50,
            fetchedAt: new DateTimeImmutable('2025-01-01T00:00:00Z')
        );
        $dto3 = new PriceDTO(
            productId: 1,
            vendorName: 'VendorC',
            price: 20.00,
            fetchedAt: new DateTimeImmutable('2025-01-01T00:00:00Z')
        );

        $list = [$dto1, $dto2, $dto3];
        $result = $this->selector->getLowest($list);

        $this->assertSame($dto2, $result);
    }

    public function testGetLowestHandlesSamePriceKeepsFirst(): void
    {
        $dto1 = new PriceDTO(
            productId: 1,
            vendorName: 'VendorA',
            price: 10.00,
            fetchedAt: new DateTimeImmutable('2025-01-01T00:00:00Z')
        );
        $dto2 = new PriceDTO(
            productId: 1,
            vendorName: 'VendorB',
            price: 10.00,
            fetchedAt: new DateTimeImmutable('2025-01-01T00:00:00Z')
        );

        $list = [$dto1, $dto2];
        $result = $this->selector->getLowest($list);

        $this->assertSame($dto1, $result);
    }
}
