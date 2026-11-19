<?php

namespace App\Tests\Api;

use App\Factory\AuditLogFactory;
use App\Factory\ClientFactory;
use App\Factory\CompanyFactory;
use App\Factory\InvoiceFactory;
use App\Factory\UserFactory;

final class AuditLogTest extends AbstractApiTestCase
{
    public function testCreatingAClientWritesAnAuditLog(): void
    {
        $company = CompanyFactory::createOne();
        $marc = UserFactory::createOne(['email' => 'marc@test.fr', 'company' => $company]);

        $this->loginAs('marc@test.fr')->request('POST', '/api/clients', [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => ['name' => 'Audit SARL', 'email' => 'audit@example.fr'],
        ]);
        self::assertResponseStatusCodeSame(201);

        $log = AuditLogFactory::repository()->findOneBy(['action' => 'client.created', 'actor' => $marc]);

        self::assertNotNull($log);
        self::assertSame($company->getId(), $log->getCompany()?->getId());
    }

    public function testUpdatingAnInvoiceLogsTheChanges(): void
    {
        $company = CompanyFactory::createOne();
        UserFactory::createOne(['email' => 'marc@test.fr', 'company' => $company]);
        $invoice = InvoiceFactory::createOne([
            'company' => $company,
            'client' => ClientFactory::createOne(['company' => $company]),
            'issuedAt' => new \DateTimeImmutable('2026-10-01'),
            'dueAt' => new \DateTimeImmutable('2026-11-16'),
        ]);

        $this->loginAs('marc@test.fr')->request('PATCH', '/api/invoices/'.$invoice->getId(), [
            'headers' => ['Content-Type' => 'application/merge-patch+json'],
            'json' => ['dueAt' => '2026-11-30'],
        ]);
        self::assertResponseIsSuccessful();

        $log = AuditLogFactory::repository()->findOneBy(['action' => 'invoice.updated']);

        self::assertNotNull($log);
        self::assertSame(
            ['dueAt' => ['old' => '2026-11-16', 'new' => '2026-11-30']],
            $log->getData(),
        );
    }
}
