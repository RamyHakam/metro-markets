<?php

namespace App\Domain\Model;

use App\Domain\ValueObject\PriceAmount;
use App\Domain\ValueObject\ProductId;
use DateTimeImmutable;

final readonly class Price
{
    public function __construct(
        public  ProductId $id,
        public  string $vendorName,
        public PriceAmount $price,
        public  DateTimeImmutable $fetchedAt
    )
    {
    }

}
