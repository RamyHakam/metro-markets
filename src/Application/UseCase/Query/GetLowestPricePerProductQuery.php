<?php

namespace App\Application\UseCase\Query;

use App\Domain\ValueObject\ProductId;
use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('sync')]
final readonly class GetLowestPricePerProductQuery
{
    public function __construct( public ProductId $productId)
    {
    }
}
