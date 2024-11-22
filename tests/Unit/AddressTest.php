<?php

namespace Lucasnpinheiro\Getnet\Tests\Unit;

use Lucasnpinheiro\Getnet\Address;
use PHPUnit\Framework\TestCase;

class AddressTest extends TestCase
{
    public function testConstruct()
    {
        $address = new Address(
            'street',
            'number',
            'complement',
            'district',
            'city',
            'state',
            'country',
            'postalCode'
        );

        $this->assertInstanceOf(Address::class, $address);
    }

    public function testJsonSerialize()
    {
        $address = new Address(
            'street',
            'number',
            'complement',
            'district',
            'city',
            'state',
            'country',
            'postalCode'
        );

        $expected = [
            'street' => 'street',
            'number' => 'number',
            'complement' => 'complement',
            'district' => 'district',
            'city' => 'city',
            'state' => 'state',
            'country' => 'country',
            'postal_code' => 'postalCode',
        ];

        $this->assertEquals($expected, $address->jsonSerialize());
    }

    public function testJsonSerializeEmpty()
    {
        $address = new Address(
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        );

        $expected = [
            'street' => '',
            'number' => '',
            'complement' => '',
            'district' => '',
            'city' => '',
            'state' => '',
            'country' => '',
            'postal_code' => '',
        ];

        $this->assertEquals($expected, $address->jsonSerialize());
    }
}