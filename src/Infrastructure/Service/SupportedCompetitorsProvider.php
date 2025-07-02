<?php

namespace App\Infrastructure\Service;

use App\Application\Enum\CompetitorTypeEnum;
use App\Application\Port\SupportedCompetitorsProviderInterface;
use App\Domain\ValueObject\ProductId;
use App\Infrastructure\Persistence\Doctrine\Entity\PriceEntity;
use Doctrine\ORM\EntityManagerInterface;

final readonly class SupportedCompetitorsProvider implements SupportedCompetitorsProviderInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function addCompetitor(ProductId $id, CompetitorTypeEnum $competitorType): void
    {
        /* @var PriceEntity $priceEntity */
        $priceEntity = $this->entityManager->getRepository(PriceEntity::class)
            ->findOneBy(['productId' => $id->getValue()]);
        if (!$priceEntity) {
            return;
        }
        $priceEntity->addSupportedCompetitor($competitorType);

        $this->entityManager->persist($priceEntity);
        $this->entityManager->flush();
    }

    public function removeCompetitor(ProductId $id, CompetitorTypeEnum $competitorType): void
    {
        /* @var PriceEntity $priceEntity */
        $priceEntity = $this->entityManager->getRepository(PriceEntity::class)
            ->findOneBy(['productId' => $id->getValue()]);
        if (!$priceEntity) {
            return;
        }

        $priceEntity->removeSupportedCompetitor($competitorType);

        $this->entityManager->persist($priceEntity);
        $this->entityManager->flush();
    }
}
