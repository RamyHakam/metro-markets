<?php

namespace App\Infrastructure\Service;

use App\Application\DTO\PriceDTO;
use App\Application\Port\PriceSelectorInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;

#[AsAlias(PriceSelectorInterface::class)]
final  readonly  class LowestPriceSelector implements PriceSelectorInterface
{
    public function getLowest(array $priceDTOs): ?PriceDTO
    {
        return array_reduce(
            $priceDTOs,
            fn(?PriceDTO $carry, PriceDTO $p) =>
            $carry === null || $p->price < $carry->price
                ? $p
                : $carry,
            null
        );
    }
}
