<?php

namespace App\Infrastructure\DataFixture;

use App\Infrastructure\DataFixture\Factory\PriceEntityFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PriceFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
         PriceEntityFactory::createMany(3);
        $manager->flush();
    }
}
