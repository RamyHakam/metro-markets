<?php

namespace App\Application\UseCase\Handler;

use App\Application\DTO\PriceDTO;
use App\Application\Port\CurrentLowestPriceProviderInterface;
use App\Application\UseCase\Query\GetLowestPriceForAllProductsQuery;
use App\Application\UseCase\Query\GetLowestPricePerProductQuery;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetLowestPriceForAllProductsQueryHandler
{
    public function __construct(private CurrentLowestPriceProviderInterface $currentLowestPriceProvider)
    {
    }

    public function __invoke(GetLowestPriceForAllProductsQuery $query): array
    {
        $currentLowestPriceList = $this->currentLowestPriceProvider->getCurrentLowestPriceForAllProducts();

        if (empty($currentLowestPriceList)) {
            return [];
        }
        return array_map(
            fn($price) => PriceDTO::fromDomain($price),
            $currentLowestPriceList
        );
    }
}
