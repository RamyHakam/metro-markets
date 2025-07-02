<?php

namespace App\Application\Port;

use App\Domain\Model\Price;
use App\Domain\ValueObject\ProductId;

interface CurrentLowestPriceProviderInterface
{
    public function getCurrentLowestPrice(ProductId $id): ?Price;

    public function getCurrentLowestPriceForAllProducts(): array;
}
