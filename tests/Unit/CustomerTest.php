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

    public function testAddress()
    {
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
        $customer = new Customer('customer-id', $billingAddress);

        $this->assertSame($billingAddress, $customer->address());
    }

    public function testCustomerId()
    {
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
        $customer = new Customer('customer-id', $billingAddress);

        $this->assertEquals('customer-id', $customer->customerId());
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

    public function testJsonSerializeWithOptionalFields()
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
            $phoneNumber,
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

    /**
     * @dataProvider customerDataProvider
     */
    public function testJsonSerializeWithDataProvider(
        string $customerId,
        Address $billingAddress,
        ?string $firstName,
        ?string $lastName,
        ?string $name,
        ?string $email,
        ?string $documentType,
        ?string $documentNumber,
        ?string $phoneNumber,
        array $expected
    ) {
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

        $this->assertEquals($expected, $customer->jsonSerialize());
    }

    public function customerDataProvider(): array
    {
        $address1 = new Address(
            'Rua A',
            '123',
            'Apto 1',
            'Centro',
            'São Paulo',
            'SP',
            'Brasil',
            '01234-567'
        );

        $address2 = new Address(
            'Rua B',
            '456',
            null,
            'Jardins',
            'Rio de Janeiro',
            'RJ',
            'Brasil',
            '04567-890'
        );

        return [
            'complete_customer' => [
                'customer123',
                $address1,
                'John',
                'Doe',
                'John Doe',
                'john@example.com',
                'CPF',
                '12345678901',
                '11999887766',
                [
                    'customer_id' => 'customer123',
                    'billing_address' => $address1,
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'name' => 'John Doe',
                    'email' => 'john@example.com',
                    'document_type' => 'CPF',
                    'document_number' => '12345678901',
                    'phone_number' => '11999887766',
                ]
            ],
            'minimal_customer' => [
                'customer456',
                $address2,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                [
                    'customer_id' => 'customer456',
                    'billing_address' => $address2,
                ]
            ],
        ];
    }
}
