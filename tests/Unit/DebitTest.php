<?php

namespace Lucasnpinheiro\Getnet\Tests\Unit;

use Lucasnpinheiro\Getnet\Card;
use Lucasnpinheiro\Getnet\Debit;
use PHPUnit\Framework\TestCase;

class DebitTest extends TestCase
{
    public function testJsonSerialize(): void
    {
        $card = new Card(
            numberToken: 'numberToken',
            cardholderName: 'cardholderName',
            securityCode: 'securityCode',
            expirationMonth: 'expirationMonth',
            expirationYear: 'expirationYear',
        );

        $debit = new Debit(
            cardholderMobile: 'cardholderMobile',
            dynamicMcc: 123,
            authenticated: true,
            card: $card,
            credentialsOnFileType: 'ONE_CLICK',
            transactionId: 'transactionId',
            softDescriptor: 'softDescriptor',
        );

        $expected = [
            'cardholder_mobile' => 'cardholderMobile',
            'dynamic_mcc' => 123,
            'authenticated' => true,
            'card' => [
                'number_token' => 'numberToken',
                'cardholder_name' => 'cardholderName',
                'security_code' => 'securityCode',
                'expiration_month' => 'expirationMonth',
                'expiration_year' => 'expirationYear',
            ],
            'credentials_on_file_type' => 'ONE_CLICK',
            'transaction_id' => 'transactionId',
            'soft_descriptor' => 'softDescriptor',
        ];

        $this->assertEquals($expected, $debit->jsonSerialize());
    }

    public function testJsonSerializeWithoutOptionalFields(): void
    {
        $card = new Card(
            numberToken: 'numberToken',
            cardholderName: 'cardholderName',
            securityCode: '123',
            expirationMonth: '12',
            expirationYear: '24',
        );

        $debit = new Debit(
            cardholderMobile: 'cardholderMobile',
            dynamicMcc: 123,
            authenticated: true,
            card: $card,
            credentialsOnFileType: 'ONE_CLICK',
        );

        $expected = [
            'cardholder_mobile' => 'cardholderMobile',
            'dynamic_mcc' => 123,
            'authenticated' => true,
            'card' => [
                'number_token' => 'numberToken',
                'cardholder_name' => 'cardholderName',
                'security_code' => '123',
                'expiration_month' => '12',
                'expiration_year' => '24',
            ],
            'credentials_on_file_type' => 'ONE_CLICK',
        ];

        $this->assertEquals($expected, $debit->jsonSerialize());
    }
}
