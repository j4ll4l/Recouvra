<?php

namespace App\Tests\Api;

use App\Factory\ClientFactory;
use App\Factory\CompanyFactory;
use App\Factory\UserFactory;

final class ClientSearchTest extends AbstractApiTestCase
{
    public function testSearchByNameIgnoresCase(): void
    {
        $company = CompanyFactory::createOne();
        UserFactory::createOne(['email' => 'marc@test.fr', 'company' => $company]);
        ClientFactory::createOne(['company' => $company, 'name' => 'M. Martin']);
        ClientFactory::createOne(['company' => $company, 'name' => 'Mme Dupont']);

        $client = $this->loginAs('marc@test.fr');
        $response = $client->request('GET', '/api/clients', ['query' => ['name' => 'martin']]);

        self::assertResponseIsSuccessful();
        $names = array_column($response->toArray()['member'], 'name');
        self::assertSame(['M. Martin'], $names);
    }
}
