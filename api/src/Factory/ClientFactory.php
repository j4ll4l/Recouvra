<?php

namespace App\Factory;

use App\Entity\Client;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Client>
 */
final class ClientFactory extends PersistentObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    #[\Override]
    public static function class(): string
    {
        return Client::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'company' => CompanyFactory::new(),
            'name' => self::faker()->company(),
            'email' => self::faker()->safeEmail(),
            'phone' => self::faker()->numerify('0# ## ## ## ##'),
            'siret' => self::faker()->numerify('##############'),
            'address' => self::faker()->address(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Client $client): void {})
        ;
    }
}
