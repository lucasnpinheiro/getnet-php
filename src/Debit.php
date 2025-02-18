<?php

declare(strict_types=1);

namespace Lucasnpinheiro\Getnet;

use JsonSerializable;

class Debit implements JsonSerializable
{
    public function __construct(
        private string $cardholderMobile,
        private int $dynamicMcc,
        private bool $authenticated = false,
        private Card $card,
        private string $credentialsOnFileType = 'ONE_CLICK',
        private ?string $transactionId = null,
        private ?string $softDescriptor = null,
    ) {
    }

    public function jsonSerialize(): array
    {
        $data = [
            'cardholder_mobile' => $this->cardholderMobile,
            'dynamic_mcc' => $this->dynamicMcc,
            'authenticated' => $this->authenticated,
            'card' => $this->card->jsonSerialize(),
            'credentials_on_file_type' => $this->credentialsOnFileType,
        ];

        $data += array_filter([
            'transaction_id' => $this->transactionId,
            'soft_descriptor' => $this->softDescriptor,
        ]);

        return $data;
    }

    public function cardholderMobile(): string
    {
        return $this->cardholderMobile;
    }

    public function dynamicMcc(): int
    {
        return $this->dynamicMcc;
    }

    public function authenticated(): bool
    {
        return $this->authenticated;
    }

    public function card(): Card
    {
        return $this->card;
    }

    public function credentialsOnFileType(): string
    {
        return $this->credentialsOnFileType;
    }

    public function transactionId(): ?string
    {
        return $this->transactionId;
    }

    public function softDescriptor(): ?string
    {
        return $this->softDescriptor;
    }
}
