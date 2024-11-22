<?php

namespace Lucasnpinheiro\Getnet\Tests\Unit;

use Lucasnpinheiro\Getnet\Order;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    public function testConstruct()
    {
        $orderId = 'order-id';
        $salesTax = 10.99;
        $productType = 'product-type';

        $order = new Order($orderId, $salesTax, $productType);

        $this->assertInstanceOf(Order::class, $order);
    }

    public function testJsonSerialize()
    {
        $orderId = 'order-id';
        $salesTax = 10.99;
        $productType = 'product-type';

        $order = new Order($orderId, $salesTax, $productType);

        $expected = [
            'order_id' => $orderId,
            'sales_tax' => $salesTax,
            'product_type' => $productType,
        ];

        $this->assertEquals($expected, $order->jsonSerialize());
    }

    public function testJsonSerializeWithoutSalesTax()
    {
        $orderId = 'order-id';
        $productType = 'product-type';

        $order = new Order($orderId, null, $productType);

        $expected = [
            'order_id' => $orderId,
            'product_type' => $productType,
        ];

        $this->assertEquals($expected, $order->jsonSerialize());
    }

    public function testJsonSerializeWithoutProductType()
    {
        $orderId = 'order-id';
        $salesTax = 10.99;

        $order = new Order($orderId, $salesTax);

        $expected = [
            'order_id' => $orderId,
            'sales_tax' => $salesTax,
        ];

        $this->assertEquals($expected, $order->jsonSerialize());
    }

    public function testJsonSerializeWithoutSalesTaxAndProductType()
    {
        $orderId = 'order-id';

        $order = new Order($orderId);

        $expected = [
            'order_id' => $orderId,
        ];

        $this->assertEquals($expected, $order->jsonSerialize());
    }
}