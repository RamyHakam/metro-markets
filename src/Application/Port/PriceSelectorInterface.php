<?php

namespace App\Application\Port;

use App\Application\DTO\PriceDTO;

interface PriceSelectorInterface
{
    public function getLowest(array $priceDTOs): ?PriceDTO;

}
