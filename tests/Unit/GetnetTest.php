<?php

namespace Lucasnpinheiro\Getnet\Tests\Unit;

use Lucasnpinheiro\Getnet\TransactionCard;
use PHPUnit\Framework\TestCase;
use Lucasnpinheiro\Getnet\Getnet;
use Lucasnpinheiro\Getnet\Environment;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;

class GetnetTest extends TestCase
{
    private string $clientId = 'c076e924-a3fe-492d-a41f-1f8de48fb4b1"';
    private string $clientSecret = 'bc097a2f-28e0-43ce-be92-d846253ba748';
    private string $accessToken = 'mock_access_token';
    private Getnet $getnet;

    protected function setUp(): void
    {
        // Cria o mock do cliente HTTP
        $mockHttpClient = $this->createMock(Client::class);

        // Simula a resposta do token de autenticação
        $mockResponse = new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode(['access_token' => $this->accessToken])
        );

        $mockHttpClient->method('request')
            ->willReturn($mockResponse);

        // Substitui a autenticação para evitar chamadas reais
        $this->getnet = $this->getMockBuilder(Getnet::class)
            ->setConstructorArgs([$this->clientId, $this->clientSecret, Environment::SANDBOX])
            ->onlyMethods(['createAuthorizationHeader', 'authenticate'])
            ->getMock();

        $this->getnet->method('createAuthorizationHeader')->willReturn('Basic mock_auth_header');
        $this->getnet->method('authenticate')->willReturn(null);
    }

    public function testAuthenticateSetsAccessToken(): void
    {
        // Mock interno para testar autenticação
        $mockHttpClient = $this->createMock(Client::class);

        $mockResponse = new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode(['access_token' => $this->accessToken])
        );

        $mockHttpClient->method('request')
            ->willReturn($mockResponse);

        // Substituir o cliente HTTP dentro do método privado
        $reflection = new \ReflectionClass($this->getnet);
        $authenticateMethod = $reflection->getMethod('authenticate');
        $authenticateMethod->setAccessible(true);

        $authenticateMethod->invoke($this->getnet);
        $this->assertEquals($this->accessToken, $this->getnet->getBaseUrl());
    }

    public function testProcessTransaction(): void
    {
        $transactionMock = $this->createMock(TransactionCard::class);
        $transactionMock->method('jsonSerialize')->willReturn([
            'amount' => 100,
            'card_number' => '4111111111111111',
            'expiration_month' => '12',
            'expiration_year' => '25',
        ]);

        $mockHttpClient = $this->createMock(Client::class);

        $mockResponse = new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode(['status' => 'approved'])
        );

        $mockHttpClient->method('request')->willReturn($mockResponse);

        $result = $this->getnet->processTransaction($transactionMock);
        $this->assertJson($result);
        $this->assertStringContainsString('approved', $result);
    }
}