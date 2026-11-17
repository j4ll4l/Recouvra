<?php

namespace App\Story;

use App\Entity\Client;
use App\Entity\Invoice;
use App\Factory\AuditLogFactory;
use App\Factory\ClientFactory;
use App\Factory\CompanyFactory;
use App\Factory\InvitationFactory;
use App\Factory\InvoiceExtractionFactory;
use App\Factory\InvoiceFactory;
use App\Factory\PaymentFactory;
use App\Factory\ReminderFactory;
use App\Factory\ReminderScenarioFactory;
use App\Factory\ReminderStepFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class PrimaryTenantStory extends Story
{
    public function build(): void
    {
        // 1. Entreprise et utilisateurs
        $company = CompanyFactory::createOne([
            'name' => 'Marc Plomberie',
            'email' => 'contact@marc-plomberie.fr',
        ]);

        $marc = UserFactory::new()->admin()->create([
            'company' => $company,
            'email' => 'marc@demo.fr',
            'firstName' => 'Marc',
            'lastName' => 'Durand',
        ]);

        $julie = UserFactory::createOne([
            'company' => $company,
            'email' => 'julie@demo.fr',
            'firstName' => 'Julie',
            'lastName' => 'Petit',
        ]);

        // 2. Scénario de relance et ses 3 étapes
        $scenario = ReminderScenarioFactory::createOne(['company' => $company]);

        $courteous = ReminderStepFactory::new()->courteous()->create(['scenario' => $scenario]);
        $firm = ReminderStepFactory::new()->firm()->create(['scenario' => $scenario]);
        ReminderStepFactory::new()->formalNotice()->create(['scenario' => $scenario]);

        // 3. Clients
        $tilleuls = ClientFactory::createOne(['company' => $company, 'name' => 'SCI Les Tilleuls']);
        $martin = ClientFactory::createOne(['company' => $company, 'name' => 'M. Martin']);
        $dupont = ClientFactory::createOne(['company' => $company, 'name' => 'Mme Dupont']);
        $rhone = ClientFactory::createOne([
            'company' => $company,
            'name' => 'Bâtiment Rhône SAS',
            'phone' => null,
        ]);

        // 4. Factures : une par statut
        $this->invoice('F-2026-0001', $martin, 70833, 85000, 'draft', '+30 days');
        $this->invoice('F-2026-0002', $dupont, 100000, 120000, 'issued', '+10 days');
        $this->invoice('F-2026-0003', $rhone, 200000, 240000, 'overdue', '-2 days');
        $inv0004 = $this->invoice('F-2026-0004', $tilleuls, 300000, 360000, 'reminded', '-20 days');
        $inv0005 = $this->invoice('F-2026-0005', $martin, 100000, 120000, 'paid', '-15 days');
        $inv0006 = $this->invoice('F-2026-0006', $rhone, 416667, 500000, 'disputed', '-40 days');
        $this->invoice('F-2026-0007', $dupont, 50000, 60000, 'written_off', '-120 days');

        // 5. Paiements
        $payment0004 = PaymentFactory::createOne([
            'invoice' => $inv0004,
            'amountCents' => 160000,
            'method' => 'transfer',
            'paidAt' => new \DateTimeImmutable('-8 days'),
        ]);

        PaymentFactory::createOne([
            'invoice' => $inv0005,
            'amountCents' => 120000,
            'method' => 'check',
            'paidAt' => new \DateTimeImmutable('-12 days'),
        ]);

        // 6. Relances envoyées
        $reminder0004 = ReminderFactory::createOne([
            'invoice' => $inv0004,
            'step' => $courteous,
            'subject' => 'Rappel facture F-2026-0004',
            'body' => 'Bonjour SCI Les Tilleuls, sauf erreur de notre part, la facture F-2026-0004 de 3 600 € est échue. Merci de procéder au règlement.',
            'sentAt' => new \DateTimeImmutable('-17 days'),
        ]);

        ReminderFactory::createOne([
            'invoice' => $inv0004,
            'step' => $firm,
            'subject' => 'Facture F-2026-0004 impayée',
            'body' => 'Bonjour SCI Les Tilleuls, malgré notre précédent rappel, la facture F-2026-0004 de 3 600 € reste impayée. Merci de régulariser sous 8 jours.',
            'sentAt' => new \DateTimeImmutable('-5 days'),
        ]);

        ReminderFactory::createOne([
            'invoice' => $inv0006,
            'step' => $courteous,
            'subject' => 'Rappel facture F-2026-0006',
            'body' => 'Bonjour Bâtiment Rhône SAS, sauf erreur de notre part, la facture F-2026-0006 de 5 000 € est échue. Merci de procéder au règlement.',
            'sentAt' => new \DateTimeImmutable('-37 days'),
        ]);

        ReminderFactory::new()->failed()->create([
            'invoice' => $inv0006,
            'step' => $firm,
            'subject' => 'Facture F-2026-0006 impayée',
            'body' => 'Bonjour Bâtiment Rhône SAS, malgré notre précédent rappel, la facture F-2026-0006 de 5 000 € reste impayée. Merci de régulariser sous 8 jours.',
        ]);

        // 7. Extractions IA
        InvoiceExtractionFactory::createOne([
            'company' => $company,
            'invoice' => $inv0004,
            'pdfPath' => 'uploads/invoices/f-2026-0004.pdf',
            'result' => [
                'number' => 'F-2026-0004',
                'client' => 'SCI Les Tilleuls',
                'amount_ht_cents' => 300000,
                'amount_ttc_cents' => 360000,
                'issued_at' => $inv0004->getIssuedAt()->format('Y-m-d'),
                'due_at' => $inv0004->getDueAt()->format('Y-m-d'),
            ],
        ]);
        InvoiceExtractionFactory::new()->pending()->create(['company' => $company]);
        InvoiceExtractionFactory::new()->failed()->create(['company' => $company]);

        // 8. Invitations
        InvitationFactory::createOne(['company' => $company, 'email' => 'paul@demo.fr']);
        InvitationFactory::new()->expired()->create(['company' => $company, 'email' => 'ancien@demo.fr']);

        // 9. Journal d'audit
        AuditLogFactory::createOne([
            'company' => $company,
            'actor' => $julie,
            'action' => 'invoice.created',
            'entityType' => 'Invoice',
            'entityId' => $inv0004->getId(),
            'data' => ['number' => 'F-2026-0004'],
        ]);

        AuditLogFactory::createOne([
            'company' => $company,
            'actor' => $julie,
            'action' => 'payment.created',
            'entityType' => 'Payment',
            'entityId' => $payment0004->getId(),
            'data' => ['invoice' => 'F-2026-0004', 'amount_cents' => 160000],
        ]);

        AuditLogFactory::createOne([
            'company' => $company,
            'actor' => $marc,
            'action' => 'invoice.status_changed',
            'entityType' => 'Invoice',
            'entityId' => $inv0006->getId(),
            'data' => ['from' => 'overdue', 'to' => 'disputed'],
        ]);

        AuditLogFactory::createOne([
            'company' => $company,
            'actor' => null,
            'action' => 'reminder.sent',
            'entityType' => 'Reminder',
            'entityId' => $reminder0004->getId(),
            'data' => ['step' => 'courteous'],
        ]);

        // 10. Objets réutilisables dans les tests
        $this->addState('company', $company);
        $this->addState('marc', $marc);
        $this->addState('julie', $julie);
        $this->addState('invoice0004', $inv0004);
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
