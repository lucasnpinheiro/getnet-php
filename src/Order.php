<?php

declare(strict_types=1);

namespace Lucasnpinheiro\Getnet;

use JsonSerializable;

class Order implements JsonSerializable
{
    public function __construct(
        private string $orderId,
        private ?float $salesTax = null,
        private ?string $productType = null
    ) {
    }

    public function jsonSerialize(): array
    {
        $order = [
            'order_id' => $this->orderId
        ];

        if ($this->salesTax) {
            $order['sales_tax'] = $this->salesTax;
        }
        if ($this->productType) {
            $order['product_type'] = $this->productType;
        }

        return $order;
    }

    public function orderId(): string
    {
        return $this->orderId;
    }

    public function salesTax(): ?float
    {
        return $this->salesTax;
    }

    public function productType(): ?string
    {
        return $this->productType;
    }
}
