<?php

namespace Lucasnpinheiro\Getnet\Tests\Integration;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected MockHandler $mockHandler;
    protected Client $httpClient;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $this->httpClient = new Client(['handler' => $handlerStack]);
    }

    protected function getMockResponse(string $filename): array
    {
        $path = __DIR__ . '/Mock/responses/' . $filename;
        if (!file_exists($path)) {
            throw new \RuntimeException("Mock response file not found: {$filename}");
        }
        return json_decode(file_get_contents($path), true);
    }

    protected function queueMockResponse(string $filename, int $statusCode = 200): void
    {
        $response = $this->getMockResponse($filename);
        $this->mockHandler->append(
            new Response(
                $statusCode,
                ['Content-Type' => 'application/json'],
                json_encode($response)
            )
        );
    }
}
