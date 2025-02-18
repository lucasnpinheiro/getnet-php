<?php

declare(strict_types=1);

namespace Lucasnpinheiro\Getnet;

class TransactionBoleto extends Transaction
{
    public function __construct(
        private string $sellerId,
        private int $amount,
        private string $currency,
        private Order $order,
        private Boleto $boleto,
        private Customer $customer
    ) {
        parent::__construct($sellerId, $amount, $currency);
    }

    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'order' => $this->order,
            'boleto' => $this->boleto,
            'customer' => $this->customer
        ]);
    }

    public function order(): Order
    {
        return $this->order;
    }

    public function boleto(): Boleto
    {
        return $this->boleto;
    }

    public function customer(): Customer
    {
        return $this->customer;
    }
}
