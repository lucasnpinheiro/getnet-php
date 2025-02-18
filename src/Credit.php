<?php

declare(strict_types=1);

namespace Lucasnpinheiro\Getnet;

use JsonSerializable;

class Credit implements JsonSerializable
{
    public function __construct(
        private bool $delayed,
        private bool $preAuthorization,
        private bool $saveCardData,
        private string $transactionType,
        private int $numberInstallments,
        private Card $card,
        private ?string $softDescriptor = null,
        private ?int $dynamicMcc = null,
        private ?string $credentialsOnFileType = null,
        private ?string $transactionId = null,
    ) {
    }

    public function jsonSerialize(): array
    {
        $data = [
            'delayed' => $this->delayed,
            'pre_authorization' => $this->preAuthorization,
            'save_card_data' => $this->saveCardData,
            'transaction_type' => $this->transactionType,
            'number_installments' => $this->numberInstallments,
            'card' => $this->card,
        ];

        $data += array_filter([
            'soft_descriptor' => $this->softDescriptor,
            'dynamic_mcc' => $this->dynamicMcc,
            'credentials_on_file_type' => $this->credentialsOnFileType,
            'transaction_id' => $this->transactionId,
        ]);

        return $data;
    }

    public function delayed(): bool
    {
        return $this->delayed;
    }

    public function preAuthorization(): bool
    {
        return $this->preAuthorization;
    }

    public function saveCardData(): bool
    {
        return $this->saveCardData;
    }

    public function transactionType(): string
    {
        return $this->transactionType;
    }

    public function numberInstallments(): int
    {
        return $this->numberInstallments;
    }

    public function card(): Card
    {
        return $this->card;
    }

    public function softDescriptor(): ?string
    {
        return $this->softDescriptor;
    }

    public function dynamicMcc(): ?int
    {
        return $this->dynamicMcc;
    }

    public function credentialsOnFileType(): ?string
    {
        return $this->credentialsOnFileType;
    }

    public function transactionId(): ?string
    {
        return $this->transactionId;
    }

}
