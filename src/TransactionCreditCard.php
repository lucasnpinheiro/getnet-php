<?php

declare(strict_types=1);

namespace Lucasnpinheiro\Getnet;

class TransactionCreditCard extends Transaction
{
    public function __construct(
        private string $sellerId,
        private int $amount,
        private string $currency,
        private Order $order,
        private Customer $customer,
        private Credit $credit,
        private ?Device $device = null,
        private ?Shippings $shippings = null
    ) {
        parent::__construct($sellerId, $amount, $currency);
    }

    public function jsonSerialize(): array
    {
        $data = array_merge(parent::jsonSerialize(), [
            'order' => $this->order,
            'customer' => $this->customer,
            'credit' => $this->credit,
        ]);

        if (!empty($this->device)) {
            $data['device'] = $this->device;
        }

        if (!empty($this->shippings)) {
            $data['shippings'] = [$this->shippings];
        }

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

    public function credit(): Credit
    {
        return $this->credit;
    }

    public function device(): ?Device
    {
        return $this->device;
    }

    public function shippings(): ?Shippings
    {
        return $this->shippings;
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
