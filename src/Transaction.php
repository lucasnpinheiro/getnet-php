<?php

declare(strict_types=1);

namespace Lucasnpinheiro\Getnet;

use JsonSerializable;

class Transaction implements JsonSerializable
{
    public function __construct(
        private string $sellerId,
        private int $amount,
        private string $currency = "BRL"
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'seller_id' => $this->sellerId,
            'amount' => $this->amount,
            'currency' => $this->currency,
        ];
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
