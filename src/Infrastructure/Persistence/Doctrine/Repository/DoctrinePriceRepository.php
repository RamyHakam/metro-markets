<?php

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Model\Price;
use App\Domain\Repository\PriceRepositoryInterface;
use App\Domain\ValueObject\ProductId;
use App\Infrastructure\Persistence\Doctrine\Entity\PriceEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\Lazy;

#[AsAlias(PriceRepositoryInterface::class)]
#[AsTaggedItem('app.repository.price')]
#[Lazy]
class DoctrinePriceRepository extends ServiceEntityRepository implements PriceRepositoryInterface
{
    public function __construct(private readonly ManagerRegistry $registry)
    {
        parent::__construct($registry, PriceEntity::class);
    }

    public function saveOrUpdate(Price $price): void
    {
        // Check if the entity already exists in the database
        $existingEntity = $this->findOneBy(['productId' => $price->productId->getValue()]);
        if ($existingEntity) {
            // If it exists, update the existing entity
            $existingEntity->setAmount($price->priceAmount->getValue());
            $existingEntity->setVendorName($price->vendorName);
            $existingEntity->setFetchedAt($price->fetchedAt);
            $this->registry->getManager()->persist($existingEntity);
        } else {
            // If it does not exist, create a new entity
            $this->registry->getManager()->persist(PriceEntity::fromDomain($price));
        }
        $this->registry->getManager()->flush();
    }

    public function remove(Price $price): void
    {
        $priceEntity = PriceEntity::fromDomain($price);
        $this->registry->getManager()->remove($priceEntity);
        $this->registry->getManager()->flush();
    }

    public function findLowestPriceByProductId(ProductId $productId): ?Price
    {
        $entity = $this->registry
            ->getManager()
            ->createQueryBuilder()
            ->select('p')
            ->from(PriceEntity::class, 'p')
            ->where('p.productId = :pid')
            ->setParameter('pid', $productId->getValue())
            ->orderBy('p.price', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $entity ? $entity->toDomain() : null;
    }

    public function findLowestPriceForAllProducts(): array
    {
        // ToDo Add Pagination  like Pagerfanta to handle large datasets
        $productLowestPrices = $this->registry->getRepository(PriceEntity::class)->findAll();
        if (empty($productLowestPrices)) {
            return [];
        }
        return  array_map(
            fn(PriceEntity $entity) => $entity->toDomain(),
            $productLowestPrices
        );
    }

    public function findProductIdsByOffset(int $offset, int $limit): array
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->select('p')
            ->setFirstResult($offset)
            ->setMaxResults($limit);
        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }

    public function findByProductId(ProductId $productId): ?Price
    {
        $entity = $this->findOneBy(['productId' => $productId->getValue()]);
        return $entity ? $entity->toDomain() : null;
    }
}
