<?php

namespace Lucasnpinheiro\Getnet\Tests\Unit;


use Lucasnpinheiro\Getnet\Customer;
use Lucasnpinheiro\Getnet\Debit;
use Lucasnpinheiro\Getnet\Device;
use Lucasnpinheiro\Getnet\Order;
use Lucasnpinheiro\Getnet\Shippings;
use Lucasnpinheiro\Getnet\SubMerchant;
use Lucasnpinheiro\Getnet\TransactionDebitCard;
use PHPUnit\Framework\TestCase;

class TransactionDebitCardTest extends TestCase
{
    public function testConstructor(): void
    {
        $sellerId = 'seller-id';
        $amount = 100;
        $currency = 'BRL';
        $order = $this->createMock(Order::class);
        $customer = $this->createMock(Customer::class);
        $debit = $this->createMock(Debit::class);
        $device = $this->createMock(Device::class);
        $shippings =$this->createMock(Shippings::class);
        $subMerchant = $this->createMock(SubMerchant::class);

        $transaction = new TransactionDebitCard(
            $sellerId,
            $amount,
            $currency,
            $order,
            $customer,
            $debit,
            $device,
            $shippings,
            $subMerchant,
        );

        $this->assertInstanceOf(TransactionDebitCard::class, $transaction);
    }

    public function testJsonSerialize(): void
    {
        $sellerId = 'seller-id';
        $amount = 100;
        $currency = 'BRL';
        $order = $this->createMock(Order::class);
        $customer = $this->createMock(Customer::class);
        $debit = $this->createMock(Debit::class);
        $device = $this->createMock(Device::class);
        $shippings =$this->createMock(Shippings::class);
        $subMerchant = $this->createMock(SubMerchant::class);

        $transaction = new TransactionDebitCard(
            $sellerId,
            $amount,
            $currency,
            $order,
            $customer,
            $debit,
            $device,
            $shippings,
            $subMerchant,
        );

        $expected = [
            'seller_id' => $sellerId,
            'amount' => $amount,
            'currency' => $currency,
            'order' => $order,
            'customer' => $customer,
            'debit' => $debit,
            'device' => $device,
            'shippings' => $shippings,
            'subMerchant' => $subMerchant,
        ];

        $this->assertEquals($expected, $transaction->jsonSerialize());
    }

    public function testJsonSerializeWithoutOptionalFields(): void
    {
        $sellerId = 'seller-id';
        $amount = 100;
        $currency = 'BRL';
        $order = $this->createMock(Order::class);
        $customer = $this->createMock(Customer::class);
        $debit = $this->createMock(Debit::class);

        $transaction = new TransactionDebitCard(
            $sellerId,
            $amount,
            $currency,
            $order,
            $customer,
            $debit,
        );

        $expected = [
            'seller_id' => $sellerId,
            'amount' => $amount,
            'currency' => $currency,
            'order' => $order,
            'customer' => $customer,
            'debit' => $debit,
        ];

        $this->assertEquals($expected, $transaction->jsonSerialize());
    }
}