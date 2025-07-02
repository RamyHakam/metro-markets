<?php

namespace App\Domain\Repository;

use App\Domain\Model\Price;
use App\Domain\ValueObject\ProductId;

interface PriceRepositoryInterface
{
    public function saveOrUpdate(Price $price): void;

    public function remove(Price $price): void;

    public function findLowestPriceByProductId(ProductId $productId): ?Price;

    public function findLowestPriceForAllProducts(): array;

    public  function  findProductIdsByOffset(int $offset, int $limit): array;

    public function findByProductId(ProductId $productId): ?Price;
}
