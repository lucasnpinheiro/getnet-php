<?php

namespace Lucasnpinheiro\Getnet\Tests\Unit;

use Lucasnpinheiro\Getnet\Environment;
use Lucasnpinheiro\Getnet\Type;
use PHPUnit\Framework\TestCase;

class TypeTest extends TestCase
{
    public function testTypeValues()
    {
        $this->assertSame('PIX', Type::PIX);
        $this->assertSame('CREDIT', Type::CREDIT);
        $this->assertSame('DEBIT', Type::DEBIT);
    }
}
