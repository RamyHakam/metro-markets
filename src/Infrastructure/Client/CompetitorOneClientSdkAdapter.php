<?php

namespace App\Infrastructure\Client;

use App\Application\Enum\CompetitorTypeEnum;
use App\Application\Port\CompetitorPriceClientInterface;
use DummySdk\DummySdkClient;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem(CompetitorTypeEnum::COMPETITOR_ONE->value)]
final readonly class CompetitorOneClientSdkAdapter implements  CompetitorPriceClientInterface
{

    public function __construct(
        private DummySdkClient                   $dummySdkClient,
    )
    {
    }

    /**
     * @throws \JsonException
     */
    public function fetchProductPrices(int $productId): array
    {
        // Simulate fetching product prices from a dummy SDK
        $response = $this->dummySdkClient->getPrices($productId);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Failed to fetch product prices from Dummy SDK');
        }

        return json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
    }
}
