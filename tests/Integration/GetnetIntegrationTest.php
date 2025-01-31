<?php

namespace Lucasnpinheiro\Getnet\Tests\Integration;

use Lucasnpinheiro\Getnet\Address;
use Lucasnpinheiro\Getnet\Card;
use Lucasnpinheiro\Getnet\Credit;
use Lucasnpinheiro\Getnet\Customer;
use Lucasnpinheiro\Getnet\Environment;
use Lucasnpinheiro\Getnet\Getnet;
use Lucasnpinheiro\Getnet\Order;
use Lucasnpinheiro\Getnet\TransactionCreditCard;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class GetnetIntegrationTest extends TestCase
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

        // Add mock response for authentication
        $this->mockHandler->append(new Response(200, [], json_encode([
            'access_token' => 'mock_access_token',
            'token_type' => 'Bearer',
            'expires_in' => 3600
        ])));

        // Initialize Getnet with mock values
        $this->getnet = new Getnet(
            'mock_client_id',
            'mock_client_secret',
            Environment::SANDBOX
        );
        $this->getnet->setHttpClient($client);
    }

    public function testAuthenticationFlow(): void
    {
        // Queue mock response for card token
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'number_token' => 'mock_card_token_123'
            ]))
        );

        $card = new Card(
            numberToken: '5155901222280001',
            cardholderName: 'Test Customer',
            securityCode: '123',
            expirationMonth: '12',
            expirationYear: '28',
            brand: 'mastercard'
        );

        $tokenizedCard = $this->getnet->getToken($card);
        $this->assertNotEmpty($tokenizedCard->numberToken());
    }

    public function testCardTokenization(): void
    {
        // Ensure the mock response for card tokenization matches expected output
        $this->mockHandler->append(
            new Response(200, [], json_encode([
                'number_token' => 'mock_card_token_123'
            ]))
        );

        $cardNumber = '5155901222280001';
        $token = $this->getnet->tokenizeCard($cardNumber);
        $this->assertEquals('mock_card_token_123', $token);
    }
}
