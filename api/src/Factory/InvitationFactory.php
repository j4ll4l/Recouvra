<?php

namespace App\Factory;

use App\Entity\Invitation;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Invitation>
 */
final class InvitationFactory extends PersistentObjectFactory
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
        return Invitation::class;
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
            'email' => self::faker()->safeEmail(),
            'role' => 'ROLE_COLLABORATOR',
            'tokenHash' => hash('sha256', self::faker()->unique()->uuid()),
            'expiresAt' => new \DateTimeImmutable('+7 days'),
            'acceptedAt' => null,
        ];
    }

    public function accepted(): static
    {
        return $this->with([
            'acceptedAt' => new \DateTimeImmutable('-1 day'),
        ]);
    }

    public function expired(): static
    {
        return $this->with([
            'expiresAt' => new \DateTimeImmutable('-1 day'),
        ]);
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Invitation $invitation): void {})
        ;
    }
}
