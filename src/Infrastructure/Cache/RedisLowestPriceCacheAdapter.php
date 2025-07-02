<?php

namespace App\Infrastructure\Cache;

use App\Application\Port\LowestPriceCacheInterface;
use App\Domain\Model\Price;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\Target;

#[AsAlias(LowestPriceCacheInterface::class)]
final  readonly class RedisLowestPriceCacheAdapter implements LowestPriceCacheInterface
{
    public function __construct(
        #[Target('lowest_price_cache')]
        private CacheItemPoolInterface $lowestPriceCache,
        private PriceCacheSerializer   $serializer)
    {
    }

    public function get(string $productId): ?Price
    {
        try {
            $cacheItem = $this->lowestPriceCache->getItem($productId);

            if (!$cacheItem->isHit()) {
                return null;
            }
            return $this->serializer->deserialize($cacheItem->get());
        } catch (\Throwable $e) {
            // ToDO: Handle exception, e.g., log it
            return null;
        }
    }

    public function set(string $productId,  Price $price): void
    {
        try {
            $cacheItem = $this->lowestPriceCache->getItem($productId);
            $cacheItem->set($this->serializer->serialize($price));
            $this->lowestPriceCache->save($cacheItem);
        } catch (\Throwable $e) {
            // ToDO: Handle exception, e.g., log it
        }
    }
}
