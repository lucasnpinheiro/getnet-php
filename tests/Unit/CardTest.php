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
}