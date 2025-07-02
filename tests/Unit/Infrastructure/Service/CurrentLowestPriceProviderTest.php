<?php

namespace App\Tests\Unit\Infrastructure\Service;

use App\Infrastructure\Service\CurrentLowestPriceProvider;
use App\Application\Port\LowestPriceCacheInterface;
use App\Domain\Repository\PriceRepositoryInterface;
use App\Domain\ValueObject\ProductId;
use App\Domain\Model\Price;
use PHPUnit\Framework\TestCase;

class CurrentLowestPriceProviderTest extends TestCase
{
    private LowestPriceCacheInterface $cache;
    private PriceRepositoryInterface $repository;
    private CurrentLowestPriceProvider $provider;

    protected function setUp(): void
    {
        $this->cache = $this->createMock(LowestPriceCacheInterface::class);
        $this->repository = $this->createMock(PriceRepositoryInterface::class);
        $this->provider = new CurrentLowestPriceProvider(
            $this->cache,
            $this->repository
        );
    }

    public function testGetCurrentLowestPriceReturnsCacheValueWhenHit(): void
    {
        $productId = ProductId::create(10);
        $cachedPrice = $this->createMock(Price::class);

        $this->cache
            ->expects($this->once())
            ->method('get')
            ->with((string)$productId->getValue())
            ->willReturn($cachedPrice);

        // repository should not be called when cache hit
        $this->repository
            ->expects($this->never())
            ->method('findLowestPriceByProductId');

        $result = $this->provider->getCurrentLowestPrice($productId);
        $this->assertSame($cachedPrice, $result);
    }

    public function testGetCurrentLowestPriceFallsBackToRepositoryWhenCacheMiss(): void
    {
        $productId = ProductId::create(20);
        $repoPrice = $this->createMock(Price::class);

        $this->cache
            ->expects($this->once())
            ->method('get')
            ->with((string)$productId->getValue())
            ->willReturn(null);

        $this->repository
            ->expects($this->once())
            ->method('findLowestPriceByProductId')
            ->with($productId)
            ->willReturn($repoPrice);

        $result = $this->provider->getCurrentLowestPrice($productId);
        $this->assertSame($repoPrice, $result);
    }

    public function testGetCurrentLowestPriceReturnsNullWhenNoData(): void
    {
        $productId = ProductId::create(30);

        $this->cache
            ->expects($this->once())
            ->method('get')
            ->willReturn(null);

        $this->repository
            ->expects($this->once())
            ->method('findLowestPriceByProductId')
            ->with($productId)
            ->willReturn(null);

        $result = $this->provider->getCurrentLowestPrice($productId);
        $this->assertNull($result);
    }

    public function testGetCurrentLowestPriceForAllProductsReturnsRepositoryData(): void
    {
        $allPrices = [
            $this->createMock(Price::class),
            $this->createMock(Price::class)
        ];

        $this->repository
            ->expects($this->once())
            ->method('findLowestPriceForAllProducts')
            ->willReturn($allPrices);

        // cache should not be invoked for all-products
        $this->cache
            ->expects($this->never())
            ->method('get');

        $result = $this->provider->getCurrentLowestPriceForAllProducts();
        $this->assertSame($allPrices, $result);
    }
}

