<?php

namespace App\Infrastructure\Service;

use App\Application\Port\CurrentLowestPriceProviderInterface;
use App\Application\Port\LowestPriceCacheInterface;
use App\Domain\Model\Price;
use App\Domain\Repository\PriceRepositoryInterface;
use App\Domain\ValueObject\ProductId;

final  readonly  class CurrentLowestPriceProvider implements CurrentLowestPriceProviderInterface
{
    public function __construct(
        private LowestPriceCacheInterface $priceCache,
        private PriceRepositoryInterface $priceRepository
    ) {}

    public function getCurrentLowestPrice(ProductId $id): ?Price
    {
        return $this->priceCache->get($id->getValue())
            ?? $this->priceRepository->findLowestPriceByProductId($id);
    }

    public function getCurrentLowestPriceForAllProducts(): array
    {
        return $this->priceRepository->findLowestPriceForAllProducts();
    }
}
