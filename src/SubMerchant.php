<?php

declare(strict_types=1);

namespace Lucasnpinheiro\Getnet;

use JsonSerializable;

class SubMerchant implements JsonSerializable
{
    public function __construct(
        private string $identificationCode,
        private string $documentType,
        private string $documentNumber,
        private string $address,
        private string $city,
        private string $state,
        private string $postalCode
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'identification_code' => $this->identificationCode,
            'document_type' => $this->documentType,
            'document_number' => $this->documentNumber,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postalCode
        ];
    }

    public function identificationCode(): string
    {
        return $this->identificationCode;
    }

    public function documentType(): string
    {
        return $this->documentType;
    }

    public function documentNumber(): string
    {
        return $this->documentNumber;
    }

    public function address(): string
    {
        return $this->address;
    }

    public function city(): string
    {
        return $this->city;
    }

    public function state(): string
    {
        return $this->state;
    }

    public function postalCode(): string
    {
        return $this->postalCode;
    }


}
