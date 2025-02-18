<?php

declare(strict_types=1);

namespace Lucasnpinheiro\Getnet;

use JsonSerializable;

class Card implements JsonSerializable
{
    public function __construct(
        private string $numberToken,
        private string $cardholderName,
        private string $securityCode,
        private string $expirationMonth,
        private string $expirationYear,
        private ?string $brand = null,
    ) {
    }

    public function jsonSerialize(): array
    {
        $data = [
            'number_token' => $this->numberToken,
            'cardholder_name' => $this->cardholderName,
            'security_code' => $this->securityCode,
            'expiration_month' => $this->expirationMonth,
            'expiration_year' => $this->expirationYear,
        ];

        if ($this->brand !== null) {
            $data['brand'] = $this->brand;
        }

        return $data;
    }

    public function updateNumberToken(string $token): void
    {
        $this->numberToken = $token;
    }

    public function numberToken(): string
    {
        return $this->numberToken;
    }

    public function cardholderName(): string
    {
        return $this->cardholderName;
    }

    public function securityCode(): string
    {
        return $this->securityCode;
    }

    public function expirationMonth(): string
    {
        return $this->expirationMonth;
    }

    public function expirationYear(): string
    {
        return $this->expirationYear;
    }

    public function brand(): ?string
    {
        return $this->brand;
    }

}
