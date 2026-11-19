<?php

namespace App\Tests\Api;

use ApiPlatform\Test\ApiTestCase;
use ApiPlatform\Test\Client;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

abstract class AbstractApiTestCase extends ApiTestCase
{
    use Factories;
    use ResetDatabase;

    protected static ?bool $alwaysBootKernel = false;

    protected function loginAs(string $email): Client
    {
        $client = static::createClient();
        $response = $client->request('POST', '/api/login', [
            'json' => ['email' => $email, 'password' => 'password'],
        ]);

        $client->setDefaultOptions(['auth_bearer' => $response->toArray()['token']]);

        return $client;
    }
}
