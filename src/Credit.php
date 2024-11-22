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
}
