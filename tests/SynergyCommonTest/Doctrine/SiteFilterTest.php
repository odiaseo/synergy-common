<?php
/**
 * Created by PhpStorm.
 * User: peleodiase
 * Date: 14/03/16
 * Time: 14:29
 */

namespace SynergyCommonTest\Doctrine;

use SynergyCommonTest\Stub\SiteStub;
use SynergyCommon\Doctrine\Filter\SiteFilter;
use SynergyCommonTest\Bootstrap;
use Laminas\Log\Logger;

/**
 * Class SiteFilterTest
 * @package SynergyCommonTest\Doctrine
 */
class SiteFilterTest extends \PHPUnit\Framework\TestCase
{

    public function testSiteFilter()
    {
        $filter = new SiteFilter(Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default'));
        $site   = new SiteStub();
        $filter->setSite($site);
        $filter->setLogger(new Logger());

        $this->assertEmpty($filter->getSiteFilterQuery('a', 'b'));
        $site->setId(1);
        $this->assertNotEmpty($filter->getSiteFilterQuery('a', 'b'));
    }
}
