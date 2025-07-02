<?php

namespace App\Application\Port;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag]
interface CompetitorPriceClientInterface
{
    public function fetchProductPrices(int $productId): array;
}
