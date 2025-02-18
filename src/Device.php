<?php

declare(strict_types=1);

namespace Lucasnpinheiro\Getnet;

use JsonSerializable;

class Device implements JsonSerializable
{
    public function __construct(
        private string $ipAddress,
        private string $deviceId,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'ip_address' => $this->ipAddress,
            'device_id' => $this->deviceId,
        ];
    }

    public function ipAddress(): string
    {
        return $this->ipAddress;
    }

    public function deviceId(): string
    {
        return $this->deviceId;
    }
}
