<?php

namespace Lucasnpinheiro\Getnet\Tests\Unit;

use Lucasnpinheiro\Getnet\Credit;
use Lucasnpinheiro\Getnet\Customer;
use Lucasnpinheiro\Getnet\Device;
use Lucasnpinheiro\Getnet\Order;
use Lucasnpinheiro\Getnet\Shippings;
use Lucasnpinheiro\Getnet\TransactionCreditCard;
use PHPUnit\Framework\TestCase;

class TransactionCreditCardTest extends TestCase
{
    public function testTransactionCardSerializesCorrectly()
    {
        $order = $this->createMock(Order::class);
        $customer = $this->createMock(Customer::class);
        $credit = $this->createMock(Credit::class);
        $device = $this->createMock(Device::class);
        $shippings = $this->createMock(Shippings::class);

        $transactionCard = new TransactionCreditCard(
            'seller123',
            1000,
            'BRL',
            $order,
            $customer,
            $credit,
            $device,
            $shippings
        );

        $expected = [
            'seller_id' => 'seller123',
            'amount' => 1000,
            'currency' => 'BRL',
            'order' => $order,
            'customer' => $customer,
            'credit' => $credit,
            'device' => $device,
            'shippings' => [$shippings],
        ];

        $this->assertEquals($expected, $transactionCard->jsonSerialize());
    }

    public function testTransactionCardSerializesWithoutOptionalFields()
    {
        $order = $this->createMock(Order::class);
        $customer = $this->createMock(Customer::class);
        $credit = $this->createMock(Credit::class);

        $transactionCard = new TransactionCreditCard('seller123', 1000, 'BRL', $order, $customer, $credit);

        $expected = [
            'seller_id' => 'seller123',
            'amount' => 1000,
            'currency' => 'BRL',
            'order' => $order,
            'customer' => $customer,
            'credit' => $credit,
        ];

        $this->assertEquals($expected, $transactionCard->jsonSerialize());
    }

    public function testTransactionCardHandlesNullDevice()
    {
        $order = $this->createMock(Order::class);
        $customer = $this->createMock(Customer::class);
        $credit = $this->createMock(Credit::class);
        $shippings = $this->createMock(Shippings::class);

        $transactionCard = new TransactionCreditCard('seller123', 1000, 'BRL', $order, $customer, $credit, null, $shippings);

        $expected = [
            'seller_id' => 'seller123',
            'amount' => 1000,
            'currency' => 'BRL',
            'order' => $order,
            'customer' => $customer,
            'credit' => $credit,
            'shippings' => [$shippings],
        ];

        $this->assertEquals($expected, $transactionCard->jsonSerialize());
    }

    public function testTransactionCardHandlesNullShippings()
    {
        $order = $this->createMock(Order::class);
        $customer = $this->createMock(Customer::class);
        $credit = $this->createMock(Credit::class);
        $device = $this->createMock(Device::class);

        $transactionCard = new TransactionCreditCard('seller123', 1000, 'BRL', $order, $customer, $credit, $device, null);

        $expected = [
            'seller_id' => 'seller123',
            'amount' => 1000,
            'currency' => 'BRL',
            'order' => $order,
            'customer' => $customer,
            'credit' => $credit,
            'device' => $device,
        ];

        $this->assertEquals($expected, $transactionCard->jsonSerialize());
    }
}
