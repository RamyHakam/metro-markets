<?php

namespace App\Tests\Unit\Infrastructure\Service;

use App\Application\Enum\CompetitorTypeEnum;
use App\Domain\ValueObject\ProductId;
use App\Infrastructure\Persistence\Doctrine\Entity\PriceEntity;
use App\Infrastructure\Service\SupportedCompetitorsProvider;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;

class SupportedCompetitorsProviderTest extends TestCase
{
    private EntityManagerInterface $em;
    private ObjectRepository $repository;
    private SupportedCompetitorsProvider $provider;

    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->repository = $this->createMock(EntityRepository::class);
        $this->provider = new SupportedCompetitorsProvider($this->em);

        $this->em->method('getRepository')
            ->with(PriceEntity::class)
            ->willReturn($this->repository);
    }

    public function testAddCompetitorDoesNothingWhenEntityNotFound(): void
    {
        $id = ProductId::create(123);
        $competitor = CompetitorTypeEnum::COMPETITOR_ONE;

        $this->repository->method('findOneBy')->with(['productId' => 123])->willReturn(null);

        $this->em->expects($this->never())->method('persist');
        $this->em->expects($this->never())->method('flush');

        $this->provider->addCompetitor($id, $competitor);

        $this->assertTrue(true);
    }

    public function testAddCompetitorPersistsAndFlushesWhenEntityFound(): void
    {
        $id = ProductId::create(456);
        $competitor = CompetitorTypeEnum::COMPETITOR_TWO;

        $priceEntity = $this->createMock(PriceEntity::class);

        $priceEntity->expects($this->once())
            ->method('addSupportedCompetitor')
            ->with($competitor);


        $this->repository->method('findOneBy')->with(['productId' => 456])->willReturn($priceEntity);

        $this->em->expects($this->once())->method('persist')->with($priceEntity);
        $this->em->expects($this->once())->method('flush');

        $this->provider->addCompetitor($id, $competitor);
    }

    public function testRemoveCompetitorDoesNothingWhenEntityNotFound(): void
    {
        $id = ProductId::create(789);
        $competitor = CompetitorTypeEnum::COMPETITOR_ONE;

        $this->repository->method('findOneBy')->with(['productId' => 789])->willReturn(null);
        $this->em->expects($this->never())->method('persist');
        $this->em->expects($this->never())->method('flush');

        $this->provider->removeCompetitor($id, $competitor);
        $this->assertTrue(true);
    }

    public function testRemoveCompetitorPersistsAndFlushesWhenEntityFound(): void
    {
        $id = ProductId::create(321);
        $competitor = CompetitorTypeEnum::COMPETITOR_TWO;

        $priceEntity = $this->createMock(PriceEntity::class);

        $priceEntity->expects($this->once())
            ->method('removeSupportedCompetitor')
            ->with($competitor);

        $this->repository->method('findOneBy')->with(['productId' => 321])->willReturn($priceEntity);

        $this->em->expects($this->once())->method('persist')->with($priceEntity);
        $this->em->expects($this->once())->method('flush');

        $this->provider->removeCompetitor($id, $competitor);
    }
}

