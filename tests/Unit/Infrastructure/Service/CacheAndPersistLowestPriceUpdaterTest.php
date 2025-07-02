<?php

namespace App\Tests\Unit\Infrastructure\Service;

use App\Application\DTO\PriceDTO;
use App\Domain\Model\Price;
use App\Domain\ValueObject\ProductId;
use App\Infrastructure\Service\CacheAndPersistLowestPriceUpdater;
use App\Application\Port\LowestPriceCacheInterface;
use App\Domain\Repository\PriceRepositoryInterface;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;

class CacheAndPersistLowestPriceUpdaterTest extends TestCase
{
    private PriceRepositoryInterface $repo;
    private LowestPriceCacheInterface $cache;
    private LoggerInterface $logger;
    private CacheAndPersistLowestPriceUpdater $updater;

    protected function setUp(): void
    {
        $this->repo = $this->createMock(PriceRepositoryInterface::class);
        $this->cache = $this->createMock(LowestPriceCacheInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->updater = new CacheAndPersistLowestPriceUpdater(
            $this->repo,
            $this->cache,
            $this->logger
        );
    }

    public function testUpdateIfLowerPersistsAndCachesAndLogs(): void
    {
        $productId = ProductId::create(42);
        $dto = new PriceDTO(
            productId: 42,
            vendorName: 'TestVendor',
            price: 123.45,
            fetchedAt: new DateTimeImmutable('2025-07-02T12:00:00Z')
        );
        $fetchedAt = new DateTimeImmutable('2025-07-02T12:00:00Z');

        $this->logger
            ->expects($this->once())
            ->method('info')
            ->with(
                'Updating cached and db entry lowest price for product {productId} with the new price {price} from vendor {vendor}',
                $this->callback(function (array $context) use ($productId, $dto) {
                    return isset($context['productId'], $context['price'], $context['vendor'])
                        && $context['productId'] === $productId->getValue()
                        && $context['price'] === $dto->price
                        && $context['vendor'] === $dto->vendorName;
                })
            );

        $this->repo
            ->expects($this->once())
            ->method('saveOrUpdate')
            ->with($this->callback(function ($price) use ($productId, $dto, $fetchedAt) {
                return $price->productId->getValue() === $productId->getValue()
                    && $price->vendorName === $dto->vendorName
                    && $price->priceAmount->getValue() === $dto->price
                    && $price->fetchedAt == $fetchedAt;
            }));

        $this->cache
            ->expects($this->once())
            ->method('set')
            ->with(
                (string) $productId->getValue(),
                $this->isInstanceOf(Price::class)
            );

        $this->updater->updateIfLower($productId, $dto, $fetchedAt);
    }
}

