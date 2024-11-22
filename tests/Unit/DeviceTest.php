<?php

namespace Lucasnpinheiro\Getnet\Tests\Unit;


use Lucasnpinheiro\Getnet\Device;
use PHPUnit\Framework\TestCase;

class DeviceTest extends TestCase
{
    public function testConstruct()
    {
        $ipAddress = '192.168.1.1';
        $deviceId = 'device-id';

        $device = new Device($ipAddress, $deviceId);

        $this->assertInstanceOf(Device::class, $device);
    }

    public function testJsonSerialize()
    {
        $ipAddress = '192.168.1.1';
        $deviceId = 'device-id';

        $device = new Device($ipAddress, $deviceId);

        $expected = [
            'ip_address' => $ipAddress,
            'device_id' => $deviceId,
        ];

        $this->assertEquals($expected, $device->jsonSerialize());
    }
}