<?php

declare(strict_types=1);

namespace Lucasnpinheiro\Getnet;

use GuzzleHttp\Client;
use InvalidArgumentException;

class Getnet
{
    private string $accessToken;
    private string $baseUrl;

    public function __construct(
        private string $clientId,
        private string $clientSecret,
        private string $enviroment = Environment::SANDBOX,
        private bool $debug = false
    )
    {
        $this->setEnvironment($enviroment);
        $this->authenticate();
    }


    private function setEnvironment(string $environment): void
    {
        $baseUrls = [
            Environment::SANDBOX => 'https://api-sandbox.getnet.com.br',
            Environment::HOMOLOGATION => 'https://api-homologacao.getnet.com.br',
            Environment::PRODUCTION => 'https://api.getnet.com.br',
        ];
        
        if (!isset($baseUrls[$environment])) {
            throw new InvalidArgumentException("Invalid value provided for 'environment'.");
        }

        $this->baseUrl = $baseUrls[$environment];
        $this->environment = $environment;
    }

    private function authenticate(): void
    {
        $httpClient = new Client();

        $authorizationHeader = $this->createAuthorizationHeader();
        $requestOptions = [
            'headers' => [
                'Authorization' => $authorizationHeader,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'scope' => 'oob',
                'grant_type' => 'client_credentials',
            ],
        ];

        $response = $httpClient->request('POST', $this->baseUrl . '/auth/oauth/v2/token', $requestOptions);
        $responseData = json_decode($response->getBody()->getContents(), true);

        if (!isset($responseData['access_token'])) {
            throw new \Exception('Failed to generate access token');
        }

        $this->accessToken = $responseData['access_token'];
    }

    private function createAuthorizationHeader(): string
    {
        $credentials = $this->clientId . ':' . $this->clientSecret;
        return 'Basic ' . base64_encode($credentials);
    }

    public function processTransaction(Transaction|TransactionCard|TransactionBoleto|TransactionPix $transaction): string
    {
        $httpClient = new \GuzzleHttp\Client();
        $transactionTypeMap = [
            TransactionCard::class => '/v1/payments/credit',
            TransactionBoleto::class => '/v1/payments/boleto',
            TransactionPix::class => '/v1/payments/qrcode/pix',
        ];

        if (!isset($transactionTypeMap[get_class($transaction)])) {
            throw new InvalidArgumentException("Invalid transaction type provided.");
        }

        $transactionUrl = $this->getBaseUrl() . $transactionTypeMap[get_class($transaction)];
        $requestData = [
            'headers' => [
                'Authorization' => "Bearer {$this->accessToken}",
                'Content-Type' => 'application/json; charset=utf-8',
            ],
            'json' => $transaction->jsonSerialize(),
        ];

        if (get_class($transaction) === TransactionPix::class) {
            $transactionUrl = $this->getBaseUrl() . '/v1/payments/qrcode/pix';
            $requestData['headers']['seller_id'] = $transaction->getSellerId();
            $requestData['headers']['x-qrcode-expiration-time'] = $_ENV['PIX_TIMEOUT'] ?? 1800;
        }

        $response = $httpClient->request('POST', $transactionUrl, $requestData);
        return $response->getBody()->getContents();
    }

    public function getToken(
        string $cardNumber,
        ?string $sellerId = null,
        ?string $customerId = null,
    ): string {
        $client = new Client();

        $requestHeaders = [
            'Authorization' => "Bearer {$this->accessToken}",
            'Content-Type' => 'application/json; charset=utf-8',
        ];

        if ($sellerId !== null) {
            $requestHeaders['seller_id'] = $sellerId;
        }

        $requestData = [
            'card_number' => $cardNumber,
        ];

        if ($customerId !== null) {
            $requestData['customer_id'] = $customerId;
        }

        $response = $client->request(
            'POST',
            "{$this->getBaseUrl()}/v1/tokens/card",
            [
                'verify' => false,
                'headers' => $requestHeaders,
                'json' => $requestData,
            ],
        );

        return $response->getBody()->getContents();
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
}
