<?php

namespace Lucasnpinheiro\Getnet\Tests\Unit;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use Lucasnpinheiro\Getnet\Address;
use Lucasnpinheiro\Getnet\Card;
use Lucasnpinheiro\Getnet\Credit;
use Lucasnpinheiro\Getnet\Customer;
use Lucasnpinheiro\Getnet\Debit;
use Lucasnpinheiro\Getnet\Environment;
use Lucasnpinheiro\Getnet\Getnet;
use Lucasnpinheiro\Getnet\Order;
use Lucasnpinheiro\Getnet\Transaction;
use Lucasnpinheiro\Getnet\TransactionCreditCard;
use Lucasnpinheiro\Getnet\TransactionDebitCard;
use Lucasnpinheiro\Getnet\TransactionPix;
use Lucasnpinheiro\Getnet\Type;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class GetnetTest extends TestCase
{
    private Getnet $getnet;

    protected function setUp(): void
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
        $responseData = [
            "payment_id" => "06f256c8-1bbf-42bf-93b4-ce2041bfb87e",
            "seller_id" => "1234567",
            "amount" => 100,
            "currency" => "BRL",
            "order_id" => "123456",
            "status" => "APPROVED",
            "received_at" => "2023-01-01T10:00:00",
            "credit" => [
                "delayed" => false,
                "authorization_code" => "123456",
                "authorized_at" => "2023-01-01T10:00:00",
                "reason_code" => 0,
                "reason_message" => "transaction approved",
                "acquirer" => "GETNET",
                "soft_descriptor" => "Description",
                "brand" => "Visa",
                "terminal_nsu" => "123456",
                "acquirer_transaction_id" => "123456789"
            ]
        ];

        $mockResponse = new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode($responseData)
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

        $order = new Order('123456');
        $card = new Card(
            'numberToken123',
            'John Doe',
            '123',
            '12',
            '2025'
        );
        $credit = new Credit(
            false,
            false,
            false,
            'FULL',
            1,
            $card
        );

        $billingAddress = new Address(
            'Rua Example',
            '123',
            'Apto 1',
            'Centro',
            'São Paulo',
            'SP',
            'Brasil',
            '01234-567'
        );

        $customer = new Customer(
            'customer123',
            $billingAddress,
            'John',
            'Doe',
            'John Doe',
            'john@example.com',
            'CPF',
            '12345678901',
            '11999887766'
        );

        $transaction = new TransactionCreditCard(
            '1234567',
            100,
            'BRL',
            $order,
            $customer,
            $credit
        );

        $result = $this->getnet->processTransaction($transaction);
        $this->assertEquals($responseData['payment_id'], $result->payment_id);
        $this->assertEquals($responseData['status'], $result->status);
    }

    public function testProcessDebitCardTransaction(): void
    {
        $responseData = [
            "payment_id" => "06f256c8-1bbf-42bf-93b4-ce2041bfb87e",
            "seller_id" => "1234567",
            "amount" => 100,
            "currency" => "BRL",
            "order_id" => "123456",
            "status" => "APPROVED",
            "debit" => [
                "authorization_code" => "123456",
                "authorized_at" => "2023-01-01T10:00:00",
                "reason_code" => 0,
                "reason_message" => "transaction approved",
                "brand" => "Visa",
                "terminal_nsu" => "123456",
                "acquirer_transaction_id" => "123456789"
            ]
        ];

        $mockResponse = new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode($responseData)
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

        $order = new Order('123456');
        $card = new Card(
            'numberToken123',
            'John Doe',
            '123',
            '12',
            '2025'
        );
        $debit = new Debit(
            '11999887766',
            123,
            true,
            $card
        );

        $billingAddress = new Address(
            'Rua Example',
            '123',
            'Apto 1',
            'Centro',
            'São Paulo',
            'SP',
            'Brasil',
            '01234-567'
        );

        $customer = new Customer(
            'customer123',
            $billingAddress,
            'John',
            'Doe',
            'John Doe',
            'john@example.com',
            'CPF',
            '12345678901',
            '11999887766'
        );

        $transaction = new TransactionDebitCard(
            '1234567',
            100,
            'BRL',
            $order,
            $customer,
            $debit
        );

        $result = $this->getnet->processTransaction($transaction);
        $this->assertEquals($responseData['payment_id'], $result->payment_id);
        $this->assertEquals($responseData['status'], $result->status);
    }

    public function testProcessPixTransaction(): void
    {
        $responseData = [
            "payment_id" => "06f256c8-1bbf-42bf-93b4-ce2041bfb87e",
            "seller_id" => "1234567",
            "amount" => 100,
            "currency" => "BRL",
            "order_id" => "123456",
            "status" => "PENDING",
            "pix" => [
                "qr_code" => "00020101021226870014br.gov.bcb.pix2565qrcodes-pix.gerencianet.com.br/v2/2359a5f27578-4d89-8503-f078132c8c4b52040000530398654041.005802BR5925EMPRESA TESTE 6014BELO HORIZONTE62070503***6304E2CA",
                "expiration_date" => "2023-01-01T10:30:00",
                "additional_information" => [
                    "transaction_id" => "123456789"
                ]
            ]
        ];

        $mockResponse = new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode($responseData)
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

        $transaction = new TransactionPix(
            '1234567',
            100,
            'BRL',
            '123456',
            'customer123'
        );

        $result = $this->getnet->processTransaction($transaction);
        $this->assertEquals($responseData['payment_id'], $result->payment_id);
        $this->assertEquals($responseData['status'], $result->status);
    }

    public function testGetToken(): void
    {
        $responseData = [
            'number_token' => 'dfe05208b105578c070f806c80abd3af09e246827d29b866cf4ce16c205849977c9496cbf0d0234f42339937f327747075f68763537b90b31389e01231d4d13c'
        ];

        $mockResponse = new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode($responseData)
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

        $card = new Card(
            '5155901222280001',
            'John Doe',
            '123',
            '12',
            '2025'
        );

        $result = $this->getnet->getToken($card);
        $this->assertEquals($responseData['number_token'], $result->numberToken());
    }

    public function testAuthenticationFailure(): void
    {
        $mockResponse = new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode(['error' => 'invalid_client'])
        );

        $mockHttpClient = $this->createMock(Client::class);
        $mockHttpClient->method('request')->willReturn($mockResponse);

        $this->getnet->setHttpClient($mockHttpClient);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to generate access token');

        $reflection = new ReflectionClass($this->getnet);
        $method = $reflection->getMethod('authenticate');
        $method->setAccessible(true);
        $method->invoke($this->getnet);
    }

    public function testProcessResponse(): void
    {
        $responseData = [
            'payment_id' => '1a938e0d-26ab-4ac2-b263-ab46a99e4356',
            'status' => 'PENDING'
        ];

        $mockResponse = new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode($responseData)
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

        $result = $this->getnet->processResponse('1a938e0d-26ab-4ac2-b263-ab46a99e4356', Type::PIX);
        $this->assertEquals($responseData['payment_id'], $result->payment_id);
        $this->assertEquals($responseData['status'], $result->status);
    }

    /**
     * @dataProvider environmentDataProvider
     */
    public function testEnvironmentUrls(string $environment, string $expectedBaseUrl): void
    {
        $getnet = new Getnet(
            'client_id',
            'client_secret',
            $environment
        );

        $reflection = new ReflectionClass($getnet);
        $property = $reflection->getProperty('baseUrl');
        $this->assertEquals($expectedBaseUrl, $property->getValue($getnet));
    }

    public function environmentDataProvider(): array
    {
        return [
            'sandbox' => [
                Environment::SANDBOX,
                'https://api-sandbox.getnet.com.br'
            ],
            'homologation' => [
                Environment::HOMOLOGATION,
                'https://api-homologacao.getnet.com.br'
            ],
            'production' => [
                Environment::PRODUCTION,
                'https://api.getnet.com.br'
            ]
        ];
    }

    public function testInvalidEnvironment(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid value provided for 'environment'.");

        new Getnet(
            'client_id',
            'client_secret',
            'invalid_environment'
        );
    }

    public function testCreateAuthorizationHeader(): void
    {
        $getnet = new Getnet(
            'test_client_id',
            'test_client_secret',
            Environment::SANDBOX
        );

        $reflection = new ReflectionClass($getnet);
        $method = $reflection->getMethod('createAuthorizationHeader');
        $method->setAccessible(true);
        $expectedHeader = 'Basic ' . base64_encode('test_client_id:test_client_secret');
        $this->assertEquals($expectedHeader, $method->invoke($getnet));
    }

    public function testInvalidTransactionType(): void
    {
        $transaction = new Transaction(
            'seller123',
            100,
            'BRL'
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid transaction type provided.');

        $this->getnet->processTransaction($transaction);
    }
}
