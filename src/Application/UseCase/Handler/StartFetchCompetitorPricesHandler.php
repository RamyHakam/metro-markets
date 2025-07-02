<?php

namespace App\Application\UseCase\Handler;

use App\Application\UseCase\Command\FetchCompetitorPricesMessage;
use App\Application\UseCase\Command\StartFetchCompetitorPricesMessage;
use App\Domain\Repository\PriceRepositoryInterface;
use App\Infrastructure\Persistence\Doctrine\Entity\CompetitorProductEntity;
use App\Infrastructure\Persistence\Doctrine\Entity\PriceEntity;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final  readonly class StartFetchCompetitorPricesHandler
{
    public function __construct(
        private PriceRepositoryInterface $productRepo,
        private MessageBusInterface      $bus,
        #[Target('schedulerLogger')]
        private  LoggerInterface $schedulerLogger,
        private int                      $batchSize = 400,

    ) {}

    public function __invoke(StartFetchCompetitorPricesMessage $msg): void
    {
        $this->schedulerLogger->info('Starting to fetch competitor prices for all products.');
        $offset = 0;
        do {
            /** @var PriceEntity[] $productIdSlice */
            $productIdSlice = $this->productRepo->findProductIdsByOffset($offset, $this->batchSize);

            if (empty($productIdSlice)) {
                $this->schedulerLogger->info('No more products to process for fetching competitor prices.');
                break;
            }

            foreach ( $productIdSlice as $product) {
                foreach ($product->getSupportedCompetitors() as $competitor) {
                    $this->schedulerLogger->info(
                        'Dispatching FetchCompetitorPricesMessage for product id =  {productId} and competitor type =  {competitorType}',
                        [
                            'productId' => $product->toDomain()->productId->getValue(),
                            'competitorType' => $competitor->competitorType->value,
                        ]
                    );
                    /* @var CompetitorProductEntity $competitor */
                    $this->bus->dispatch(new FetchCompetitorPricesMessage(
                        source: $competitor->competitorType,
                        productId: $product->toDomain()->productId,
                    ));
                }
            }
            $offset += $this->batchSize;
            $this->schedulerLogger->info(
                'Processed {count} products, moving to next batch with offset {offset}',
                [
                    'count' => count($productIdSlice),
                    'offset' => $offset,
                ]
            );
        } while (!empty($productIdSlice));
    }
}
