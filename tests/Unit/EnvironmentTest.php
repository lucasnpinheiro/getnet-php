<?php

namespace Lucasnpinheiro\Getnet\Tests\Unit;

use Lucasnpinheiro\Getnet\Environment;
use PHPUnit\Framework\TestCase;

class EnvironmentTest extends TestCase
{
    public function testEnvironmentValues()
    {
        $this->assertSame('sandbox', Environment::SANDBOX);
        $this->assertSame('homologation', Environment::HOMOLOGATION);
        $this->assertSame('production', Environment::PRODUCTION);
    }
}
