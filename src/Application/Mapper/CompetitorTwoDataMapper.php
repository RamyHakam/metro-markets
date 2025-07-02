<?php

namespace App\Application\Mapper;


use App\Application\DTO\PriceDTO;
use App\Application\DTO\ProductPricesDTO;
use App\Application\Enum\CompetitorTypeEnum;
use App\Application\Port\ProductPricesDataMapperInterface;
use DateTimeImmutable;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem(CompetitorTypeEnum::COMPETITOR_TWO->value)]
final  readonly class CompetitorTwoDataMapper implements  ProductPricesDataMapperInterface
{
    private  const  PRICES_KEY = 'competitor_data';
    private const  PRODUCT_ID_KEY = 'id';
    private  const  VENDOR_NAME_KEY = 'name';
    private const  PRICE_KEY = 'amount';
    public function map(int $productId, array $productPricesList): ProductPricesDTO
    {
        $prices = [];
        foreach ($productPricesList[self::PRICES_KEY] as $item) {

            $prices[] = new PriceDTO(
                productId: (int) $productPricesList[self::PRODUCT_ID_KEY],
                vendorName: $item[self::VENDOR_NAME_KEY],
                price: (float) $item[self::PRICE_KEY],
                fetchedAt: new DateTimeImmutable(),
            );
        }

        return new ProductPricesDTO(
            productId: (int) $productPricesList[self::PRODUCT_ID_KEY],
            pricesDTOS: $prices,
            fetchedAt: new DateTimeImmutable(),
        );
    }
}
