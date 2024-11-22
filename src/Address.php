<?php

declare(strict_types=1);

namespace Lucasnpinheiro\Getnet;

use JsonSerializable;

class Address implements JsonSerializable
{
    public function __construct(
        private string $street,
        private string $number,
        private string $complement,
        private string $district,
        private string $city,
        private string $state,
        private string $country,
        private string $postalCode
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'street' => $this->street,
            'number' => $this->number,
            'complement' => $this->complement,
            'district' => $this->district,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'postal_code' => $this->postalCode
        ];
    }
}
