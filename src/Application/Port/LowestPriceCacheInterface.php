<?php

namespace App\Application\Port;

use App\Domain\Model\Price;

interface LowestPriceCacheInterface
{
    public function get(string $productId): ?Price;

    public function set(string $productId, Price $price): void;

}
