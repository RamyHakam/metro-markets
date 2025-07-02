<?php

namespace App\Domain\Factory;

use App\Domain\Model\Price;
use App\Domain\ValueObject\PriceAmount;
use App\Domain\ValueObject\ProductId;
use DateTimeImmutable;

final readonly  class PriceFactory
{
    public  static  function create(
        string $rawProductId,
        string $vendorName,
        float  $rawAmount,
        DateTimeImmutable $fetchedAt
    ): Price {
        $productId   = ProductId::create($rawProductId);
        $amount      = PriceAmount::create($rawAmount);

        return new Price(
            productId:  $productId,
            vendorName: $vendorName,
            priceAmount:     $amount,
            fetchedAt:  $fetchedAt
        );
    }
}
