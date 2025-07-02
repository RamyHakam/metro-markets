<?php

namespace App\Infrastructure\DataFixture\Factory;

use App\Application\Enum\CompetitorTypeEnum;
use App\Infrastructure\Persistence\Doctrine\Entity\CompetitorProductEntity;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<CompetitorProductEntity>
 */
final class CompetitorProductEntityFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return CompetitorProductEntity::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'competitorType' => self::faker()->randomElement(CompetitorTypeEnum::cases()),
            'price' => PriceEntityFactory::random()
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(CompetitorProductEntity $competitorProductEntity): void {})
        ;
    }
}
