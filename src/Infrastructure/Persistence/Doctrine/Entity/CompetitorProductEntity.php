<?php

namespace App\Infrastructure\Persistence\Doctrine\Entity;

use App\Application\Enum\CompetitorTypeEnum;
use App\Domain\ValueObject\ProductId;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'competitor_product')]
class CompetitorProductEntity
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: "string", enumType: CompetitorTypeEnum::class)]
    public CompetitorTypeEnum $competitorType;

    #[ORM\ManyToOne(targetEntity: PriceEntity::class, inversedBy: 'supportedCompetitors')]
    public PriceEntity $price;


    public static function create(CompetitorTypeEnum $competitorType, PriceEntity $priceEntity): self
    {
        $competitorProductEntity = new self();
        $competitorProductEntity->competitorType = $competitorType;
        $competitorProductEntity->price = $priceEntity;
        return $competitorProductEntity;
    }
}

