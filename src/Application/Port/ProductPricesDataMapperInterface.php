<?php

namespace App\Application\Port;

use App\Application\DTO\ProductPricesDTO;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag]
interface ProductPricesDataMapperInterface
{
    /**
     * @return ProductPricesDTO
     */
    public function map(int $productId, array $productPricesList): ProductPricesDTO;
}
