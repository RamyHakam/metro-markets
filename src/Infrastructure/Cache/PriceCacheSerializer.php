<?php

namespace App\Infrastructure\Cache;

use App\Application\DTO\PriceDTO;
use App\Domain\Factory\PriceFactory;
use App\Domain\Model\Price;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

final  readonly class PriceCacheSerializer
{
    public function __construct(
        private SerializerInterface $serializer)
    {
    }

    /**
     * @throws ExceptionInterface
     */
    public function serialize(Price $price): string
    {
        $dto = PriceDTO::fromDomain($price);

        return $this->serializer->serialize(
            $dto,
            'json',
            ['groups' => ['price:export']]
        );
    }

    /**
     * @throws ExceptionInterface
     */
    public function deserialize(string $json): Price
    {

        /** @var PriceDTO $dto */
        $dto = $this->serializer->deserialize(
            $json,
            PriceDTO::class,
            'json',
            ['groups' => ['price:export']]
        );

       return  PriceFactory::create(
            $dto->productId,
            $dto->vendorName,
            $dto->price,
            $dto->fetchedAt
        );
    }
}
