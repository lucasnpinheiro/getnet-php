<?php

namespace Lucasnpinheiro\Getnet\Tests\Unit;

use Lucasnpinheiro\Getnet\Transaction;
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase
{
    public function testConstruct()
    {
        $transaction = new Transaction('seller-id', 100, 'BRL');
        $this->assertInstanceOf(Transaction::class, $transaction);
    }

    public function testJsonSerialize()
    {
        $transaction = new Transaction('seller-id', 100, 'BRL');
        $expected = [
            'seller_id' => 'seller-id',
            'amount' => 100,
            'currency' => 'BRL',
        ];
        $this->assertEquals($expected, $transaction->jsonSerialize());
    }

    public function testJsonSerializeWithDefaultCurrency()
    {
        $transaction = new Transaction('seller-id', 100);
        $expected = [
            'seller_id' => 'seller-id',
            'amount' => 100,
            'currency' => 'BRL',
        ];
        $this->assertEquals($expected, $transaction->jsonSerialize());
    }
}
