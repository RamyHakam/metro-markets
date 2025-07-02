<?php

namespace App\Application\UseCase\Handler;


use App\Application\Port\CurrentLowestPriceProviderInterface;
use App\Application\Port\LowestPriceUpdaterInterface;
use App\Application\Port\PriceSelectorInterface;
use App\Application\UseCase\Command\ProcessFetchedPriceMessage;
use App\Domain\ValueObject\ProductId;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final  readonly class ProcessFetchedPriceHandler
{
    public function __construct(
        private PriceSelectorInterface              $priceSelector,
        private CurrentLowestPriceProviderInterface $currentLowestPriceProvider,
        private LowestPriceUpdaterInterface         $lowestPriceUpdater,
        #[Target('processPricesLogger')]
        private  LoggerInterface $processPricesLogger
    )
    {
    }

    public function __invoke(ProcessFetchedPriceMessage $message): void
    {
        // ToDO: check if this competitor is supported for this product then add it to the list of competitors

        $this->processPricesLogger->info(
            'Processing fetched prices for product {productId}',
            [
                'productId' => $message->productPricesDTO->productId,
            ]
        );

        $productPricesDTO = $message->productPricesDTO;

        $lowestNewDTO = $this->priceSelector->getLowest(
            $productPricesDTO->pricesDTOS
        );

        if ($lowestNewDTO === null) {
            return;
        }
        $productIdVo = ProductId::create($productPricesDTO->productId);
        $currentLowestPrice = $this->currentLowestPriceProvider->getCurrentLowestPrice($productIdVo);

        $this->processPricesLogger->info(
            'Current lowest price for product {productId} is {currentPrice} and new lowest price selected  is {newPrice}',
            [
                'productId' => $productPricesDTO->productId,
                'currentPrice' => $currentLowestPrice?->priceAmount->getValue() ?? 'none',
                'newPrice' => $lowestNewDTO->price,
            ]
        );

        if ($currentLowestPrice === null || $lowestNewDTO->price < $currentLowestPrice->priceAmount->getValue()) {

            $this->processPricesLogger->info(
                'Updating lowest price for product {productId} with new price {newPrice} from vendor {vendor}',
                [
                    'productId' => $productPricesDTO->productId,
                    'newPrice' => $lowestNewDTO->price,
                    'vendor' => $lowestNewDTO->vendorName,
                ]
            );
            $this->lowestPriceUpdater->updateIfLower($productIdVo, $lowestNewDTO,
                $productPricesDTO->fetchedAt
            );
        }
    }
}
