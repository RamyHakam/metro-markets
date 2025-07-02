<?php

namespace App\Application\UseCase\Handler;

use App\Application\Port\CompetitorPriceClientInterface;
use App\Application\Port\ProductPricesDataMapperInterface;
use App\Application\UseCase\Command\FetchCompetitorPricesMessage;
use App\Application\UseCase\Command\ProcessFetchedPriceMessage;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final  readonly class  FetchCompetitorPricesHandler
{
    public function __construct(
        #[AutowireLocator(CompetitorPriceClientInterface::class)]
        private ContainerInterface  $competitorPriceClientContainer,
        #[AutowireLocator(ProductPricesDataMapperInterface::class)]
        private ContainerInterface  $competitorPriceDataMapperContainer,
        private MessageBusInterface $bus,
        #[Target('fetchPricesLogger')]
        private  LoggerInterface $fetchPricesLogger
    )
    {
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws ExceptionInterface
     */
    public function __invoke(FetchCompetitorPricesMessage $message): void
    {
             $productId = $message->productId;
             $competitorType = $message->source;
            /** @var CompetitorPriceClientInterface $competitorPriceClient */
            $competitorPriceClient = $this->competitorPriceClientContainer->get($competitorType->value);

            $this->fetchPricesLogger->info(
                'Fetching prices for product {productId} from competitor {competitorType}',
                [
                    'productId' => $productId->getValue(),
                    'competitorType' => $competitorType->value,
                ]
            );
            $productPricesList = $competitorPriceClient->fetchProductPrices($productId->getValue());

            if (empty($productPricesList)) {
                $this->fetchPricesLogger->info(
                    'No prices found for product {productId} from competitor {competitorType}',
                    [
                        'productId' => $productId->getValue(),
                        'competitorType' => $competitorType->value,
                    ]
                );
                return;
            }

            /** @var ProductPricesDataMapperInterface $dataMapper */
            $dataMapper = $this->competitorPriceDataMapperContainer->get($message->source->value);
            $productPricesDTO = $dataMapper->map($productId->getValue(), $productPricesList);

            $this->bus->dispatch(new ProcessFetchedPriceMessage($productPricesDTO));
    }
}
