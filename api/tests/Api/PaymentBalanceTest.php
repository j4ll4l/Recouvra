<?php

namespace App\Tests\Api;

use App\Entity\Invoice;
use App\Factory\ClientFactory;
use App\Factory\CompanyFactory;
use App\Factory\InvoiceFactory;
use App\Factory\PaymentFactory;
use App\Factory\UserFactory;

final class PaymentBalanceTest extends AbstractApiTestCase
{
    public function testPaymentAboveRemainingBalanceIsRejected(): void
    {
        $invoice = $this->createInvoiceWithBalanceOf400Euros();

        $this->loginAs('marc@test.fr')->request('POST', '/api/payments', [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => $this->paymentPayload($invoice, 50000), // 500 €
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains(['violations' => [['propertyPath' => 'amountCents']]]);
    }

    public function testPaymentEqualToRemainingBalanceIsAccepted(): void
    {
        $invoice = $this->createInvoiceWithBalanceOf400Euros();

        $this->loginAs('marc@test.fr')->request('POST', '/api/payments', [
            'headers' => ['Content-Type' => 'application/ld+json'],
            'json' => $this->paymentPayload($invoice, 40000), // 400 € pile
        ]);

        self::assertResponseStatusCodeSame(201);
    }

    /**
     * Facture de 1 200 € TTC, déjà payée à hauteur de 800 € → reste 400 €.
     */
    private function createInvoiceWithBalanceOf400Euros(): Invoice
    {
        $company = CompanyFactory::createOne();
        UserFactory::createOne(['email' => 'marc@test.fr', 'company' => $company]);

        $invoice = InvoiceFactory::createOne([
            'company' => $company,
            'client' => ClientFactory::createOne(['company' => $company]),
            'amountHtCents' => 100000,
            'amountTtcCents' => 120000,
        ]);

        PaymentFactory::createOne(['invoice' => $invoice, 'amountCents' => 80000]);

        return $invoice;
    }

    /**
     * @return array<string, mixed>
     */
    private function paymentPayload(Invoice $invoice, int $amountCents): array
    {
        return [
            'invoice' => '/api/invoices/'.$invoice->getId(),
            'amountCents' => $amountCents,
            'paidAt' => (new \DateTimeImmutable())->format('Y-m-d'),
            'method' => 'transfer',
        ];
    }
}
