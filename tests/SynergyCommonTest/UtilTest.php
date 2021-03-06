<?php

namespace SynergyCommonTest;

use SynergyCommon\Util;

/**
 * Class UtilTest
 *
 * @package SynergyCommonTest
 */
class UtilTest extends \PHPUnit\Framework\TestCase
{

    public function testCurrencyConversion()
    {
        $result = Util::currencyConverter(120, 'USD', 'GBP');
        $this->assertNotEquals(0, $result);
    }
}
