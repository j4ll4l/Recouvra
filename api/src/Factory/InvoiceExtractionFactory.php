<?php

namespace App\Factory;

use App\Entity\InvoiceExtraction;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<InvoiceExtraction>
 */
final class InvoiceExtractionFactory extends PersistentObjectFactory
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
        return InvoiceExtraction::class;
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
            'pdfPath' => 'uploads/invoices/f-2026-0042.pdf',
            'status' => 'done',
            'result' => [
                'number' => 'F-2026-0042',
                'client' => 'SCI Les Tilleuls',
                'amount_ht_cents' => 300000,
                'amount_ttc_cents' => 360000,
                'issued_at' => '2026-11-01',
                'due_at' => '2026-12-01',
            ],
            'error' => null,
        ];
    }

    public function pending(): static
    {
        return $this->with([
            'status' => 'pending',
            'result' => null,
        ]);
    }

    public function failed(): static
    {
        return $this->with([
            'status' => 'failed',
            'result' => null,
            'error' => 'Impossible de lire le PDF : le texte est illisible.',
        ]);
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(InvoiceExtraction $invoiceExtraction): void {})
        ;
    }
}
