<?php

namespace  App\Application\DTO;

use App\Domain\Model\Price;
use DateTimeImmutable;
use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

final  readonly class PriceDTO
{
    public function __construct(
        #[SerializedName('productId')]
        #[Groups(['price:import', 'price:export'])]
        public int                $productId,

        #[Groups(['price:import', 'price:export'])]
        public string             $vendorName,

        #[Groups(['price:import', 'price:export'])]
        public float              $price,

        #[Groups(['price:export'])]
        #[SerializedName('fetchedAt')]
        #[Context([
            DateTimeNormalizer::FORMAT_KEY   => 'Y-m-d H:i:s',
            DateTimeNormalizer::TIMEZONE_KEY => 'UTC',
        ])]
        public DateTimeImmutable $fetchedAt,
    ) {}

    public static function fromDomain(Price $price): self
    {
        return new self(
            productId: $price->productId->getValue(),
            vendorName: $price->vendorName,
            price: $price->priceAmount->getValue(),
            fetchedAt: $price->fetchedAt
        );
    }
}
