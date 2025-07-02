<?php

namespace App\Infrastructure\Persistence\Doctrine\Entity;

use App\Application\Enum\CompetitorTypeEnum;
use App\Domain\Model\Price;
use App\Domain\ValueObject\PriceAmount;
use App\Domain\ValueObject\ProductId;
use App\Infrastructure\Persistence\Doctrine\Repository\DoctrinePriceRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctrinePriceRepository::class)]
#[ORM\Table(name: 'prices')]
class PriceEntity
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'product_id', type: 'integer')]
    private int $productId;

    #[ORM\Column(name: 'vendor_name', type: 'string')]
    private string $vendorName;

    #[ORM\Column(name: 'price', type: 'decimal', precision: 10, scale: 2)]
    private float $price;

    #[ORM\Column(name: 'fetched_at', type: 'datetime_immutable')]
    private DateTimeImmutable $fetchedAt;

    #[ORM\OneToMany(targetEntity: CompetitorProductEntity::class, mappedBy: 'price', cascade: ['persist'])]
    #[ORM\JoinTable(name: 'product_price_competitors')]
    private Collection $supportedCompetitors;

    public function __construct()
    {
        $this->supportedCompetitors = new ArrayCollection();
    }

    public static function fromDomain(Price $price): self
    {
        $priceEntity = new self();
        $priceEntity->productId  = $price->productId->getValue();
        $priceEntity->vendorName = $price->vendorName;
        $priceEntity->price      = $price->priceAmount->getValue();
        $priceEntity->fetchedAt  = $price->fetchedAt;
        return $priceEntity;
    }

    public function toDomain(): Price
    {
        return new Price(
            ProductId::create($this->productId),
            $this->vendorName,
            PriceAmount::create($this->price),
            $this->fetchedAt
        );
    }

    public function getSupportedCompetitors(): array
    {
        return $this->supportedCompetitors->toArray();
    }

    public function removeSupportedCompetitor(CompetitorTypeEnum $competitorType): void
    {
        foreach ($this->supportedCompetitors as $cp) {
            if ($cp->competitorType === $competitorType) {
                $this->supportedCompetitors->removeElement($cp);
                break;
            }
        }
    }

    public function addSupportedCompetitor(CompetitorTypeEnum $competitorType): void
    {
        $competitorProductEntity = CompetitorProductEntity::create(
            competitorType: $competitorType,
            priceEntity: $this
        );
        if (!$this->supportedCompetitors->contains($competitorProductEntity)) {
            $this->supportedCompetitors->add($competitorProductEntity);
        }
    }
    public function getId(): int
    {
        return $this->id;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getVendorName(): string
    {
        return $this->vendorName;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getFetchedAt(): DateTimeImmutable
    {
        return $this->fetchedAt;
    }

    public function setProductId(int $productId): void
    {
        $this->productId = $productId;
    }

    public function setVendorName(string $vendorName): void
    {
        $this->vendorName = $vendorName;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function setFetchedAt(DateTimeImmutable $fetchedAt): void
    {
        $this->fetchedAt = $fetchedAt;
    }




}
