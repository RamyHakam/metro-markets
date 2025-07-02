<?php

namespace App\Infrastructure\DataFixture;

use App\Infrastructure\DataFixture\Factory\PriceEntityFactory;
use App\Infrastructure\DataFixture\Factory\UserEntityFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // default API keys
        UserEntityFactory::createOne(
            [
                'email' => 'test@test',
                'apiKey' => $_ENV['API_KEY']
            ]
        );
         UserEntityFactory::createMany(10);
        $manager->flush();
    }
}
