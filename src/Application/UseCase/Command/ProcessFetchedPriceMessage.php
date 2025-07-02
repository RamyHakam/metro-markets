<?php

namespace App\Application\UseCase\Command;

use App\Application\DTO\ProductPricesDTO;
use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('process_transport')]
class ProcessFetchedPriceMessage
{
    public function __construct(public ProductPricesDTO $productPricesDTO) {}

}

