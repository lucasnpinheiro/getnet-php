<?php

namespace Lucasnpinheiro\Getnet\Tests\Unit;

use Lucasnpinheiro\Getnet\Card;
use PHPUnit\Framework\TestCase;

class CardTest extends TestCase
{
    public function testConstruct()
    {
        $card = new Card(
            'numberToken',
            'cardholderName',
            'securityCode',
            'expirationMonth',
            'expirationYear',
        );

        $this->assertInstanceOf(Card::class, $card);
    }

    public function testNumberToken()
    {
        $card = new Card(
            'numberToken',
            'cardholderName',
            'securityCode',
            'expirationMonth',
            'expirationYear'
        );

        $this->assertEquals('numberToken', $card->numberToken());
    }

    public function testUpdateNumberToken()
    {
        $card = new Card(
            'numberToken',
            'cardholderName',
            'securityCode',
            'expirationMonth',
            'expirationYear'
        );

        $card->updateNumberToken('newToken');
        $this->assertEquals('newToken', $card->numberToken());
    }

    public function testJsonSerializeWithoutBrand()
    {
        $card = new Card(
            'numberToken',
            'cardholderName',
            'securityCode',
            'expirationMonth',
            'expirationYear',
        );

        $expected = [
            'number_token' => 'numberToken',
            'cardholder_name' => 'cardholderName',
            'security_code' => 'securityCode',
            'expiration_month' => 'expirationMonth',
            'expiration_year' => 'expirationYear',
        ];

        $this->assertEquals($expected, $card->jsonSerialize());
    }

    public function testJsonSerializeWithBrand()
    {
        $card = new Card(
            'numberToken',
            'cardholderName',
            'securityCode',
            'expirationMonth',
            'expirationYear',
            'brand',
        );

        $expected = [
            'number_token' => 'numberToken',
            'cardholder_name' => 'cardholderName',
            'security_code' => 'securityCode',
            'expiration_month' => 'expirationMonth',
            'expiration_year' => 'expirationYear',
            'brand' => 'brand',
        ];

        $this->assertEquals($expected, $card->jsonSerialize());
    }

    /**
     * @dataProvider cardDataProvider
     */
    public function testJsonSerializeWithDataProvider(
        string $numberToken,
        string $cardholderName,
        string $securityCode,
        string $expirationMonth,
        string $expirationYear,
        ?string $brand,
        array $expected
    ) {
        $card = new Card(
            $numberToken,
            $cardholderName,
            $securityCode,
            $expirationMonth,
            $expirationYear,
            $brand
        );

        $this->assertEquals($expected, $card->jsonSerialize());
    }

    public function cardDataProvider(): array
    {
        return [
            'without_brand' => [
                'numberToken123',
                'John Doe',
                '123',
                '12',
                '2025',
                null,
                [
                    'number_token' => 'numberToken123',
                    'cardholder_name' => 'John Doe',
                    'security_code' => '123',
                    'expiration_month' => '12',
                    'expiration_year' => '2025',
                ]
            ],
            'with_brand' => [
                'numberToken456',
                'Jane Doe',
                '456',
                '06',
                '2024',
                'visa',
                [
                    'number_token' => 'numberToken456',
                    'cardholder_name' => 'Jane Doe',
                    'security_code' => '456',
                    'expiration_month' => '06',
                    'expiration_year' => '2024',
                    'brand' => 'visa',
                ]
            ]
        ];
    }
}
