<?php

declare(strict_types=1);

namespace Lucasnpinheiro\Getnet;

class TransactionCard extends Transaction
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
    )
    {
        parent::__construct($sellerId, $amount, $currency);

    }

    public function jsonSerialize(): array
    {
        $data = array_merge(parent::jsonSerialize(), [
            'order' => $this->order,
            'customer' => $this->customer,
            'credit' => $this->credit,
        ]);

        if ($this->device) {
            $data['device'] = $this->device;
        }
        if ($this->shippings) {
            $data['shippings'] = [$this->shippings];
        }

        return $data;
    }
}
