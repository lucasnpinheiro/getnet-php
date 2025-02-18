<?php

declare(strict_types=1);

namespace Lucasnpinheiro\Getnet;

use JsonSerializable;

class Customer implements JsonSerializable
{
    public function __construct(
        private string $customerId,
        private Address $billingAddress,
        private ?string $firstName = null,
        private ?string $lastName = null,
        private ?string $name = null,
        private ?string $email = null,
        private ?string $documentType = null,
        private ?string $documentNumber = null,
        private ?string $phoneNumber = null
    ) {
    }

    public function jsonSerialize(): array
    {
        $data = [
            'customer_id' => $this->customerId,
            'billing_address' => $this->billingAddress,
        ];

        $data += array_filter([
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'name' => $this->name,
            'email' => $this->email,
            'document_type' => $this->documentType,
            'document_number' => $this->documentNumber,
            'phone_number' => $this->phoneNumber,
        ]);

        return $data;
    }

    public function customerId(): string
    {
        return $this->customerId;
    }

    public function address(): Address
    {
        return $this->billingAddress;
    }

    public function firstName(): ?string
    {
        return $this->firstName;
    }

    public function lastName(): ?string
    {
        return $this->lastName;
    }

    public function name(): ?string
    {
        return $this->name;
    }

    public function email(): ?string
    {
        return $this->email;
    }

    public function documentType(): ?string
    {
        return $this->documentType;
    }

    public function documentNumber(): ?string
    {
        return $this->documentNumber;
    }

    public function phoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

}
