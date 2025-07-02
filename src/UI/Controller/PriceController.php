<?php

namespace App\UI\Controller;
use App\Application\DTO\PriceDTO;
use App\Application\UseCase\Query\GetLowestPriceForAllProductsQuery;
use App\Application\UseCase\Query\GetLowestPricePerProductQuery;
use App\Domain\ValueObject\ProductId;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route(path: '/api/price')]
class PriceController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $queryBus
    ) {}

    #[Route('/{id}', name: 'get_price_by_id', requirements: ['id' => '\d+'],
        methods: ['GET'])]
    public function getPrice(int $id): JsonResponse
    {
        $envelope = $this->queryBus->dispatch(new GetLowestPricePerProductQuery(ProductId::create($id)));
        // get the handler response
        $handledStamp = $envelope->last(HandledStamp::class);
        if (!$handledStamp) {
            return new JsonResponse(['message' => 'Handler not found'], 404);
        }
        // fetch the result from the handler response
        /** @var PriceDTO|null $dto */
        $lowestPriceDto = $handledStamp->getResult();
        if (!$lowestPriceDto) {
            return new JsonResponse(['message' => 'No Price found for this product'], 404);
        }

        return $this->json(
            $lowestPriceDto,
            200,
            [],
            ['groups' => ['price:export']]
        );
    }

    #[Route('', methods:['GET'])]
    public function getAll(): JsonResponse
    {
        $envelope = $this->queryBus->dispatch(new GetLowestPriceForAllProductsQuery());
        $stamp    = $envelope->last(HandledStamp::class);

        if (!$stamp) {
            // ToDo: handle the case when the handler is not found
            // ToDo: log the error or throw an exception
            return new JsonResponse(['message' => 'Handler not found'], 404);
        }

        return $this->json(
            $stamp?->getResult(),
            200,
            [],
            ['groups' => ['price:export']]
        );
    }



}
