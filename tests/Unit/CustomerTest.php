<?php

namespace Lucasnpinheiro\Getnet\Tests\Unit;


use Lucasnpinheiro\Getnet\Address;
use Lucasnpinheiro\Getnet\Customer;
use PHPUnit\Framework\TestCase;

class CustomerTest extends TestCase
{
    public function testConstruct()
    {
        $customerId = 'customer-id';
        $billingAddress = new Address(
            'street',
            'number',
            'complement',
            'district',
            'city',
            'state',
            'country',
            'postalCode'
        );
        $customer = new Customer($customerId, $billingAddress);

        $this->assertInstanceOf(Customer::class, $customer);
    }

    public function testJsonSerialize()
    {
        $customerId = 'customer-id';
        $billingAddress = new Address(
            'street',
            'number',
            'complement',
            'district',
            'city',
            'state',
            'country',
            'postalCode'
        );
        $firstName = 'first-name';
        $lastName = 'last-name';
        $name = 'name';
        $email = 'email@example.com';
        $documentType = 'document-type';
        $documentNumber = 'document-number';
        $phoneNumber = 'phone-number';

        $customer = new Customer(
            $customerId,
            $billingAddress,
            $firstName,
            $lastName,
            $name,
            $email,
            $documentType,
            $documentNumber,
            $phoneNumber
        );

        $expectedData = [
            'customer_id' => $customerId,
            'billing_address' => $billingAddress,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'name' => $name,
            'email' => $email,
            'document_type' => $documentType,
            'document_number' => $documentNumber,
            'phone_number' => $phoneNumber,
        ];

        $this->assertEquals($expectedData, $customer->jsonSerialize());
    }

    public function testJsonSerializeWithNullValues()
    {
        $customerId = 'customer-id';
        $billingAddress = new Address(
            'street',
            'number',
            'complement',
            'district',
            'city',
            'state',
            'country',
            'postalCode'
        );

        $customer = new Customer($customerId, $billingAddress);

        $expectedData = [
            'customer_id' => $customerId,
            'billing_address' => $billingAddress,
        ];

        $this->assertEquals($expectedData, $customer->jsonSerialize());
    }
}