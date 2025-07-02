<?php

namespace App\Application\UseCase\Command;
use App\Application\Enum\CompetitorTypeEnum;
use App\Domain\ValueObject\ProductId;
use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('fetch_transport')]
final  readonly  class  FetchCompetitorPricesMessage
{
    public function __construct(
        public CompetitorTypeEnum $source,
        public  ProductId  $productId,
    ) {}
}
