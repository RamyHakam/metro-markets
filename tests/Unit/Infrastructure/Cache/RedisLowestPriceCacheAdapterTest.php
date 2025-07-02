<?php

namespace App\Tests\Unit\Infrastructure\Cache;

use App\Domain\Model\Price;
use App\Domain\ValueObject\PriceAmount;
use App\Domain\ValueObject\ProductId;
use App\Infrastructure\Cache\RedisLowestPriceCacheAdapter;
use App\Infrastructure\Cache\PriceCacheSerializer;
use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\CacheItemInterface;

class RedisLowestPriceCacheAdapterTest extends TestCase
{
    private CacheItemPoolInterface $cachePool;
    private PriceCacheSerializer $serializer;
    private RedisLowestPriceCacheAdapter $adapter;

    protected function setUp(): void
    {
        $this->cachePool = $this->createMock(CacheItemPoolInterface::class);
        $this->serializer = $this->createMock(PriceCacheSerializer::class);
        $this->adapter = new RedisLowestPriceCacheAdapter(
            $this->cachePool,
            $this->serializer
        );
    }

    public function testGetReturnsNullWhenCacheMiss(): void
    {
        $productId = '123';
        $cacheItem = $this->createMock(CacheItemInterface::class);
        $cacheItem->method('isHit')->willReturn(false);

        $this->cachePool
            ->expects($this->once())
            ->method('getItem')
            ->with($productId)
            ->willReturn($cacheItem);

        $result = $this->adapter->get($productId);
        $this->assertNull($result);
    }

    public function testGetReturnsDeserializedPriceOnHit(): void
    {
        $productId = '456';
        $serialized = 'serialized-price-data';
        $expectedPrice = new Price(
            ProductId::create(456),
            'vendor',
            PriceAmount::create(99.99),
            new DateTimeImmutable('2025-01-01T00:00:00Z')
        );

        $cacheItem = $this->createMock(CacheItemInterface::class);
        $cacheItem->method('isHit')->willReturn(true);
        $cacheItem->method('get')->willReturn($serialized);

        $this->cachePool
            ->expects($this->once())
            ->method('getItem')
            ->with($productId)
            ->willReturn($cacheItem);

        $this->serializer
            ->expects($this->once())
            ->method('deserialize')
            ->with($serialized)
            ->willReturn($expectedPrice);

        $result = $this->adapter->get($productId);
        $this->assertSame($expectedPrice, $result);
    }

    public function testGetReturnsNullOnThrowable(): void
    {
        $productId = '789';
        $this->cachePool
            ->method('getItem')
            ->willThrowException(new Exception('cache error'));

        $result = $this->adapter->get($productId);
        $this->assertNull($result);
    }

    public function testSetStoresSerializedPrice(): void
    {
        $productId = '321';
        $price = $this->createMock(Price::class);
        $serialized = 'price-data';

        $cacheItem = $this->createMock(CacheItemInterface::class);
        $cacheItem
            ->expects($this->once())
            ->method('set')
            ->with($serialized);

        $this->cachePool
            ->expects($this->once())
            ->method('getItem')
            ->with($productId)
            ->willReturn($cacheItem);

        $this->serializer
            ->expects($this->once())
            ->method('serialize')
            ->with($price)
            ->willReturn($serialized);

        $this->cachePool
            ->expects($this->once())
            ->method('save')
            ->with($cacheItem);

        $this->adapter->set($productId, $price);
    }

    public function testSetSilentlyIgnoresExceptions(): void
    {
        $productId = '654';
        $price = $this->createMock(Price::class);

        $this->cachePool
            ->expects($this->once())
            ->method('getItem')
            ->willThrowException(new \RuntimeException('fail'));

        $this->adapter->set($productId, $price);

        $this->addToAssertionCount(1);
    }
}
