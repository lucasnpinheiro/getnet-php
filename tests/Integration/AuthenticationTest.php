<?php

namespace Lucasnpinheiro\Getnet\Tests\Integration;

use Lucasnpinheiro\Getnet\Getnet;
use Lucasnpinheiro\Getnet\Environment;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class AuthenticationTest extends TestCase
{
    private Getnet $getnet;
    private MockHandler $mockHandler;

    protected function setUp(): void
    {
        parent::setUp();

        // Criando um MockHandler para simular respostas da API
        $this->mockHandler = new MockHandler();

        $handlerStack = HandlerStack::create($this->mockHandler);
        $httpClient = new Client(['handler' => $handlerStack]);

        // Inicializando a classe Getnet com o mock do HTTP Client
        $this->getnet = new Getnet('client_id', 'client_secret', Environment::SANDBOX, false, $httpClient);
    }

    public function testAuthenticationSuccess(): void
    {
        // Simulando uma resposta de autenticação bem-sucedida
        $this->mockHandler->append(new Response(200, [], json_encode([
            'access_token' => 'mock_access_token',
            'token_type' => 'Bearer',
            'expires_in' => 3600,
        ])));

        // Acessando o método privado `authenticate()` com Reflection
        $reflection = new \ReflectionClass($this->getnet);
        $method = $reflection->getMethod('authenticate');
        $method->setAccessible(true);
        $method->invoke($this->getnet); // Chamando o método privado

        // Verificando se o token foi armazenado corretamente
        $accessTokenProperty = $reflection->getProperty('accessToken');
        $accessTokenProperty->setAccessible(true);

        $this->assertEquals('mock_access_token', $accessTokenProperty->getValue($this->getnet));
    }

    public function testAuthenticationFailure(): void
{
    // Simulando falha na autenticação
    $this->mockHandler->append(new Response(401, [], json_encode([
        'error' => 'invalid_client',
        'error_description' => 'Client authentication failed',
    ])));

    // Acessando o método privado `authenticate()` com Reflection
    $reflection = new \ReflectionClass($this->getnet);
    $method = $reflection->getMethod('authenticate');
    $method->setAccessible(true);

    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Authentication error: Client error: `POST https://api-sandbox.getnet.com.br/auth/oauth/v2/token` resulted in a `401 Unauthorized` response: {"error":"invalid_client","error_description":"Client authentication failed"}');

    $method->invoke($this->getnet); // Chamando o método privado
}

}
