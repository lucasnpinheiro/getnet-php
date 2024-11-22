<?php

declare(strict_types=1);

namespace Lucasnpinheiro\Getnet;

class TransactionDebitCard extends Transaction
{
    public function __construct(
        private string $sellerId,
        private int $amount,
        private string $currency,
        private Order $order,
        private Customer $customer,
        private Debit $debit,
        private ?Device $device = null,
        private ?Shippings $shippings = null,
        private ?SubMerchant $subMerchant = null,
    ) {
        parent::__construct($sellerId, $amount, $currency);
    }

    public function jsonSerialize(): array
    {
        $data = array_merge(parent::jsonSerialize(), [
            'order' => $this->order,
            'customer' => $this->customer,
            'debit' => $this->debit,
        ]);

        $data += array_filter([
            'device' => $this->device,
            'shippings' => $this->shippings,
            'subMerchant' => $this->subMerchant,
        ]);

        return $data;
    }
}
