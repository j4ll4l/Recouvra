<?php

namespace App\Factory;

use App\Entity\Reminder;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Reminder>
 */
final class ReminderFactory extends PersistentObjectFactory
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
        return Reminder::class;
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
            'invoice' => InvoiceFactory::new(),
            'step' => ReminderStepFactory::new(),
            'subject' => 'Rappel facture F-2026-0042',
            'body' => 'Bonjour, sauf erreur de notre part, la facture F-2026-0042 de 3 600 € est échue depuis le 1er décembre. Merci de procéder au règlement.',
            'status' => 'sent',
            'sentAt' => new \DateTimeImmutable('-2 days'),
        ];
    }

    public function pending(): static
    {
        return $this->with([
            'status' => 'pending',
            'sentAt' => null,
        ]);
    }

    public function failed(): static
    {
        return $this->with([
            'status' => 'failed',
            'sentAt' => null,
        ]);
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Reminder $reminder): void {})
        ;
    }
}
