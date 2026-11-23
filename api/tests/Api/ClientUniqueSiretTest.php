<?php

namespace App\Tests\Api;

use App\Factory\ClientFactory;
use App\Factory\CompanyFactory;
use App\Factory\UserFactory;

final class ClientUniqueSiretTest extends AbstractApiTestCase
{
    private const SIRET = '12345678901234';

    public function testDuplicateSiretInSameCompanyIsRejected(): void
    {
        $company = CompanyFactory::createOne();
        UserFactory::createOne(['email' => 'marc@test.fr', 'company' => $company]);
        ClientFactory::createOne(['company' => $company, 'siret' => self::SIRET]);

        $this->loginAs('marc@test.fr')->request('POST', '/api/clients', [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => [
                'name' => 'Doublon SAS',
                'email' => 'doublon@test.fr',
                'siret' => self::SIRET,
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains(['violations' => [
            ['propertyPath' => 'siret', 'message' => 'Un client avec ce SIRET existe déjà.'],
        ]]);
    }

    public function testSameSiretInAnotherCompanyIsAccepted(): void
    {
        $company = CompanyFactory::createOne();
        UserFactory::createOne(['email' => 'marc@test.fr', 'company' => $company]);
        ClientFactory::createOne(['company' => CompanyFactory::createOne(), 'siret' => self::SIRET]);

        $this->loginAs('marc@test.fr')->request('POST', '/api/clients', [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => [
                'name' => 'Même client, autre entreprise',
                'email' => 'client@test.fr',
                'siret' => self::SIRET,
            ],
        ]);

        self::assertResponseStatusCodeSame(201);
    }

    public function testSeveralClientsWithoutSiretAreAccepted(): void
    {
        $company = CompanyFactory::createOne();
        UserFactory::createOne(['email' => 'marc@test.fr', 'company' => $company]);
        ClientFactory::createOne(['company' => $company, 'siret' => null]);

        $this->loginAs('marc@test.fr')->request('POST', '/api/clients', [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => [
                'name' => 'Particulier',
                'email' => 'particulier@test.fr',
            ],
        ]);

        self::assertResponseStatusCodeSame(201);
    }
}
