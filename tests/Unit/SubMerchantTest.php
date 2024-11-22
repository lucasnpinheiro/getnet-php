<?php

namespace Lucasnpinheiro\Getnet\Tests\Unit;

use Lucasnpinheiro\Getnet\SubMerchant;
use PHPUnit\Framework\TestCase;

class SubMerchantTest extends TestCase
{
    public function testJsonSerialize()
    {
        $identificationCode = 'identification-code';
        $documentType = 'document-type';
        $documentNumber = 'document-number';
        $address = 'address';
        $city = 'city';
        $state = 'state';
        $postalCode = 'postal-code';

        $subMerchant = new SubMerchant(
            $identificationCode,
            $documentType,
            $documentNumber,
            $address,
            $city,
            $state,
            $postalCode
        );

        $expectedData = [
            'identification_code' => $identificationCode,
            'document_type' => $documentType,
            'document_number' => $documentNumber,
            'address' => $address,
            'city' => $city,
            'state' => $state,
            'postal_code' => $postalCode
        ];

        $this->assertEquals($expectedData, $subMerchant->jsonSerialize());
    }

    public function testJsonSerializeWithEmptyValues()
    {
        $subMerchant = new SubMerchant(
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        );

        $expectedData = [
            'identification_code' => '',
            'document_type' => '',
            'document_number' => '',
            'address' => '',
            'city' => '',
            'state' => '',
            'postal_code' => ''
        ];

        $this->assertEquals($expectedData, $subMerchant->jsonSerialize());
    }
}
