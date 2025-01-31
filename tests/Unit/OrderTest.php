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

    public function testOrderId()
    {
        $order = new Order('test-order-123');
        $this->assertEquals('test-order-123', $order->orderId());
    }

    /**
     * @dataProvider orderDataProvider
     */
    public function testJsonSerializeWithDataProvider(
        string $orderId,
        ?float $salesTax,
        ?string $productType,
        array $expected
    ) {
        $order = new Order($orderId, $salesTax, $productType);
        $this->assertEquals($expected, $order->jsonSerialize());
    }

    public function orderDataProvider(): array
    {
        return [
            'complete_order' => [
                'order123',
                15.99,
                'physical',
                [
                    'order_id' => 'order123',
                    'sales_tax' => 15.99,
                    'product_type' => 'physical'
                ]
            ],
            'order_without_sales_tax' => [
                'order456',
                null,
                'digital',
                [
                    'order_id' => 'order456',
                    'product_type' => 'digital'
                ]
            ],
            'order_without_product_type' => [
                'order789',
                25.50,
                null,
                [
                    'order_id' => 'order789',
                    'sales_tax' => 25.50
                ]
            ],
            'minimal_order' => [
                'order000',
                null,
                null,
                [
                    'order_id' => 'order000'
                ]
            ]
        ];
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
