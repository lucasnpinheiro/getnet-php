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
}