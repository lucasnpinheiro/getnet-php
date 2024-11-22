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
    )
    {
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
}
