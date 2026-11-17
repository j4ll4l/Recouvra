<?php

namespace App\Factory;

use App\Entity\Invoice;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Invoice>
 */
final class InvoiceFactory extends PersistentObjectFactory
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
        return Invoice::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    #[\Override]
    protected function defaults(): array|callable
    {
        $amountHt = self::faker()->numberBetween(20000, 800000);
        $issuedAt = \DateTimeImmutable::createFromMutable(
            self::faker()->dateTimeBetween('-20 days', 'now')
        );

        return [
            'client' => ClientFactory::new(),
            'number' => self::faker()->unique()->numerify('F-2026-####'),
            'amountHtCents' => $amountHt,
            'amountTtcCents' => (int) round($amountHt * 1.2),
            'status' => 'issued',
            'issuedAt' => $issuedAt,
            'dueAt' => $issuedAt->modify('+30 days'),
            'pdfPath' => null,
        ];
    }

    public function overdue(): static
    {
        $issuedAt = new \DateTimeImmutable('-60 days');

        return $this->with([
            'status' => 'overdue',
            'issuedAt' => $issuedAt,
            'dueAt' => $issuedAt->modify('+30 days'),
        ]);
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this->afterInstantiate(function (Invoice $invoice): void {
            if (null === $invoice->getCompany()) {
                $invoice->setCompany($invoice->getClient()->getCompany());
            }
        });
    }
}
