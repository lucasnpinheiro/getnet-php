<?php

declare(strict_types=1);

namespace Lucasnpinheiro\Getnet;

class TransactionPix extends Transaction
{
    public function __construct(
        private string $sellerId,
        private int $amount,
        private string $currency,
        private ?string $orderId = null,
        private ?string $customerId = null
    ) {
        parent::__construct($sellerId, $amount, $currency);
    }

    public function jsonSerialize(): array
    {
        $data = [
            'amount' => $this->amount,
            'currency' => $this->currency,
            'seller_id' => $this->sellerId()
        ];

        if ($this->orderId) {
            $data['order_id'] = $this->orderId;
        }
        if ($this->customerId) {
            $data['customer_id'] = $this->customerId;
        }

        return $data;
    }


    public function orderId(): ?string
    {
        return $this->orderId;
    }

    public function customerId(): ?string
    {
        return $this->customerId;
    }

    public function sellerId(): string
    {
        return $this->sellerId;
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function currency(): string
    {
        return $this->currency;
    }
}
