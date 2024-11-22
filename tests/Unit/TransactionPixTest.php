<?php

namespace Lucasnpinheiro\Getnet\Tests\Unit;

use Lucasnpinheiro\Getnet\TransactionPix;
use PHPUnit\Framework\TestCase;

class TransactionPixTest extends TestCase
{
    public function testTransactionPixSerializesCorrectly()
    {
        $transactionPix = new TransactionPix('seller123', 1000, 'BRL', 'order123', 'customer123');

        $expected = [
            'amount' => 1000,
            'currency' => 'BRL',
            'seller_id' => 'seller123',
            'order_id' => 'order123',
            'customer_id' => 'customer123',
        ];

        $this->assertEquals($expected, $transactionPix->jsonSerialize());
    }

    public function testTransactionPixSerializesWithoutOptionalFields()
    {
        $transactionPix = new TransactionPix('seller123', 1000, 'BRL');

        $expected = [
            'amount' => 1000,
            'currency' => 'BRL',
            'seller_id' => 'seller123',
        ];

        $this->assertEquals($expected, $transactionPix->jsonSerialize());
    }

    public function testTransactionPixHandlesNullOrderId()
    {
        $transactionPix = new TransactionPix('seller123', 1000, 'BRL', null, 'customer123');

        $expected = [
            'amount' => 1000,
            'currency' => 'BRL',
            'seller_id' => 'seller123',
            'customer_id' => 'customer123',

        ];

        $this->assertEquals($expected, $transactionPix->jsonSerialize());
    }

    public function testTransactionPixHandlesNullCustomerId()
    {
        $transactionPix = new TransactionPix('seller123', 1000, 'BRL', 'order123', null);

        $expected = [
            'amount' => 1000,
            'currency' => 'BRL',
            'seller_id' => 'seller123',
            'order_id' => 'order123',
        ];

        $this->assertEquals($expected, $transactionPix->jsonSerialize());
        $this->assertEquals('seller123', $transactionPix->getSellerId());
    }
}
