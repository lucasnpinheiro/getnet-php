<?php

declare(strict_types=1);

namespace Lucasnpinheiro\Getnet;

use JsonSerializable;

class Shippings implements JsonSerializable
{
    public function __construct(
        private string $firstName,
        private string $name,
        private string $email,
        private string $phoneNumber,
        private float $shippingAmount,
        private Address $address
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'first_name' => $this->firstName,
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phoneNumber,
            'shipping_amount' => $this->shippingAmount,
            'address' => $this->address,
        ];
    }
}
