<?php

namespace Lucasnpinheiro\Getnet\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Lucasnpinheiro\Getnet\Address;
use Lucasnpinheiro\Getnet\Card;
use Lucasnpinheiro\Getnet\Credit;
use Lucasnpinheiro\Getnet\Customer;
use Lucasnpinheiro\Getnet\Debit;
use Lucasnpinheiro\Getnet\Environment;
use Lucasnpinheiro\Getnet\Getnet;
use Lucasnpinheiro\Getnet\Order;
use Lucasnpinheiro\Getnet\TransactionCreditCard;
use Lucasnpinheiro\Getnet\TransactionDebitCard;
use Lucasnpinheiro\Getnet\TransactionPix;
use PHPUnit\Framework\TestCase;

class GetnetTest extends TestCase
{
    private Getnet $getnet;

    public function setUp(): void
    {
        $mockTokenResponse = new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode(['access_token' => 'mock_access_token'])
        );

        $mockHttpClient = $this->createMock(Client::class);
        $mockHttpClient->method('request')->willReturn($mockTokenResponse);

        $this->getnet = new Getnet(
            'mock_client_id',
            'mock_client_secret',
            Environment::SANDBOX
        );
        $this->getnet->setHttpClient($mockHttpClient);

        parent::setUp();
    }

    public function testProcessCreditCardTransaction(): void
    {
        $mockResponse = new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode([
                "payment_id" => "06f256c8-1bbf-42bf-93b4-ce2041bfb87e",
                "status" => "APPROVED",
                "credit" => [
                    "authorization_code" => 99999,
                    "reason_message" => "transaction approved"
                ]
            ])
        );

        $mockHttpClient = $this->createMock(Client::class);
        $mockHttpClient->method('request')->willReturnOnConsecutiveCalls(
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode(['access_token' => 'mock_access_token'])
            ),
            $mockResponse
        );

        $this->getnet->setHttpClient($mockHttpClient);

        $orderId = 'order-id';
        $salesTax = 10.99;
        $productType = 'product-type';

        $order = new Order($orderId, $salesTax, $productType);
        $customerId = 'customer-id';
        $billingAddress = new Address(
            'street',
            'number',
            'complement',
            'district',
            'city',
            'state',
            'country',
            'postalCode'
        );
        $customer = new Customer($customerId, $billingAddress);

        $card = new Card(
            numberToken: 'numberToken',
            cardholderName: 'cardholderName',
            securityCode: 'securityCode',
            expirationMonth: 'expirationMonth',
            expirationYear: 'expirationYear',
        );
        $credit = new Credit(
            delayed: false,
            preAuthorization: false,
            saveCardData: false,
            transactionType: 'type',
            numberInstallments: 1,
            card: $card,
        );

        $creditTransaction = new TransactionCreditCard(
            sellerId: "6eb2412c-165a-41cd-b1d9-76c575d70a28",
            amount: 100,
            currency: "BRL",
            order: $order,
            customer: $customer,
            credit: $credit
        );

        $result = $this->getnet->processTransaction($creditTransaction);
        $this->assertJson($result);
        $this->assertStringContainsString("APPROVED", $result);
    }

    public function testProcessDebitCardTransaction(): void
    {
        $mockResponse = new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode([
                "payment_id" => "06f256c8-1bbf-42bf-93b4-ce2041bfb87e",
                "status" => "APPROVED",
                "credit" => [
                    "authorization_code" => 99999,
                    "reason_message" => "transaction approved"
                ]
            ])
        );

        $mockHttpClient = $this->createMock(Client::class);
        $mockHttpClient->method('request')->willReturnOnConsecutiveCalls(
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode(['access_token' => 'mock_access_token'])
            ),
            $mockResponse
        );

        $this->getnet->setHttpClient($mockHttpClient);

        $orderId = 'order-id';
        $salesTax = 10.99;
        $productType = 'product-type';

        $order = new Order($orderId, $salesTax, $productType);
        $customerId = 'customer-id';
        $billingAddress = new Address(
            'street',
            'number',
            'complement',
            'district',
            'city',
            'state',
            'country',
            'postalCode'
        );
        $customer = new Customer($customerId, $billingAddress);

        $card = new Card(
            numberToken: 'numberToken',
            cardholderName: 'cardholderName',
            securityCode: 123,
            expirationMonth: '12',
            expirationYear: '24',
        );

        $debit = new Debit(
            cardholderMobile: 'cardholderMobile',
            dynamicMcc: 123,
            authenticated: true,
            card: $card,
            credentialsOnFileType: 'ONE_CLICK',
        );


        $debitTransaction = new TransactionDebitCard(
            sellerId: "6eb2412c-165a-41cd-b1d9-76c575d70a28",
            amount: 100,
            currency: "BRL",
            order: $order,
            customer: $customer,
            debit: $debit
        );

        $result = $this->getnet->processTransaction($debitTransaction);
        $this->assertJson($result);
        $this->assertStringContainsString("APPROVED", $result);
    }

    public function testProcessPixTransaction(): void
    {
        $mockResponse = new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode([
                "payment_id" => "1a938e0d-26ab-4ac2-b263-ab46a99e4356",
                "status" => "WAITING",
                "description" => "QR Code gerado com sucesso e aguardando o pagamento.",
                "additional_data" => [
                    "transaction_id" => "8289874875871543653292342",
                    "qr_code" => "00020101021226740014br.gov.bcb.pix210812345678220412342308123456782420001122334455 667788995204000053039865406123.455802BR5913FULANO DE TAL6008BRASILIA62190515RP12345678- 201980720014br.gov.bcb.pix2550bx.com.br/spi/U0VHUkVET1RPVEFMTUVOVEVBTEVBVE9SSU8=63 0434D1",
                    "creation_date_qrcode" => "2021-02-14T17:50:12Z",
                    "expiration_date_qrcode" => "2021-02-14T17:51:52Z",
                    "psp_code" => "033"
                ]
            ])
        );

        $mockHttpClient = $this->createMock(Client::class);
        $mockHttpClient->method('request')->willReturnOnConsecutiveCalls(
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode(['access_token' => 'mock_access_token'])
            ),
            $mockResponse
        );

        $this->getnet->setHttpClient($mockHttpClient);

        $orderId = 'order-id';
        $sellerId = 'seller-id';
        $amount = 10.99;
        $customerId = 'customer-id';

        $debitTransaction = new TransactionPix(
            sellerId: $sellerId,
            amount: $amount,
            currency: 'BRL',
            orderId: $orderId,
            customerId: $customerId
        );

        $result = $this->getnet->processTransaction($debitTransaction);
        $this->assertJson($result);
        $this->assertStringContainsString("WAITING", $result);
    }

    public function testGetToken(): void
    {
        $mockResponse = new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode(['number_token' => 'mock_number_token'])
        );

        $mockHttpClient = $this->createMock(Client::class);
        $mockHttpClient->method('request')->willReturnOnConsecutiveCalls(
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode(['access_token' => 'mock_access_token'])
            ),
            $mockResponse
        );

        $this->getnet->setHttpClient($mockHttpClient);

        $cardNumber = '4111111111111111';
        $sellerId = 'seller-id';
        $customerId = 'customer-id';

        $result = $this->getnet->getToken($cardNumber, $sellerId, $customerId);
        $this->assertJson($result);
        $this->assertStringContainsString('mock_number_token', $result);
    }
}
