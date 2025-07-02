<?php

namespace App\Infrastructure\DataFixture\Factory;

use App\Infrastructure\Persistence\Doctrine\Entity\PriceEntity;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<PriceEntity>
 */
final class PriceEntityFactory extends PersistentProxyObjectFactory
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
        return PriceEntity::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'productId' => self::faker()->numberBetween(0, 200),
            'price' => self::faker()->randomFloat(2, 10, 100),
            'vendorName' => self::faker()->company() . ' - init-from-faker' ,
            'fetchedAt' => self::faker()->dateTimeThisMonth(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(PriceEntity $priceEntity): void {})
        ;
    }
}
