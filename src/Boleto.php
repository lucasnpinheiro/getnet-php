<?php

declare(strict_types=1);

namespace Lucasnpinheiro\Getnet;

use JsonSerializable;

class Boleto implements JsonSerializable
{
    public function __construct(
        private string $documentNumber,
        private string $expirationDate,
        private string $instructions,
        private string $provider = 'santander',
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'document_number' => $this->documentNumber,
            'expiration_date' => $this->expirationDate,
            'instructions' => $this->instructions,
            'provider' => $this->provider,
        ];
    }

    public function documentNumber(): string
    {
        return $this->documentNumber;
    }

    public function expirationDate(): string
    {
        return $this->expirationDate;
    }

    public function instructions(): string
    {
        return $this->instructions;
    }

    public function provider(): string
    {
        return $this->provider;
    }
}
