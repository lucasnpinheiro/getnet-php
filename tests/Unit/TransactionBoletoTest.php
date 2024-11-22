<?php

namespace Lucasnpinheiro\Getnet\Tests\Unit;

use Lucasnpinheiro\Getnet\Address;
use Lucasnpinheiro\Getnet\Boleto;
use Lucasnpinheiro\Getnet\Customer;
use Lucasnpinheiro\Getnet\Order;
use Lucasnpinheiro\Getnet\TransactionBoleto;
use PHPUnit\Framework\TestCase;

class TransactionBoletoTest extends TestCase
{
    public function testConstruct()
    {
        $sellerId = 'seller_id';
        $amount = 10;
        $currency = 'BRL';

        $order = new Order('order-id', 10.99, 'product-type');
        $boleto = new Boleto('document_number', 'expiration_date', 'instructions');
        $customer = new Customer(
            'customer-id', new Address(
            'street',
            'number',
            'complement',
            'district',
            'city',
            'state',
            'country',
            'postalCode'
        )
        );

        $transactionBoleto = new TransactionBoleto($sellerId, $amount, $currency, $order, $boleto, $customer);

        $this->assertInstanceOf(TransactionBoleto::class, $transactionBoleto);
    }

    public function testJsonSerialize()
    {
        $sellerId = 'seller_id';
        $amount = 10;
        $currency = 'BRL';
        $order = new Order('order-id', 10.99, 'product-type');
        $boleto = new Boleto('document_number', 'expiration_date', 'instructions');
        $customer = new Customer(
            'customer-id', new Address(
            'street',
            'number',
            'complement',
            'district',
            'city',
            'state',
            'country',
            'postalCode'
        )
        );

        $transactionBoleto = new TransactionBoleto($sellerId, $amount, $currency, $order, $boleto, $customer);

        $expected = [
            'seller_id' => $sellerId,
            'amount' => $amount,
            'currency' => $currency,
            'order' => $order,
            'boleto' => $boleto,
            'customer' => $customer,
        ];

        $this->assertEquals($expected, $transactionBoleto->jsonSerialize());
    }
}