<?php

namespace Lucasnpinheiro\Getnet\Tests\Unit;

use Lucasnpinheiro\Getnet\Address;
use Lucasnpinheiro\Getnet\Shippings;
use PHPUnit\Framework\TestCase;

class ShippingsTest extends TestCase
{
    public function testConstruct()
    {
        $firstName = 'first-name';
        $name = 'name';
        $email = 'email@example.com';
        $phoneNumber = 'phone-number';
        $shippingAmount = 10.99;
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

        $shippings = new Shippings(
            $firstName,
            $name,
            $email,
            $phoneNumber,
            $shippingAmount,
            $address
        );

        $this->assertInstanceOf(Shippings::class, $shippings);
    }

    public function testJsonSerialize()
    {
        $firstName = 'first-name';
        $name = 'name';
        $email = 'email@example.com';
        $phoneNumber = 'phone-number';
        $shippingAmount = 10.99;
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

        $shippings = new Shippings(
            $firstName,
            $name,
            $email,
            $phoneNumber,
            $shippingAmount,
            $address
        );

        $expectedData = [
            'first_name' => $firstName,
            'name' => $name,
            'email' => $email,
            'phone_number' => $phoneNumber,
            'shipping_amount' => $shippingAmount,
            'address' => $address,
        ];

        $this->assertEquals($expectedData, $shippings->jsonSerialize());
    }
}