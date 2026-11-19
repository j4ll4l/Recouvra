<?php

namespace App\Tests\Api;

use App\Factory\ClientFactory;
use App\Factory\CompanyFactory;
use App\Factory\InvoiceFactory;
use App\Factory\UserFactory;

final class TenantIsolationTest extends AbstractApiTestCase
{
    public function testUserCannotReadInvoiceOfAnotherCompany(): void
    {
        $marcPlomberie = CompanyFactory::createOne();
        $elecPro = CompanyFactory::createOne();

        UserFactory::createOne(['email' => 'marc@test.fr', 'company' => $marcPlomberie]);
        $elecProInvoice = InvoiceFactory::createOne([
            'company' => $elecPro,
            'client' => ClientFactory::createOne(['company' => $elecPro]),
        ]);

        $client = $this->loginAs('marc@test.fr');
        $client->request('GET', '/api/invoices/'.$elecProInvoice->getId());

        self::assertResponseStatusCodeSame(404);
    }

    public function testUserOnlySeesInvoicesOfHisCompany(): void
    {
        $marcPlomberie = CompanyFactory::createOne();
        $elecPro = CompanyFactory::createOne();

        UserFactory::createOne(['email' => 'marc@test.fr', 'company' => $marcPlomberie]);
        InvoiceFactory::createOne([
            'company' => $marcPlomberie,
            'client' => ClientFactory::createOne(['company' => $marcPlomberie]),
        ]);
        InvoiceFactory::createMany(3, [
            'company' => $elecPro,
            'client' => ClientFactory::createOne(['company' => $elecPro]),
        ]);

        $client = $this->loginAs('marc@test.fr');
        $client->request('GET', '/api/invoices');

        self::assertResponseIsSuccessful();
        self::assertJsonContains(['totalItems' => 1]);
    }

    public function testUserCannotAddPaymentToInvoiceOfAnotherCompany(): void
    {
        $marcPlomberie = CompanyFactory::createOne();
        $elecPro = CompanyFactory::createOne();

        UserFactory::createOne(['email' => 'marc@test.fr', 'company' => $marcPlomberie]);
        $elecProInvoice = InvoiceFactory::createOne([
            'company' => $elecPro,
            'client' => ClientFactory::createOne(['company' => $elecPro]),
        ]);

        $client = $this->loginAs('marc@test.fr');
        $client->request('POST', '/api/payments', [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => [
                'invoice' => '/api/invoices/'.$elecProInvoice->getId(),
                'amountCents' => 100,
                'paidAt' => (new \DateTimeImmutable())->format('Y-m-d'),
                'method' => 'transfer',
            ],
        ]);

        self::assertResponseStatusCodeSame(400);
    }
}
