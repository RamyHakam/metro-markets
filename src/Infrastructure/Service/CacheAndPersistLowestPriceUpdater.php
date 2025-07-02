<?php

namespace App\Infrastructure\Service;

use App\Application\DTO\PriceDTO;
use App\Application\Port\LowestPriceCacheInterface;
use App\Application\Port\LowestPriceUpdaterInterface;
use App\Domain\Factory\PriceFactory;
use App\Domain\Repository\PriceRepositoryInterface;
use App\Domain\ValueObject\ProductId;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\Target;

#[AsAlias(LowestPriceUpdaterInterface::class)]
final  readonly class CacheAndPersistLowestPriceUpdater implements  LowestPriceUpdaterInterface
{
    public function __construct(
        private PriceRepositoryInterface  $priceRepository,
        private LowestPriceCacheInterface $priceCache,
        #[Target('updatePricesLogger')]
        private LoggerInterface           $logger,
    ) {}

    public function updateIfLower(
        ProductId $productId,
        PriceDTO            $candidate,
        DateTimeImmutable  $fetchedAt
    ): void {

        $newPrice = PriceFactory::create(
            $productId->getValue(),
            $candidate->vendorName,
            $candidate->price,
            $fetchedAt
        );

        $this->logger->info(
            'Updating cached and db entry lowest price for product {productId} with the new price {price} from vendor {vendor}',
            [
                'productId' => $productId->getValue(),
                'price' => $newPrice->priceAmount->getValue(),
                'vendor' => $newPrice->vendorName,
            ]
        );
        $this->priceRepository->saveOrUpdate($newPrice);
        $this->priceCache->set($productId->getValue(), $newPrice);
    }
}
