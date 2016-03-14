<?php

namespace SynergyCommonTest\Doctrine;

use SynergyCommon\Doctrine\QueryCacheProfile;

/**
 * Class QueryCacheProfileTest
 * @package SynergyCommonTest
 */
class QueryCacheProfileTest extends \PHPUnit_Framework_TestCase
{

    public function testCacheKeyGeneration()
    {
        $profile  = new QueryCacheProfile(10, 'test');
        $result   = $profile->generateCacheKeys('sql', [], []);
        $expected = hash('sha512', 'sql' . "-" . serialize([]) . "-" . serialize([]));

        $this->assertTrue(is_array($result));
        list($cacheKey, $resultKey) = $result;

        $this->assertSame($expected, $resultKey);
        $this->assertSame('test', $cacheKey);
    }

    public function testCacheKeyGenerationNoKey()
    {
        $profile  = new QueryCacheProfile(10);
        $result   = $profile->generateCacheKeys('sql', [], []);
        $expected = hash('sha512', 'sql' . "-" . serialize([]) . "-" . serialize([]));

        $this->assertTrue(is_array($result));
        list($cacheKey, $resultKey) = $result;

        $this->assertSame($expected, $resultKey);
        $this->assertSame(sha1($expected), $cacheKey);
    }
}
