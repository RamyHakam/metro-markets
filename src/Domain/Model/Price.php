<?php

namespace App\Domain\Model;

use App\Domain\ValueObject\PriceAmount;
use App\Domain\ValueObject\ProductId;
use DateTimeImmutable;

final readonly class Price
{
    public function __construct(
        public  ProductId         $productId,
        public  string            $vendorName,
        public PriceAmount        $priceAmount,
        public  DateTimeImmutable $fetchedAt
    )
    {
    }
}
