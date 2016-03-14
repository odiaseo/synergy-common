<?php
/**
 * Created by PhpStorm.
 * User: peleodiase
 * Date: 14/03/16
 * Time: 14:29
 */

namespace SynergyCommonTest\Doctrine;

use SynergyCommon\Doctrine\Filter\SiteFilter;
use SynergyCommon\Entity\BaseSite;
use SynergyCommonTest\Bootstrap;
use Zend\Log\Logger;

/**
 * Class SiteFilterTest
 * @package SynergyCommonTest\Doctrine
 */
class SiteFilterTest extends \PHPUnit_Framework_TestCase
{

    public function testSiteFilter()
    {
        $filter = new SiteFilter(Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default'));
        $site   = new BaseSite();
        $filter->setSite($site);
        $filter->setLogger(new Logger());

        $this->assertEmpty($filter->getSiteFilterQuery('a', 'b'));
        $site->setId(1);
        $this->assertNotEmpty($filter->getSiteFilterQuery('a', 'b'));
    }
}
