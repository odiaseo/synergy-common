<?php

namespace SynergyCommonTest\Doctrine;

use SynergyCommon\Doctrine\QueryCacheProfile;

/**
 * Class QueryCacheProfileTest
 *
 * @package SynergyCommonTest
 */
class QueryCacheProfileTest extends \PHPUnit\Framework\TestCase
{

    public function testCacheKeyGeneration()
    {
        $profile = new QueryCacheProfile(10, 'test');
        $result  = $profile->generateCacheKeys('sql', [], []);

        $this->assertTrue(is_array($result));
    }

    public function testCacheKeyGenerationNoKey()
    {
        $profile = new QueryCacheProfile(10);
        $result  = $profile->generateCacheKeys('sql', [], []);
        $this->assertTrue(is_array($result));
    }
}
