<?php
namespace SynergyCommonTest\Lib;

use SynergyCommonTest\Bootstrap;

/**
 * Class CommonLibraryTest
 *
 * @package SynergyCommonTest\Lib
 */
class CommonLibraryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Zend\ServiceManager\ServiceManager */
    protected $serviceManager;

    public function setUp()
    {
        parent::setUp();
        $this->serviceManager = Bootstrap::getServiceManager();
    }

    public function testLoggerFactory()
    {
        /** @var $logger  \SynergyCommon\Util\ErrorHandler */
        $logger = $this->serviceManager->get('logger');

        $this->assertInstanceOf('SynergyCommon\Util\ErrorHandler', $logger);
        $adapter = $logger->getLogger();

        if (class_exists('Monolog\Logger')) {
            $this->assertInstanceOf('Monolog\Logger', $adapter);
        } else {
            $this->assertInstanceOf('Zend\Log\Logger', $adapter);
        }
    }

    public function testSessionManager()
    {
        $service = $this->serviceManager->get('session_manager');
        $this->assertInstanceOf('Zend\Session\SessionManager', $service);

    }

    public function testApiService()
    {
        $service = $this->serviceManager->get('synergycommon\service\api');
        $this->assertInstanceOf('SynergyCommon\Service\BaseApiService', $service);
    }

    public function testFlashMessaager()
    {
        $service = $this->serviceManager->get('viewhelpermanager')->get('flashMessages');
        $this->assertInstanceOf('SynergyCommon\View\Helper\FlashMessages', $service);
    }

    /**
     * @dataProvider provider
     *
     * @param $alias
     */
    public function testCacheInstances($alias)
    {
        $cache = $this->serviceManager->get('doctrine.cache.' . $alias);
        $this->assertInstanceOf('Doctrine\Common\Cache\CacheProvider', $cache);
    }

    public function testCacheStatus()
    {
        $model = $this->serviceManager->get('synergy\cache\status');
        $this->assertInstanceOf('stdClass', $model);
        $this->assertFalse($model->enabled);
    }

    public function provider()
    {
        return array(
            array('synergy_apc'),
            array('synergy_memcache'),
            array('cache\factory'),
            array('result\cache\factory'),
        );
    }
}
