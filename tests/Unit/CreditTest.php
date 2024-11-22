<?php

namespace Lucasnpinheiro\Getnet\Tests\Unit;

use Lucasnpinheiro\Getnet\Card;
use Lucasnpinheiro\Getnet\Credit;
use PHPUnit\Framework\TestCase;

class CreditTest extends TestCase
{
    public function testJsonSerializeEmpty()
    {
        $card = new Card(
            numberToken: 'numberToken',
            cardholderName: 'cardholderName',
            securityCode: 'securityCode',
            expirationMonth: 'expirationMonth',
            expirationYear: 'expirationYear',
        );
        $credit = new Credit(
            delayed: false,
            preAuthorization: false,
            saveCardData: false,
            transactionType: 'type',
            numberInstallments: 1,
            card: $card,
        );

        $expected = [
            'delayed' => false,
            'pre_authorization' => false,
            'save_card_data' => false,
            'transaction_type' => 'type',
            'number_installments' => 1,
            'card' => $card,
        ];

        $this->assertEquals($expected, $credit->jsonSerialize());
    }

    public function testJsonSerialize()
    {
        $card = new Card(
            numberToken: 'numberToken',
            cardholderName: 'cardholderName',
            securityCode: 'securityCode',
            expirationMonth: 'expirationMonth',
            expirationYear: 'expirationYear',
        );
        $credit = new Credit(
            delayed: true,
            preAuthorization: true,
            saveCardData: true,
            transactionType: 'type',
            numberInstallments: 1,
            card: $card,
            softDescriptor: 'descriptor',
            dynamicMcc: 123,
            credentialsOnFileType: 'file_type',
            transactionId: 'transaction_id',
        );

        $expected = [
            'delayed' => true,
            'pre_authorization' => true,
            'save_card_data' => true,
            'transaction_type' => 'type',
            'number_installments' => 1,
            'card' => $card,
            'soft_descriptor' => 'descriptor',
            'dynamic_mcc' => 123,
            'credentials_on_file_type' => 'file_type',
            'transaction_id' => 'transaction_id',
        ];

        $this->assertEquals($expected, $credit->jsonSerialize());
    }
}