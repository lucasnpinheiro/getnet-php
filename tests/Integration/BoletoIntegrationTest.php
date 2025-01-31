<?php

namespace Lucasnpinheiro\Getnet\Tests\Integration;

use Lucasnpinheiro\Getnet\Address;
use Lucasnpinheiro\Getnet\Boleto;
use Lucasnpinheiro\Getnet\Customer;
use Lucasnpinheiro\Getnet\Environment;
use Lucasnpinheiro\Getnet\Getnet;
use Lucasnpinheiro\Getnet\Order;
use Lucasnpinheiro\Getnet\TransactionBoleto;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class BoletoIntegrationTest extends TestCase
{
    private Getnet $getnet;
    protected MockHandler $mockHandler;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup mock handler
        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $client = new Client(['handler' => $handlerStack]);

        // Initialize Getnet with mock values
        $this->getnet = new Getnet(
            'mock_client_id',
            'mock_client_secret',
            Environment::SANDBOX
        );
        $this->getnet->setHttpClient($client);
    }

    public function testCompleteBoletoTransaction(): void
    {
        // Queue mock response for authentication
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'access_token' => 'mock_access_token',
                'token_type' => 'Bearer',
                'expires_in' => 3600,
                'scope' => 'oob'
            ]))
        );

        // Queue mock response for boleto creation
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'payment_id' => 'mock_payment_123',
                'seller_id' => 'mock_seller_id',
                'amount' => 10000,
                'currency' => 'BRL',
                'order_id' => 'mock_order_123',
                'status' => 'PENDING',
                'boleto' => [
                    'our_number' => '123456789',
                    'document_number' => '123456789',
                    'expiration_date' => '2025-02-03',
                    'instructions' => 'Mock instructions',
                    'provider' => 'mock_provider',
                    'bank' => '001',
                    'line' => '123456789',
                    'pdf' => [
                        'url' => 'https://example.com/boleto.pdf'
                    ]
                ]
            ]))
        );

        // Create customer
        $customer = new Customer(
            customerId: 'customer_123',
            billingAddress: new Address(
                street: 'Rua Teste',
                number: '123',
                complement: 'Sala 1',
                district: 'Centro',
                city: 'São Paulo',
                state: 'SP',
                country: 'BR',
                postalCode: '12345678'
            ),
            firstName: 'João',
            lastName: 'Silva',
            name: 'João Silva',
            email: 'joao@example.com',
            documentType: 'CPF',
            documentNumber: '12345678912',
            phoneNumber: '11999999999'
        );

        // Create order
        $order = new Order(
            orderId: 'mock_order_123',
            salesTax: null,
            productType: 'service'
        );

        // Create boleto
        $boleto = new Boleto(
            documentNumber: '123456789',
            expirationDate: '2025-02-03',
            instructions: 'Mock instructions',
            provider: 'mock_provider'
        );

        // Create boleto transaction
        $transaction = new TransactionBoleto(
            sellerId: 'mock_seller_id',
            amount: 10000,
            currency: 'BRL',
            order: $order,
            customer: $customer,
            boleto: $boleto
        );

        $response = $this->getnet->processTransaction($transaction);
        
        $this->assertEquals('PENDING', $response->status);
        $this->assertEquals('mock_payment_123', $response->payment_id);
        $this->assertEquals('123456789', $response->boleto->our_number);
    }

    public function testBoletoTransactionWithMinimalData(): void
    {
        // Queue mock response for authentication
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'access_token' => 'mock_access_token',
                'token_type' => 'Bearer',
                'expires_in' => 3600,
                'scope' => 'oob'
            ]))
        );

        // Queue mock response for boleto creation
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'payment_id' => 'mock_payment_123',
                'seller_id' => 'mock_seller_id',
                'amount' => 5000,
                'currency' => 'BRL',
                'order_id' => 'mock_order_123',
                'status' => 'PENDING',
                'boleto' => [
                    'our_number' => '123456789',
                    'document_number' => '123456789',
                    'expiration_date' => '2025-02-03',
                    'instructions' => 'Mock instructions',
                    'provider' => 'mock_provider',
                    'bank' => '001',
                    'line' => '123456789',
                    'pdf' => [
                        'url' => 'https://example.com/boleto.pdf'
                    ]
                ]
            ]))
        );

        // Create customer with minimal data
        $customer = new Customer(
            customerId: 'customer_123',
            firstName: 'João',
            lastName: 'Silva',
            documentType: 'CPF',
            documentNumber: '12345678912',
            billingAddress: new Address(
                street: 'Rua Teste',
                number: '123',
                complement: null,
                district: 'Centro',
                city: 'São Paulo',
                state: 'SP',
                country: 'BR',
                postalCode: '12345678'
            )
        );

        // Create order with minimal data
        $order = new Order(
            orderId: 'mock_order_123',
            salesTax: null,
            productType: 'service'
        );

        // Create boleto with minimal data
        $boleto = new Boleto(
            documentNumber: '123456789',
            expirationDate: '2025-02-03',
            instructions: 'Mock instructions',
            provider: 'mock_provider'
        );

        // Create boleto transaction
        $transaction = new TransactionBoleto(
            sellerId: 'mock_seller_id',
            amount: 5000,
            currency: 'BRL',
            order: $order,
            customer: $customer,
            boleto: $boleto
        );

        $response = $this->getnet->processTransaction($transaction);
        
        $this->assertEquals('PENDING', $response->status);
        $this->assertEquals('mock_payment_123', $response->payment_id);
        $this->assertEquals('123456789', $response->boleto->our_number);
    }
}
