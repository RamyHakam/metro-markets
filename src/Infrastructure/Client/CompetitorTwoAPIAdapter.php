<?php

namespace App\Infrastructure\Client;

use App\Application\Enum\CompetitorTypeEnum;
use App\Application\Port\CompetitorPriceClientInterface;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


#[AsTaggedItem(CompetitorTypeEnum::COMPETITOR_TWO->value)]
class CompetitorTwoAPIAdapter implements  CompetitorPriceClientInterface
{
    private array $entries;

    public function __construct()
    {
        $dataFile = __DIR__ . '/products-prices.json';
        if (!is_readable($dataFile)) {
            throw new \RuntimeException(sprintf('Data file not found or unreadable: %s', $dataFile));
        }

        $json = file_get_contents($dataFile);
        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        if (!\is_array($decoded)) {
            throw new \RuntimeException('Invalid JSON structure in products-prices.json.');
        }
        $this->entries = $decoded;
    }

    public function fetchProductPrices(int $productId): array
    {
        foreach ($this->entries as $entry) {
            if (isset($entry['id']) && (int)$entry['id'] === $productId) {
                return $entry;
            }
        }
        throw new  NotFoundHttpException( sprintf('Product ID %d not found in data file.', $productId));
    }
}
