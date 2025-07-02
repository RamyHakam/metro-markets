<?php

namespace App\Infrastructure\DataFixture;

use App\Infrastructure\DataFixture\Factory\CompetitorProductEntityFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CompetitorProductFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
         CompetitorProductEntityFactory::createMany(30);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            PriceFixture::class,
        ];
    }
}
