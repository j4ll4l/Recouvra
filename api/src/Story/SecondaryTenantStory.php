<?php

namespace App\Story;

use App\Entity\Client;
use App\Entity\Invoice;
use App\Factory\ClientFactory;
use App\Factory\CompanyFactory;
use App\Factory\InvoiceFactory;
use App\Factory\PaymentFactory;
use App\Factory\ReminderFactory;
use App\Factory\ReminderScenarioFactory;
use App\Factory\ReminderStepFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;

/**
 * Deuxième entreprise : sert surtout à tester l'isolation entre entreprises.
 */
final class SecondaryTenantStory extends Story
{
    public function build(): void
    {
        // 1. Entreprise et utilisateur
        $company = CompanyFactory::createOne([
            'name' => 'Élec Pro',
            'email' => 'contact@elecpro.fr',
        ]);

        $sophie = UserFactory::new()->admin()->create([
            'company' => $company,
            'email' => 'sophie@elecpro.fr',
            'firstName' => 'Sophie',
            'lastName' => 'Bernard',
        ]);

        // 2. Scénario de relance et ses 3 étapes
        $scenario = ReminderScenarioFactory::createOne(['company' => $company]);

        $courteous = ReminderStepFactory::new()->courteous()->create(['scenario' => $scenario]);
        ReminderStepFactory::new()->firm()->create(['scenario' => $scenario]);
        ReminderStepFactory::new()->formalNotice()->create(['scenario' => $scenario]);

        // 3. Clients
        // Même nom que chez Marc Plomberie, mais fiche distincte (une par entreprise)
        $tilleuls = ClientFactory::createOne(['company' => $company, 'name' => 'SCI Les Tilleuls']);
        $garage = ClientFactory::createOne(['company' => $company, 'name' => 'Garage Lyonnais']);

        // 4. Factures
        // Même numéro que chez Marc Plomberie
        $inv0001 = $this->invoice('F-2026-0001', $tilleuls, 150000, 180000, 'overdue', '-5 days');
        $inv0002 = $this->invoice('F-2026-0002', $garage, 80000, 96000, 'paid', '-10 days');

        // 5. Paiement
        PaymentFactory::createOne([
            'invoice' => $inv0002,
            'amountCents' => 96000,
            'method' => 'transfer',
            'paidAt' => new \DateTimeImmutable('-11 days'),
        ]);

        // 6. Relance envoyée
        ReminderFactory::createOne([
            'invoice' => $inv0001,
            'step' => $courteous,
            'subject' => 'Rappel facture F-2026-0001',
            'body' => 'Bonjour SCI Les Tilleuls, sauf erreur de notre part, la facture F-2026-0001 de 1 800 € est échue. Merci de procéder au règlement.',
            'sentAt' => new \DateTimeImmutable('-2 days'),
        ]);

        // 7. Objets réutilisables dans les tests
        $this->addState('company', $company);
        $this->addState('sophie', $sophie);
        $this->addState('invoice0001', $inv0001);
    }

    /**
     * Crée une facture dont l'échéance est relative à aujourd'hui.
     * La date d'émission est toujours 30 jours avant l'échéance.
     */
    private function invoice(
        string $number,
        Client $client,
        int $htCents,
        int $ttcCents,
        string $status,
        string $dueIn,
    ): Invoice {
        $dueAt = new \DateTimeImmutable($dueIn);

        return InvoiceFactory::createOne([
            'client' => $client,
            'number' => $number,
            'amountHtCents' => $htCents,
            'amountTtcCents' => $ttcCents,
            'status' => $status,
            'dueAt' => $dueAt,
            'issuedAt' => $dueAt->modify('-30 days'),
        ]);
    }
}
