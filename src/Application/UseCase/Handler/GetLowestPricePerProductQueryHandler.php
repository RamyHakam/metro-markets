<?php

namespace App\Application\UseCase\Handler;

use App\Application\DTO\PriceDTO;
use App\Application\Port\CurrentLowestPriceProviderInterface;
use App\Application\UseCase\Query\GetLowestPricePerProductQuery;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetLowestPricePerProductQueryHandler
{
    public function __construct(private CurrentLowestPriceProviderInterface $currentLowestPriceProvider)
    {}

    public function __invoke(GetLowestPricePerProductQuery $query): ?PriceDTO
    {
        $currentLowestPrice = $this->currentLowestPriceProvider->getCurrentLowestPrice($query->productId);

        if ($currentLowestPrice === null) {
            return  null;
        }
        return PriceDTO::fromDomain($currentLowestPrice);
    }
}
