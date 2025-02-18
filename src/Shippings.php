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

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function phoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function shippingAmount(): float
    {
        return $this->shippingAmount;
    }

    public function address(): Address
    {
        return $this->address;
    }

}
