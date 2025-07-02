<?php

namespace App\Application\DTO;

use DateTimeImmutable;
use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints as Assert;

final  readonly  class ProductPricesDTO
{
    public function __construct(
        #[SerializedName('productId')]
        #[Groups(['product:prices'])]
        #[Assert\Blank(message: 'Product ID cannot be blank.')]
        public int    $productId,

        #[Groups(['product:prices'])]
        /** @var  PriceDTO [] */
        public array  $pricesDTOS,

        #[SerializedName('fetchedAt')]
        #[Context([
            DateTimeNormalizer::FORMAT_KEY   => 'Y-m-d H:i:s',
            DateTimeNormalizer::TIMEZONE_KEY => 'UTC',
        ])]
        #[Groups(['price:import', 'price:export'])]
        public DateTimeImmutable $fetchedAt,
    )
    {
    }
}
