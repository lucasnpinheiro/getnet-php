<?php

declare(strict_types=1);

namespace Lucasnpinheiro\Getnet;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use InvalidArgumentException;
use stdClass;
use Throwable;

class Getnet
{
    private string $accessToken;
    private string $baseUrl;
    private ClientInterface $httpClient;

    public function __construct(
        private string $clientId,
        private string $clientSecret,
        private string $enviroment = Environment::SANDBOX,
        private bool $debug = false,
        ?ClientInterface $httpClient = null // Adicionando parâmetro opcional
    ) {
        $this->setEnvironment($enviroment);
        $this->httpClient = $httpClient ?? new Client(); // Inicializa o HTTP client caso seja null
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
        if (!isset($this->httpClient)) {
            $this->setHttpClient(null); // Garante a inicialização
        }

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
            'verify' => false
        ];

        try {
        $response = $this->httpClient->request('POST', $this->baseUrl . '/auth/oauth/v2/token', $requestOptions);

            if ($response->getStatusCode() !== 200) {
                throw new Exception("Authentication failed with status code: " . $response->getStatusCode());
            }

            $responseData = json_decode($response->getBody()->getContents(), true);

            if ($this->debug) {
                error_log('Authentication response: ' . print_r($responseData, true));
            }

            if (!isset($responseData['access_token'])) {
                throw new Exception('Failed to generate access token. Response: ' . json_encode($responseData));
        }

        $this->accessToken = $responseData['access_token'];
        } catch (Throwable $th) {
            throw new Exception("Authentication error: " . trim(str_replace("\n", ' ', $th->getMessage())));
    }
    }


    private function createAuthorizationHeader(): string
    {
        $credentials = $this->clientId . ':' . $this->clientSecret;
        return 'Basic ' . base64_encode($credentials);
    }

    public function processTransaction(
        Transaction|TransactionCreditCard|TransactionDebitCard|TransactionBoleto|TransactionPix $transaction
    ): stdClass {
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
            $requestData['headers']['seller_id'] = $transaction->sellerId();
            $requestData['headers']['x-qrcode-expiration-time'] = $_ENV['PIX_TIMEOUT'] ?? 1800;
        }

        try {
            $response = $this->httpClient->request('POST', $transactionUrl, $requestData);
            return json_decode($response->getBody()->getContents());
        } catch (ClientException $e) {
            $response = $e->getResponse();
            return json_decode($response->getBody()->getContents());
        } catch (Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

    public function processResponse(string $paymentId, string $type): stdClass
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
        try {
        $response = $this->httpClient->request('GET', $transactionUrl, $requestData);
            return json_decode($response->getBody()->getContents());
        } catch (Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

    public function getToken(
        Card $card,
        ?string $sellerId = null,
        ?string $customerId = null,
    ): Card {
        $this->authenticate();
        $requestHeaders = [
            'Authorization' => "Bearer {$this->accessToken}",
            'Content-Type' => 'application/json; charset=utf-8',
        ];

        if ($sellerId !== null) {
            $requestHeaders['seller_id'] = $sellerId;
        }

        $requestData = [
            'card_number' => $card->numberToken(),
        ];

        if ($customerId !== null) {
            $requestData['customer_id'] = $customerId;
        }

        try {
        $response = $this->httpClient->request(
            'POST',
            "{$this->getBaseUrl()}/v1/tokens/card",
            [
                'verify' => false,
                'headers' => $requestHeaders,
                'json' => $requestData,
            ],
        );

            $content = $response->getBody()->getContents();

            $card->updateNumberToken(json_decode($content, true)['number_token']);

            return $card;
        } catch (Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

    public function tokenizeCard(string $cardNumber, ?string $customerId = null): string
    {
        $this->authenticate();

        try {
            $requestData = [
                'headers' => [
                    'Authorization' => "Bearer {$this->accessToken}",
                    'Content-Type' => 'application/json; charset=utf-8',
                ],
                'json' => array_filter([
                    'card_number' => $cardNumber,
                    'customer_id' => $customerId,
                ]),
                'verify' => false
            ];

            $response = $this->httpClient->request(
                'POST',
                $this->baseUrl . '/v1/tokens/card',
                $requestData
            );

            $responseData = json_decode($response->getBody()->getContents(), true);

            if (!isset($responseData['number_token'])) {
                throw new Exception('Failed to generate card token. Response: ' . json_encode($responseData));
            }

            return $responseData['number_token'];
        } catch (Throwable $th) {
            throw new Exception('Card tokenization error: ' . $th->getMessage());
        }
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
}
