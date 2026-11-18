<?php

namespace App\Tests\Api;

use ApiPlatform\Test\ApiTestCase;
use ApiPlatform\Test\Client;
use App\Factory\ClientFactory;
use App\Factory\CompanyFactory;
use App\Factory\InvoiceFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class TenantIsolationTest extends ApiTestCase
{
    use Factories;      // permet d'utiliser les factories dans les tests
    use ResetDatabase;  // base de test vide avant chaque test

    protected static ?bool $alwaysBootKernel = false;

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

    private function loginAs(string $email): Client
    {
        $client = static::createClient();
        $response = $client->request('POST', '/api/login', [
            'json' => ['email' => $email, 'password' => 'password'],
        ]);

        $client->setDefaultOptions(['auth_bearer' => $response->toArray()['token']]);

        return $client;
    }
}
