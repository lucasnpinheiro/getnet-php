<?php

declare(strict_types=1);

namespace Lucasnpinheiro\Getnet;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use InvalidArgumentException;

class Getnet
{
    private string $accessToken;
    private string $baseUrl;
    private ClientInterface $httpClient;

    public function __construct(
        private string $clientId,
        private string $clientSecret,
        private string $enviroment = Environment::SANDBOX,
        private bool $debug = false
    ) {
        $this->setEnvironment($enviroment);
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

    public function setHttpClient(?ClientInterface $httpClient): void
    {
        $this->httpClient = $httpClient ?? new Client();
    }

    private function authenticate(): void
    {
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

        $response = $this->httpClient->request('POST', $this->baseUrl . '/auth/oauth/v2/token', $requestOptions);
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

    public function processTransaction(
        Transaction|TransactionCreditCard|TransactionDebitCard|TransactionBoleto|TransactionPix $transaction
    ): string {
        $this->authenticate();
        $transactionTypeMap = [
            TransactionDebitCard::class => '/v1/payments/debit',
            TransactionCreditCard::class => '/v1/payments/credit',
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

        $response = $this->httpClient->request('POST', $transactionUrl, $requestData);
        return $response->getBody()->getContents();
    }

    public function processResponse(string $paymentId, string $type): string
    {
        $this->authenticate();
        $responseTypeMap = [
            Type::PIX => '/v1/payments/qrcode/{payment_id}',
        ];

        $transactionUrl = $this->getBaseUrl() . $responseTypeMap[$type];
        $transactionUrl = str_replace('{payment_id}', $paymentId, $transactionUrl);
        $requestData = [
            'headers' => [
                'Authorization' => "Bearer {$this->accessToken}",
                'Content-Type' => 'application/json; charset=utf-8',
            ]
        ];

        $response = $this->httpClient->request('GET', $transactionUrl, $requestData);
        return $response->getBody()->getContents();
    }

    public function getToken(
        string $cardNumber,
        ?string $sellerId = null,
        ?string $customerId = null,
    ): string {
        $this->authenticate();
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

        $response = $this->httpClient->request(
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
