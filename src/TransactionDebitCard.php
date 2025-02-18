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

    public function order(): Order
    {
        return $this->order;
    }

    public function customer(): Customer
    {
        return $this->customer;
    }

    public function debit(): Debit
    {
        return $this->debit;
    }

    public function device(): ?Device
    {
        return $this->device;
    }

    public function shippings(): ?Shippings
    {
        return $this->shippings;
    }

    public function subMerchant(): ?SubMerchant
    {
        return $this->subMerchant;
    }

    public function sellerId(): string
    {
        return $this->sellerId;
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function currency(): string
    {
        return $this->currency;
    }
}
