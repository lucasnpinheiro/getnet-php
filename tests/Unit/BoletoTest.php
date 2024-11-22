<?php

namespace Lucasnpinheiro\Getnet\Tests\Unit;

use Lucasnpinheiro\Getnet\Boleto;
use PHPUnit\Framework\TestCase;

class BoletoTest extends TestCase
{
    public function testConstruct()
    {
        $boleto = new Boleto(
            'document_number',
            'expiration_date',
            'instructions',
            'provider'
        );

        $this->assertInstanceOf(Boleto::class, $boleto);
    }

    public function testJsonSerialize()
    {
        $boleto = new Boleto(
            'document_number',
            'expiration_date',
            'instructions',
            'provider'
        );

        $expected = [
            'document_number' => 'document_number',
            'expiration_date' => 'expiration_date',
            'instructions' => 'instructions',
            'provider' => 'provider',
        ];

        $this->assertEquals($expected, $boleto->jsonSerialize());
    }

    public function testJsonSerializeWithDefaultProvider()
    {
        $boleto = new Boleto(
            'document_number',
            'expiration_date',
            'instructions'
        );

        $expected = [
            'document_number' => 'document_number',
            'expiration_date' => 'expiration_date',
            'instructions' => 'instructions',
            'provider' => 'santander',
        ];

        $this->assertEquals($expected, $boleto->jsonSerialize());
    }
}
