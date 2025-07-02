<?php

namespace App\Infrastructure\Client;

use App\Application\Enum\CompetitorTypeEnum;
use App\Application\Port\CompetitorPriceClientInterface;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;


#[AsTaggedItem(CompetitorTypeEnum::COMPETITOR_TWO->value)]
class CompetitorTwoAPIAdapter implements  CompetitorPriceClientInterface
{
    public function fetchProductPrices(int $productId): array
    {
        {
            return [
                'id' => $productId,
                'competitor_data' => [
                    ['name'   => 'VendorOne', 'amount' => 20.49],
                    ['name'   => 'VendorTwo', 'amount' => 18.99],
                ],
            ];
        }
    }
}
