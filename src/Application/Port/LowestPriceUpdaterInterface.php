<?php

namespace App\Application\Port;

use App\Application\DTO\PriceDTO;
use App\Domain\ValueObject\ProductId;
use DateTimeImmutable;

interface LowestPriceUpdaterInterface
{
    public function updateIfLower(
        ProductId $productId,
        PriceDTO $candidate,
        DateTimeImmutable $fetchedAt
    ): void;

}
